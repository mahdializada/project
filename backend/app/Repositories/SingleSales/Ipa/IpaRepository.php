<?php

namespace App\Repositories\SingleSales\Ipa;

use App\Models\Reason;
use App\Models\SingleSales\Ipa;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use \App\Repositories\Repository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class IpaRepository extends Repository
{
    public function paginate(Request $request): JsonResponse
    {
        $key = $request->query->get("key");
        $queryBuilder = new UriQueryBuilder(new Ipa(), $request);
        $queryBuilder->setRelations($this->getRelations());
        $searchInColumns = [
            'code',
            'whereHasOne,platforms,platform_name',
            'whereHasTwo,ispp,product,name',
            'target_countries',
            'target_gender',
            'target_age_min',
            'target_age_max',
            'target_time_start',
            'target_time_end',
            'status'
        ];

        $queryBuilder->query = $this->filterRecords($queryBuilder->query, $request, $searchInColumns);
        //      s
        $statusQuery = clone $queryBuilder->query;
        $queryBuilder->query = $this->fetchDataAccordingToStatus($queryBuilder->query, $key);

        $queryBuilder->query->orderBy("created_at", 'desc');
        $designRequest = $queryBuilder->build()->paginate()->getData();
        $extraData = [
            'allTotal',
            'removedTotal',
            'pendingTotal, status,pending',
            'inReviewTotal, status, in review',
            'rejectedTotal, status, rejected',
            'inCreationTotal, status, in creation',
            'inTestingTotal, status, in testing',
            'salesMovingTotal, status, sales moving',
            'salesUnstableTotal, status, sales unstable',
            'stoppedTotal, status, stopped',
            'failedTotal, status, failed',
            'cancelledTotal, status, cancelled',
        ];
        $designRequest = $this->getStatusCount($statusQuery, $designRequest, $extraData);
        return response()->json($designRequest);
    }


    public function store(Request $request)
    {
        $result = DB::transaction(function () use ($request) {
            //json_d
            $target_countries = [['name' => $request->country_name, 'cities' => $request->city]];
            $data = [
                'code' => $this->randomSourcingCode(),
                'ispp_id' => $request->get('ispp_id'),
                'target_countries' => json_encode($target_countries),
                'target_gender' => $request->get('target_gender'),
                'target_age_min' => $request->target_age[0],
                'target_age_max' => $request->target_age[1],
                'target_time_start' => $request->date['start_date'],
                'target_time_end' => $request->date['end_date'],
                'status' => 'pending'
            ];
            $ipa = Ipa::create($data);
            foreach ($request->get('platforms') as $value) {
                $ipa->platforms()->create([
                    'platform_name' => $value['platform_name'],
                    'platform_placement' => json_encode($value['platform_placement']),
                    'budget' => $value['budget'],
                    'target_cpo' => $value['target_cpo'],
                ]);
            }
            return $ipa;
        });

        return $this->successResponse($result->load($this->getRelations()), Response::HTTP_CREATED);
    }

    private function randomSourcingCode(): int
    {
        return rand(10000, 8999999999);
    }


    public function update(Request $request)
    {
        $ipa = Ipa::findOrFail($request->get('id'));
        $result = DB::transaction(function () use ($request, $ipa) {
            //json_d
            $target_countries = [['name' => $request->country_name, 'cities' => $request->city]];
            $data = [
                'ispp_id' => $request->get('ispp_id'),
                'target_countries' => json_encode($target_countries),
                'target_gender' => $request->get('target_gender'),
                'target_age_min' => $request->target_age[0],
                'target_age_max' => $request->target_age[1],
                'target_time_start' => $request->date['start_date'],
                'target_time_end' => $request->date['end_date'],
            ];
            $ipa->platforms()->delete();
            $ipa->update($data);
            foreach ($request->get('platforms') as $value) {
                $ipa->platforms()->create([
                    'platform_name' => $value['platform_name'],
                    'platform_placement' => json_encode($value['platform_placement']),
                    'budget' => $value['budget'],
                    'target_cpo' => $value['target_cpo'],
                ]);
            }
            return $ipa;
        });

        return $this->successResponse($result->load($this->getRelations()), Response::HTTP_CREATED);
    }

    public function show(Request $request)
    {
        # code...
        if ($request->all) {
            $ipa = Ipa::get(['id', 'code']);
            return response()->json($ipa, Response::HTTP_OK);
        }
    }


    public function storeRules(): array
    {
        return [
            'ispp_id' => ['required'],
            'target_countries' => ['required|array'],
            'target_gender' => ['required'],
            'target_age' => ['required'],
            'date' => ['required'],
            'date.*' => ['required'],

            'platforms' => ['required', 'array'],
            'platforms.*' => ['required'],
        ];
    }

    public function updateRules(Request $request)
    {
        $rules = [
            "id" => ['required', 'exists:quantity_reservation_requests_ssp,id'],
            'ispp_id' => ['required'],
            'target_countries' => ['required|array'],
            'target_gender' => ['required'],
            'target_age_min' => ['required'],
            'target_age_max' => ['required'],
            'target_time_start' => ['required'],
            'target_time_end' => ['required'],
            'platform_names' => ['required|array'],
            'platform_placements' => ['required|array'],
            'budgets' => ['required|array'],
            'target_cpos' => ['required|array'],
        ];

        $request->validate($rules);
    }

    public function search(Request $request)
    {
        $queryBuilder = new UriQueryBuilder(new Ipa(), $request);
        $data = $this->searchCode($queryBuilder->query, $request, [], false);
        return $data ? response()->json($data, 200) : response()->json(['result' => false], Response::HTTP_NO_CONTENT);
    }

    public function destroy(array $ids, $reason_ids)
    {
        try {
            //code...
            DB::beginTransaction();
            $sourcingRequests = Ipa::withTrashed()->whereIn('id', $ids)->get();
            $reasons = Reason::withTrashed()->whereIn('id', $reason_ids)->get();
            // ADD REASON
            foreach ($sourcingRequests as $s) {
                if (!$s->trashed()) {
                    $s->reasons()->syncWithoutDetaching($reasons);
                    $s->platforms()->delete();
                    $s->delete();
                } else {
                    $s->platforms()->forceDelete();
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
            'ispp.product:id,name',
            'platforms:ipa_id,platform_name,platform_placement,budget,target_cpo',
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

    public function changeIpaStatus(Request $request)
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
                $item = Ipa::withTrashed()->findOrFail($id);
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
