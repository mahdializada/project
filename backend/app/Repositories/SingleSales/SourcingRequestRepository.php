<?php

namespace App\Repositories\SingleSales;

use App\Models\SingleSales\ProductStudy;
use Exception;
use App\Models\Reason;
use Illuminate\Http\Request;
use App\Models\DesignRequest;
use Illuminate\Http\Response;
use \App\Repositories\Repository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\SingleSales\SourcingRequest;
use App\Repositories\QueryBuilder\UriQueryBuilder;


class SourcingRequestRepository extends Repository
{
    public function paginate(Request $request): JsonResponse
    {
        $key = $request->query->get("key");
        $queryBuilder = new UriQueryBuilder(new SourcingRequest(), $request);
        $queryBuilder->setRelations($this->getRelations());
        $searchInColumns = [
            'whereHasOne,city,name',
            // 'whereHasOne,receivingCountry,name',
            // 'reason_for_search',
            // 'approx_quantity',
            'sourcing_type',
            'required_shipping_method',
            'note',
            // 'whereHasOne,product,name',
            'status'
        ];

        if ($request->order) {
            $queryBuilder->query = $queryBuilder->query
                ->whereHas("requestAssinUser", function ($userQuery) {
                    $userQuery->where("user_id", auth()->id());
                });
        }


        $queryBuilder->query = $this->filterRecords($queryBuilder->query, $request, $searchInColumns);
        $statusQuery  = clone  $queryBuilder->query;
        $customColumn = ['assigned, whereHas, requestAssinUser', 'not assigned, whereDoesntHave, requestAssinUser'];
        $queryBuilder->query = $this->fetchDataAccordingToStatus($queryBuilder->query, $key, $customColumn);


        $queryBuilder->query->orderBy("created_at", 'desc');
        $designRequest = $queryBuilder->build()->paginate()->getData();
        $extraData = [
            'allTotal',
            'removedTotal',
       
            'pendingTotal, status,pending',
            'rejectedTotal, status, rejected',
            'inSourcingTotal, status, in sourcing',
            'inHoldTotal, status, in hold',
            'inHoldTotal, status, sourcing failed',
            'inHoldTotal, status, semi found',
            'notFoundTotal, status, not found',
            'foundTotal, status, found',
            'cancelledTotal, status, cancelled',
            'assignedTotal, whereHas, requestAssinUser',
            'notAssignedTotal, whereDoesntHave, requestAssinUser',
        ];
        $designRequest = $this->getStatusCount($statusQuery, $designRequest, $extraData);
        return response()->json($designRequest);
    }

    public function show(Request $request)
    {
        if ($request->query->has('code')) {
            $query =  SourcingRequest::with($this->getRelations())
                ->where('code', $request->code);
            if ($request->order) {
                $query = $query->whereHas("requestAssinUser", function ($userQuery) {
                    $userQuery->where("user_id", auth()->id());
                });
            }
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
            $data = $request->all();
            $data['status'] = 'pending';
            $data['code'] =  $this->randomSourcingCode();
            $created = SourcingRequest::create($data);
            return $created;
        });

        return $this->successResponse($result->load($this->getRelations())->refresh(), Response::HTTP_CREATED);
    }

    private function randomSourcingCode(): int
    {
        return rand(10000, 8999999999);
    }


    public function update(Request $request)
    {

        try {
            DB::beginTransaction();
            $sourcers = $request->all();
            $model = SourcingRequest::findOrFail($request->get('id'));
            $model->update($sourcers);
            $updated_items = $model->load($this->getRelations());

            DB::commit();
            return response()->json($updated_items);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 500);
        }
    }

    public function storeRules(): array
    {
        return [
            // 'product_id' => ['required'],
            "sourcing_type" => ["nullable"],
            'city_id' => ['required'],
            // 'receiving_country_id' => ['required'],
            // 'reason_for_search' => ['required'],
            'required_shipping_method' => ['required'],
        ];
    }


    public function updateRules(Request $request)
    {
        $rules = [
            "id" => ['required', 'exists:sourcing_requests_ssp,id'],
            "sourcing_type" => ["nullable"],
            'city_id' => ['required'],
            // 'receiving_country_id' => ['required'],
            // 'reason_for_search' => ['required'],
            // 'approx_quantity' => ['required'],
            'required_shipping_method' => ['required'],
        ];

        $request->validate($rules);
    }

    public function destroy($ids, $reasonsIds)
    {
        try {
            //code...
            DB::beginTransaction();
            $sourcingRequests = SourcingRequest::withTrashed()->whereIn('id', $ids)->get();
            $reasons = Reason::withTrashed()->whereIn('id', $reasonsIds)->get();
            // ADD REASON
            foreach ($sourcingRequests as $s) {
                if (!$s->trashed()) {
                    $s->reasons()->syncWithoutDetaching($reasons);
                    $s->delete();
                } else {
                    $s->reasons()->detach();
                    $s->forceDelete();
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
            'city:id,name',
            // 'receivingCountry:id,name',
            'product:id,name,category_id',
            "product.category:id,name",
            "reasons",
        ];
    }

    public function changeSourcingRequestStatus(Request $request)
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
                $item = SourcingRequest::withTrashed()->findOrFail($id);
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
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->getMessage());
        }
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

    public function searchSourcer(Request $request)
    {
        $queryBuilder = new UriQueryBuilder(new SourcingRequest(), $request);
        $data = $this->searchCode($queryBuilder->query, $request);
        return $data ? response()->json($data, 200) : response()->json(['result' => false], Response::HTTP_NO_CONTENT);
    }

    public function unAssign(Request $request)
    {
        try {
            DB::beginTransaction();
            $orderIds = $request->input("orderIds");
            DB::table('sourcing_request_assign_users')->whereIn("sourcing_request_id", $orderIds)->delete();
            DB::commit();
            return $this->successResponse(null, Response::HTTP_OK);
        } catch (\Throwable $th) {
            DB::rollback();
            return $this->errorResponse($th->getMessage());
        }
    }

    public function assign(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $arr = [];
            $sourcings = SourcingRequest::with($this->getRelations())->whereIn('id', $data['assign_ids'])->get();
            foreach ($sourcings as $req) {
                foreach ($data['user_ids'] as $user_id) {
                    $arr[$user_id] = [
                        'user_id' => $request->user()->id,
                        'sourcing_request_id' => $req->id
                    ];
                }
                $req->requestAssinUser()->syncWithoutDetaching($arr);
            }
            DB::commit();
            return $this->successResponse(null, Response::HTTP_CREATED);
        } catch (Exception $exception) {
            DB::rollback();
            return $this->errorResponse($exception->getMessage());
        }
    }
}
