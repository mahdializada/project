<?php

namespace App\Repositories\SingleSales\QuantityReservation;

use App\Events\SendNotification;
use App\Events\Updated;
use App\Models\DesignRequest;
use App\Models\DesignRequestHistory;
use App\Models\DesignRequestOrder;
use App\Models\DesignRequestOrderUser;
use App\Models\DesignRequestReject;
use App\Models\DesignRequestRejectReason;
use App\Models\Notification;
use App\Models\Reason;
use App\Models\SingleSales\QuantityReservationRequest;
use App\Models\SingleSales\SourcingRequest;
use App\Models\StatusTimeConsumed;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use  \App\Repositories\Repository;

class QuantityReservationRepository extends Repository
{
    public function paginate(Request $request): JsonResponse
    {
        $key = $request->query->get("key");
        $queryBuilder = new UriQueryBuilder(new QuantityReservationRequest(), $request);
        $queryBuilder->setRelations($this->getRelations());
        $searchInColumns = [
            'code',
            'whereHasOne,receivingCountry,name',
            'whereHasMany,products,name',
            'whereHasOne,products,quantity',
            'shipping_method',
            'whereHasOne,products,whereHasOne,purchaseds,arrival_time',
            'purchase_order_number',
            'order_note',
            'status'
        ];

        $queryBuilder->query = $this->filterRecords($queryBuilder->query, $request, $searchInColumns);
        //
        $statusQuery = clone $queryBuilder->query;
        $queryBuilder->query = $this->fetchDataAccordingToStatus($queryBuilder->query, $key);

        $queryBuilder->query->orderBy("created_at", 'desc');
        $designRequest = $queryBuilder->build()->paginate()->getData();
        $extraData = [
            'allTotal',
            'removedTotal',
            'pendingTotal, status,pending',
            'rejectedTotal, status, rejected',
            'inProcessTotal, status, in process',
            'notPossibleTotal, status, not possible',
            'orderMadeTotal, status, order made',
            'orderReceivedTotal, status, order received',
            'cancelledTotal, status, cancelled',
        ];
        $designRequest = $this->getStatusCount($statusQuery, $designRequest, $extraData);
        return response()->json($designRequest);
    }

    public function show(Request $request)
    {
        if ($request->query->has('code')) {
            $query =  QuantityReservationRequest::with($this->getRelations())
                ->where('code', $request->code);
            if ($request->key == "removed") {
                $query = $query->withTrashed();
            }
            $sourcer = $query->first();
            if ($sourcer) {
                return response()->json($sourcer, Response::HTTP_OK);
            } else {
                return response()->json(["message" => "not found"], Response::HTTP_NOT_FOUND);
            }
        }
        return response()->json(["message" => "not found"], Response::HTTP_NOT_FOUND);
    }



    public function store(Request $request)
    {
        $result = DB::transaction(function () use ($request) {
            $q= [
                'products_note'=>$request->get('products_note'),
                'shipping_method'=>$request->get('shipping_method'),
                'shipping_note'=>$request->get('shipping_note'),
                'importing_state_id'=>$request->get('importing_state_id'),
                'code'=>$this->randomSourcingCode(),
                'status'=>'pending'
            ];

            $quantityReservation = QuantityReservationRequest::create($q);
            $products = $request->get('product_id');
            $quantities = $request->get('quantity');
            $statues = $request->get("status");
            // new coding
            $data =[
                            'product_id'=>$products,
                            'quantity'=>$quantities,
                            'status'=>'pending',
                            'quantity_reservation_id'=>$quantityReservation->id
                    ];
                DB::table("quantity_reservation_products")->insert($data);


                        // --------------------insertion  pdm_purchased_products_info ---------------


            // $data1 =[
            //     'order_no'=>$request->get('purchase_order_number'),
            //     'arrival_time'=>$request->get('arrival_time'),
            //     'product_requests_id'=>$quantityReservation->id,

            // ];
            //     DB::table("pdm_purchased_products_info")->insert($data1);

                    // ------------------------------- end of this

            // for($i = 0; $i < count($products);$i++){
            //     $data =[
            //             'product_id'=>$products[$i],
            //             'quantity'=>$quantities[$i],
            //             'status'=>$statues[$i],
            //             'quantity_reservation_id'=>$quantityReservation->id
            //     ];
            //     DB::table("quantity_reservation_products")->insert($data);
            // }

            return $quantityReservation;
        });

        return $this->successResponse($result->load($this->getRelations()), Response::HTTP_CREATED);
    }

    private function randomSourcingCode(): int
    {
        return rand(10000, 8999999999);
    }


    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            $quantityReservation = QuantityReservationRequest::findOrFail($request->get('id'));
            $q= [
                'products_note'=>$request->get('products_note'),
                'shipping_method'=>$request->get('shipping_method'),
                'shipping_note'=>$request->get('shipping_note'),
                'importing_state_id'=>$request->get('importing_state_id'),
                'code'=>$this->randomSourcingCode(),
                'status'=>'pending'
            ];
            $quantityReservation->update($q);

            DB::table('quantity_reservation_products')->where("quantity_reservation_id",$quantityReservation->id)->delete();

            $products = $request->get('product_id');
            $quantities = $request->get('quantity');
            $statues = $request->get("status");

            for($i = 0; $i < count($products);$i++){
                $data =[
                        'product_id'=>$products[$i],
                        'quantity'=>$quantities[$i],
                        'status'=>$statues[$i],
                        'quantity_reservation_id'=>$quantityReservation->id
                ];
                DB::table("quantity_reservation_products")->insert($data);
            }


            $updated_items = $quantityReservation->load($this->getRelations());
            DB::commit();
            return response()->json($updated_items);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 500);
        }

    }


    public function storeRules(): array
    {
        return [
            'product_id' => ['required'],
            'receiving_country_id' => ['required'],
            'shipping_method' => ['required'],
            'quantity' => ['required'],
        ];
    }


    public function updateRules(Request $request)
    {
        $rules = [
            "id" => ['required', 'exists:quantity_reservation_requests_ssp,id'],
            'receiving_country_id' => ['required'],
            'shipping_method' => ['required'],
            'arrival_time' => ['required'],
            'purchase_order_number' => ['required'],
        ];

        $request->validate($rules);
    }

    public function destroy($ids, $reason_ids)
    {

        try {
            //code...
            DB::beginTransaction();
            $quantityReservationis = QuantityReservationRequest::withTrashed()->whereIn('id', $ids)->get();
            $reasons = Reason::whereIn('id', $reason_ids)->get();
            // ADD REASON
            foreach ($quantityReservationis as $quantityReservationi) {
                if (!$quantityReservationi->trashed()) {
                    foreach ($reasons as $reason) {
                        $quantityReservationi->reasons()->save($reason);
                    }
                    $quantityReservationi->reasons()->syncWithoutDetaching($reasons);
                    DB::table('quantity_reservation_products')->where("quantity_reservation_id",$quantityReservationi->id)->delete();
                    $quantityReservationi->delete();
                } else {
                    $quantityReservationi->reasons()->detach();
                    $quantityReservationi->forceDelete();
                }
            }
            DB::commit();

            return response()->json(['result' => true], Response::HTTP_OK);
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return $th;
        }

    }

    private function getRelations(): array
    {
        return [
            'receivingCountry:id,name',
            'products:id,name,code',
            'Info:arrival_time,order_no',
        ];
    }


    private function statusValidations($request)
    {
        $request->validate([
            'type' => ['required', 'string'],
            'status' => ['string'],
            'item_ids' => ['required'],
            'reasons' => ['required_if:type,hasReason', 'array'],
        ]);
    }

    public function changeQuantityStatus(Request $request)
    {
        $this->statusValidations($request);
        try {
            DB::beginTransaction();
            $itemIds = $request->input('item_ids');
            $noReasonTabs = array_map('trim', explode(',', $request->input('no_reason_tabs')));
            $noReasonOperations = array_map('trim', explode(',', $request->input('no_reason_operations')));
            $newStatus = $request->input('status');
            $reasons = $request->input('reasons');
            $boradcasetData = [];
            foreach ($itemIds as $id) {
                $item = QuantityReservationRequest::withTrashed()->findOrFail($id);
                if ($item->status == 'removed' || $newStatus == 'restore') {    // Restore if current status is removed
                    $item->restore();
                } else if ($newStatus != 'restore') {
                    $item->status = $newStatus;
                }
                if (!in_array($item->status, $noReasonTabs) && !in_array($newStatus, $noReasonOperations) && gettype($reasons) === 'array') {
                    $item->reasons()->syncWithoutDetaching($reasons);
                }
                $item->save();
            }
            DB::commit();
            return $this->successResponse(null, Response::HTTP_OK);
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->getMessage());
        }
    }
}
