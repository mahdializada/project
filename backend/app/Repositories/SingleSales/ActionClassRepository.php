<?php

namespace App\Repositories\SingleSales;

use App\Models\Reason;
use App\Models\SingleSales\ActionClass;
use App\Repositories\Repository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use Exception;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

//use Your Model

/**
 * Class ProductStudyCategoryRepository.
 */
class ActionClassRepository extends Repository
{
    /**
     * @return string
     *  Return the model
     */
    public function paginate(Request $request)
    {
        $key                    = $request->query->get("key");
        $queryBuilder           = new UriQueryBuilder(new ActionClass(), $request);
        $queryBuilder->setRelations($this->getRelations());
        $searchInColumns        = ['statement','type',  'created_at','updated_at','parent_id'];
        $queryBuilder->query    = $this->filterRecords($queryBuilder->query, $request, $searchInColumns);
        $statusQuery            = clone  $queryBuilder->query;
        $queryBuilder->query    = $this->fetchDataAccordingToStatus($queryBuilder->query, $key);
        $queryBuilder->query->orderBy("created_at", 'desc');
        $actionClass         = $queryBuilder->build()->paginate()->getData();
        $extraData = [];
        $actionClass = $this->getStatusCount($statusQuery, $actionClass, $extraData);
        return response()->json($actionClass);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $actionClass['type'] = $request->input("type");
            $actionClass['statement'] = $request->input("statement");
            $attrModel = ActionClass::create($actionClass);
            $data = $attrModel->load($this->getRelations())->refresh();
            DB::commit();
            return response()->json($data);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 500);
        }
    }

    public function show($id)
    {
 
        $actionClass = ActionClass::findOrFail($id);
        if ($actionClass) {
            return $this->successResponse($actionClass);
        }
        return $this->errorResponse("Not Found");
    }

    public function update(Request $request)
    {
       
        try {
            $actionClassId = $request->input("id");
            $actionClass = ActionClass::findOrFail($actionClassId);
            $actionClassAttribute['type'] = $request->input("type");
            $actionClassAttribute['statement'] = $request->input("statement");
            $actionClass->update($actionClassAttribute);
            return $this->successResponse($actionClass);
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function getRelations(): array
    {
        return [];
    }

    public function destroy(array $ids, $reason_ids)
    {
        try {
            DB::beginTransaction();
            $actionClass = ActionClass::whereIn('id', $ids)->get();
            foreach ($actionClass as $s) {
                $s->delete();
            }
            DB::commit();
            return response()->json(['result' => true], Response::HTTP_OK);
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return $th;
        }
    }

    public function storeRules()
    {
        return [
            'type' => ['required'],
            'statement' => ['required'],
        ];
    }

    public function updateRules()
    {
        return [
            "id" => ['required'],
            'type' => ['required'],
            'statement' => ['required'],
        ];
    }

    public function search(Request $request)
    {
    }
}
