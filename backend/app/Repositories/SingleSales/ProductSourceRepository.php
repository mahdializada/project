<?php

namespace App\Repositories\SingleSales;

use App\Models\Reason;
use App\Models\SingleSales\ProductSource;
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
class ProductSourceRepository extends Repository
{
    /**
     * @return string
     *  Return the model
     */
    public function paginate(Request $request)
    {
        $key                    = $request->query->get("key");
        $queryBuilder           = new UriQueryBuilder(new ProductSource(), $request);
        $queryBuilder->setRelations($this->getRelations());
        $searchInColumns        = ['source_name',  'created_at','updated_at','parent_id'];
        $queryBuilder->query    = $this->filterRecords($queryBuilder->query, $request, $searchInColumns);
        $statusQuery            = clone  $queryBuilder->query;
        $queryBuilder->query    = $this->fetchDataAccordingToStatus($queryBuilder->query, $key);
        $queryBuilder->query->orderBy("created_at", 'desc');
        $productSource         = $queryBuilder->build()->paginate()->getData();
        $extraData = [];
        $productSource = $this->getStatusCount($statusQuery, $productSource, $extraData);
        return response()->json($productSource);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $productSource['parent_id'] = $request->input("parent_id");
            $productSource['source_name'] = $request->input("source_name");
            $attrModel = ProductSource::create($productSource);
            $data = $attrModel->load($this->getRelations())->refresh();
            DB::commit();
            return response()->json($data);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 500);
        }
    }

    public function show($id)
    {
 
        $productSource = ProductSource::findOrFail($id);
        if ($productSource) {
            return $this->successResponse($productSource);
        }
        return $this->errorResponse("Not Found");
    }

    public function update(Request $request)
    {
       
        try {
            $productSourceId = $request->input("id");
            $productSource = ProductSource::findOrFail($productSourceId);
            $productSourceAttribute['parent_id'] = $request->input("parent_id");
            $productSourceAttribute['source_name'] = $request->input("source_name");
            $productSource->update($productSourceAttribute);
            return $this->successResponse($productSource);
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
            $productSource = ProductSource::whereIn('id', $ids)->get();
            foreach ($productSource as $s) {
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
            'source_name' => ['required'],
        ];
    }

    public function updateRules()
    {
        return [
            "id" => ['required'],
            'source_name' => ['required'],
        ];
    }

    public function search(Request $request)
    {
    }
}
