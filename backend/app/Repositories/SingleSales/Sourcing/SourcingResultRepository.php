<?php

namespace App\Repositories\SingleSales\Sourcing;

use App\Models\Reason;
use App\Models\SingleSales\SourcingRequest;
use App\Models\SingleSales\SourcingResult;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use function response;
class SourcingResultRepository extends \App\Repositories\Repository
{
    public function paginate(Request $request): JsonResponse
    {
        $key = $request->query->get("key");
        $queryBuilder = new UriQueryBuilder(new SourcingResult(), $request);
        $searchInColumns = [
            'code',
            'type',
            'whereHasOne,importingCountry,name',
            'whereHasOne,receivingCountry,name',
            'reason_for_search',
            'approx_quantity',
            'required_shipping_method',
            'whereHasOne,product,name',
            'status'
        ];

        $queryBuilder->query = $this->filterRecords($queryBuilder->query, $request, $searchInColumns);
        $statusQuery = clone $queryBuilder->query;
        $queryBuilder->query = $this->fetchDataAccordingToStatus($queryBuilder->query, $key);


        $queryBuilder->query->orderBy("created_at", 'desc');
        $designRequest = $queryBuilder->build()->paginate()->getData();
        $extraData = [
            'allTotal',
            'removedTotal',
            'waitingTotal, status,waiting',
            'pendingTotal, status,pending',
            'rejectedTotal, status, rejected',
            'inSourcingTotal, status, in sourcing',
            'inHoldTotal, status, in hold',
            'notFoundTotal, status, not found',
            'foundTotal, status, found',
            'cancelledTotal, status, cancelled',
        ];
        $designRequest = $this->getStatusCount($statusQuery, $designRequest, $extraData);
        return response()->json($designRequest);
    }


    public function store(Request $request)
    {
        $result = DB::transaction(function () use ($request) {
            $data = $request->all();
            $created = SourcingRequest::create($data);
            return $created;

        });

        return $this->successResponse($result, Response::HTTP_CREATED);
    }


    public function update(Request $request)
    {
        $sourcingReq = SourcingRequest::findOrFail($request->get('id'));
        return $this->successResponse(DB::transaction(function () use ($request, $sourcingReq) {
            return $sourcingReq->update($request->except(['id']));
        }), Response::HTTP_CREATED);

    }


    public function storeRules(): array
    {
        return [
            'product_id' => ['required', 'numeric', 'min:1'],
            "sourcing_type" => ["nullable"],
            'importing_country_id' => ['required'],
            'receiving_country_id' => ['required'],
            'reason_for_search' => ['required'],
            'approx_quantity' => ['required'],
            'target_cost' => ['required'],
            'required_shipping_method' => ['required'],
            'status' => ['required'],
        ];
    }


    public function updateRules(Request $request)
    {
        $rules = [
            "id" => ['required', 'exists:sourcing_requests_ssp,id'],
            'product_id' => ['required', 'numeric', 'min:1'],
            "sourcing_type" => ["nullable"],
            'importing_country_id' => ['required'],
            'receiving_country_id' => ['required'],
            'reason_for_search' => ['required'],
            'approx_quantity' => ['required'],
            'target_cost' => ['required'],
            'required_shipping_method' => ['required'],
            'status' => ['required'],
        ];

        $request->validate($rules);
    }

    public function destroy($ids, $reason_ids)
    {

        try {
            //code...
            DB::beginTransaction();
            $sourcingRequests = SourcingRequest::withTrashed()->whereIn('id', $ids)->get();
            $reasons = Reason::whereIn('id', $reason_ids)->get();

            // ADD REASON
            foreach ($sourcingRequests as $sourcingRequest) {
                if (!$sourcingRequest->trashed()) {
                    foreach ($reasons as $reason) {
                        $sourcingRequest->reasons()->save($reason);
                    }
                    $sourcingRequest->reasons()->syncWithoutDetaching($reasons);
                    $sourcingRequest->delete();
                } else {
                    $sourcingRequest->reasons()->detach();
                    $sourcingRequest->forceDelete();
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
            'importingCountry:id,name',
            'receivingCountry:id,name',
            "company:id,name",
            "user:id,firstname,lastname",
            'product',
        ];
    }
}
