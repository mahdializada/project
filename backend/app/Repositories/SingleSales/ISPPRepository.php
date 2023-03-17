<?php

namespace App\Repositories\SingleSales;

use App\Models\Reason;
use Illuminate\Http\Request;
use App\Models\SingleSales\Ispp;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use App\Repositories\Repository;
use Illuminate\Http\Response;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * Class CategoryRepository.
 */
class ISPPRepository extends Repository
{

    public function paginate(Request $request): JsonResponse
    {
        $key = $request->query->get("key");
        $queryBuilder = new UriQueryBuilder(new Ispp(), $request);
        $queryBuilder->setRelations($this->getRelations());
        $searchInColumns        = [
            'whereHasOne,company,name',
            'working_type', 'product_type', 'priority',
            'code','updated_at', 'created_at', 'product_availablity', 'is_seasonal',
            'season_or_event_name', 'available_quantity', 'product_source_value_id', 'head_selling_price',
        ];

        $queryBuilder->query    = $this->filterRecords($queryBuilder->query, $request, $searchInColumns);
        $statusQuery            = clone  $queryBuilder->query;
        $customColumn = [];
        $queryBuilder->query    = $this->fetchDataAccordingToStatus($queryBuilder->query, $key, $customColumn);

        $queryBuilder->query->orderBy("created_at", 'desc');
        $ispp         = $queryBuilder->build()->paginate()->getData();
        $extraData = [
            'allTotal',
            'removedTotal',
            'pendingTotal,status,pending',
            'inPreparationTotal,status,in preparation',
            'inReviewTotal,status,in review',
            'inHoldTotal,status,in hold',
            'completedTotal,status,completed',
            'failedTotal,status,failed',
            'cancelledTotal,status,cancelled',
            'removedTotal,status,removed',
        ];
        $ispp = $this->getStatusCount($statusQuery, $ispp, $extraData);
        return response()->json($ispp);
    }

    public function search(Request $request)
    {
        $queryBuilder = new UriQueryBuilder(new Ispp(), $request);
        $data = $this->searchCode($queryBuilder->query, $request, $this->getRelations(), false);
        return $data ? response()->json($data, 200) : response()->json(['result' => false], Response::HTTP_NO_CONTENT);
    }


    public function store(Request $request)
    {

        try {
            $isppModel = new Ispp();
            DB::beginTransaction();
            $attributes         = $request->only($isppModel->getFillable());
            $attributes['code'] = rand(100000, 9999999999);
            $attributes['price_patterns'] = json_encode($attributes['price_patterns']);
            $result =  $isppModel->create($attributes);
            $result->targetSaleCountry()->sync($request->get('attribute'));
            DB::commit();
            return response()->json(['result' => true, 'ispp' => $result->load($this->getRelations())->refresh()], Response::HTTP_CREATED);
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->getMessage());
        }
    }


    public function update(Request $request)
    {

        $isppModel = Ispp::findOrFail($request->id);
        $attributes         = $request->only($isppModel->getFillable());
        $attributes['price_patterns'] = json_encode($attributes['price_patterns']);
        try {
            DB::beginTransaction();
            $isppModel->update($attributes);
            DB::commit();
            return response()->json(['result' => true, 'ispp' => $isppModel], Response::HTTP_CREATED);
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function show(Request $request)
    {
        # code...
        if ($request->all) {
            $ispp = Ispp::get(['id', 'code']);
            return response()->json($ispp, Response::HTTP_OK);
        }
    }

    public function destroy($ids, $reason_ids)
    {

        try {
            //code...
            DB::beginTransaction();
            $ispps = Ispp::withTrashed()->whereIn('id', $ids)->get();
            $reasons = Reason::whereIn('id', $reason_ids)->get();

            // ADD REASON
            foreach ($ispps as $ispp) {
                if (!$ispp->trashed()) {
                    foreach ($reasons as $reason) {
                        $ispp->reasons()->save($reason);
                    }
                    $ispp->reasons()->syncWithoutDetaching($reasons);
                    $ispp->delete();
                } else {
                    $ispp->reasons()->detach();
                    $ispp->forceDelete();
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


    public function storeRules(): array
    {
        return [];
    }

    public function updateRules(): array
    {
        return [];
    }
    public function changeIsppStatus(Request $request)
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
                $item = Ispp::withTrashed()->findOrFail($id);
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


    private function getRelations(): array
    {

        return [
            'displayLanguage:id,name',
            'company:id,name',
            'currency:id,name',
        ];
    }
}
