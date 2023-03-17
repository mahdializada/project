<?php

namespace App\Repositories\SingleSales;

use App\Models\Reason;
use App\Models\SingleSales\ProductSourceValue;
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
class ProductSourceValueRepository extends Repository
{
    /**
     * @return string
     *  Return the model
     */
    public function paginate(Request $request)
    {
       
        $key = $request->query->get("key");
        $queryBuilder = new UriQueryBuilder(new ProductSourceValue(), $request);
        $queryBuilder->setRelations($this->getRelations());
        $searchInColumns        = ['value',  'created_at','updated_at','product_source_id'];
        $queryBuilder->query    = $this->filterRecords($queryBuilder->query, $request, $searchInColumns);
        $statusQuery            = clone  $queryBuilder->query;
        $queryBuilder->query    = $this->fetchDataAccordingToStatus($queryBuilder->query, $key);
        $queryBuilder->query->orderBy("created_at", 'desc');
        $productSourceValue         = $queryBuilder->build()->paginate()->getData();
        $extraData = [];
        $productSourceValue = $this->getStatusCount($statusQuery, $productSourceValue, $extraData);
        return response()->json($productSourceValue);
    }

    public function store(Request $request)
    {
       
        try {
            DB::beginTransaction();
            $productSourceValue['product_source_id'] = $request->input("product_source_id");
            $productSourceValue['value'] = $request->input("value");
            $attrModel = ProductSourceValue::create($productSourceValue);
            $data = $attrModel->load($this->getRelations())->refresh();
            DB::commit();
            return response()->json($data);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 500);
        }
    }

    public function show($id)
    {
        $productSourceValue = ProductSourceValue::findOrFail($id);
        if ($productSourceValue) {
            return $this->successResponse($productSourceValue);
        }
        return $this->errorResponse("Not Found");
    }

    public function update(Request $request)
    {
        try {
            $productSourceId = $request->input("id");
            $productSourceValue = ProductSourceValue::findOrFail($productSourceId);
            $productSourceValueAttribute['product_source_id'] = $request->input("product_source_id");
            $productSourceValueAttribute['value'] = $request->input("value");
            $productSourceValue->update($productSourceValueAttribute);
            return $this->successResponse($productSourceValue);
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
            $productSourceValue = ProductSourceValue::whereIn('id', $ids)->get();
            foreach ($productSourceValue as $s) { 
                $s->delete();
            }
            DB::commit();
            return response()->json(['result' => true], Response::HTTP_OK);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }

    public function storeRules()
    {
        return [
            'value' => ['required'],
        ];
    }

    public function updateRules()
    {
        return [
            "id" => ['required'],
            'value' => ['required'],
        ];
    }

    public function search(Request $request)
    {
    }
}
