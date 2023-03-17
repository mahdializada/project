<?php

namespace App\Repositories\SingleSales;

use App\Models\Reason;
use Illuminate\Http\Request;
use \App\Repositories\Repository;
use Illuminate\Http\JsonResponse;
use App\Models\SingleSales\StudyRequest;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class StudyRequestRepository extends Repository
{
    public function paginate(Request $request): JsonResponse
    {
        $queryBuilder = new UriQueryBuilder(new StudyRequest(), $request);
        $queryBuilder->setRelations($this->getRelations());
        $searchingFields = [
            'code',
            'whereHasOne,product,name',
            'whereHasOne,language,name',
            'study_reason',
            'status'
        ];

        $queryBuilder->query = $this->filterRecords($queryBuilder->query, $request, $searchingFields);
        $statusQuery  = clone  $queryBuilder->query;
        $queryBuilder->query = $this->fetchDataAccordingToStatus($queryBuilder->query, $request->key);

        $designRequest = $queryBuilder->build()->paginate()->getData();
        $extraData = [
            'allTotal',
            'removedTotal',
            'pendingTotal, status, pending',
            'inStudyTotal, status, in study',
            'inHoldTotal, status, in hold',
            'completedTotal, status, completed',
            'failedTotal, status, failed',
            'cancelledTotal, status, cancelled',
        ];
        $designRequest = $this->getStatusCount($statusQuery, $designRequest, $extraData);
        return response()->json($designRequest);
    }

    public function rules(): array
    {
        return [
            'product_id' => ["required", "exists:products_ssp,id", "uuid"],
            'study_language_id' => ["required", "exists:languages,id", "uuid"],
            'study_reason' => ['required', 'string', 'min:2', 'max:300'],
        ];
    }
public function getRelations(){
    return [
            'language:id,name',
            "product:id,name",
            "createdBy"
        ];
}

    public function storeOrUpdate($attributes, $id = null)
    {
        try {
            DB::beginTransaction();
            if (empty($id)) {
                $attributes['created_by'] = auth()->user()->id;
                $studyRequstModel = StudyRequest::create($attributes);
                $result = ['data' => 'record successfully created', 'code' => $studyRequstModel->code, 'result' => true];
            } else {
                $studyRequstModel = StudyRequest::findOrFail($id);
                $attributes['updated_by'] = auth()->user()->id;
                $studyRequstModel->update($attributes);
                $result = ['data' => 'record successfully updated', 'code' => $studyRequstModel->code, 'result' => true];
            }
            DB::commit();
            return response()->json($result, Response::HTTP_OK);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->errorResponse($th->getMessage(), 500);
        }
    }

    public function destroy($ids, $reason_ids)
    {

        try {
            //code...
            DB::beginTransaction();
            $studyRequests = StudyRequest::withTrashed()->whereIn('id', $ids)->get();
            $reasons = Reason::whereIn('id', $reason_ids)->get();

            // ADD REASON
            foreach ($studyRequests as $studyRequest) {
                if (!$studyRequest->trashed()) {
                    foreach ($reasons as $reason) {
                        $studyRequest->reasons()->save($reason);
                    }
                    $studyRequest->reasons()->syncWithoutDetaching($reasons);
                    $studyRequest->delete();
                } else {
                    $studyRequest->reasons()->detach();
                    $studyRequest->forceDelete();
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

    public function show($id)
    {
    }
}
