<?php

namespace App\Repositories;

use App\Repositories\QueryBuilder\UriQueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Country;
use App\Models\Reason;
use App\Models\Sourcer;
use App\Repositories\Files\CloudinaryFileUtils;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

//use Your Model

/**
 * Class SourcerRepository.
 */
class SourcerRepository extends Repository
{
    public function paginate(Request $request)
    {
        $key = $request->query->get("key");
        $queryBuilder = new UriQueryBuilder(new Sourcer(), $request);
        $queryBuilder->setRelations($this->getRelations())->setWithTrashed();
        $searchInColumns = [
            'code',
            'name',
            'email',
            'last_name',
            'phone_number',
            'country_id',
            'whereHasOne,country,name',
            'company_id',
            'whereHasOne,company,name'
        ];

        $queryBuilder->query = $this->filterRecords($queryBuilder->query, $request, $searchInColumns);
        $statusQuery = clone $queryBuilder->query;
        $queryBuilder->query = $this->fetchDataAccordingToStatus($queryBuilder->query, $key);


        $queryBuilder->query->orderBy("created_at", 'desc');
        $designRequest = $queryBuilder->build()->paginate()->getData();
        $extraData = [
            'allTotal',
            'removedTotal',
            'activeTotal, status, active',
            'inactiveTotal, status, inactive',
        ];
        $designRequest = $this->getStatusCount($statusQuery, $designRequest, $extraData);
        return response()->json($designRequest);
    }
    public function storeSourcer(Request $request)
    {
        try {
            $model = Sourcer::create([
                'code' => rand(10000, 999999),
                'name' => $request->name,
                'email' => $request->email,
                'last_name' => $request->last_name,
                'phone_number' => $request->phone_number,
                'country_id' => $request->country_id,
                'company_id' => $request->company_id,
            ]);
            CloudinaryFileUtils::storeFiles($model, [$request->image]);
            return response()->json($model->load($this->getRelations())->refresh(), Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function getSourcerCountry()
    {
        return Country::orderBy('name', 'asc')->get();
    }
    public function getRelations()
    {
        return [
            'company:id,name,logo',
            'country:id,name,iso2',
            'attachments:attachmentable_id,path',
            // 'reasons'
        ];
    }
    public function changeSourcersStatus(Request $request)
    {
        return $this->changeStatus($request,  Sourcer::class, 'sourcers', 'reason_sourcers', 'sourcer_id', $this->getRelations(), '');
    }
    public function destroy($ids, $reasonId,$request)
    {
        try {
            return $this->destroyItems(
                Sourcer::class,
                $ids,
                'reason_sourcer',
                $reasonId,
                'sourcer_id',
                null,
                $this->getRelations(),
                'sourcer',
                $request
            );
            // return $this->destroyItems(Sourcer::class, $ids, 'reason_sourcer', $reason_ids, 'sourcer_id', null, [], 'business-locations');
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }
    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = Sourcer::find($request->id);
            $data->update([
                'name' => $request->name,
                'email' => $request->email,
                'last_name' => $request->last_name,
                'phone_number' => $request->phone_number,
                'country_id' => $request->country_id,
                'company_id' => $request->company_id,
            ]);
             CloudinaryFileUtils::updateFiles($data, $request->id,[$request->image]);
            DB::commit();
            return $this->successResponse($data->load($this->getRelations()), Response::HTTP_OK);
        } catch (Exception $exception) {
            DB::rollback();
            return $this->errorResponse($exception->getMessage());
        }
    }
    public function updateRules(): array
    {
        return [
            'name'            => ['required'],
            'email'            => ['required'],
            'last_name'            => ['required'],
            'phone_number'            => ['required'],
            'country_id'            => ['required'],
            'company_id'            => ['required'],

        ];
    }
    public function search(Request $request)
    {
        $queryBuilder = new UriQueryBuilder(new Sourcer(), $request);
        $data = $this->searchCode($queryBuilder->query, $request, [], false);
        return $data ? response()->json($data, 200) : response()->json(['result' => false], Response::HTTP_NO_CONTENT);
    }
}
