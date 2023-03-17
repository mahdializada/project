<?php

namespace App\Repositories\SingleSales;

use App\Models\Reason;
use App\Models\SingleSales\ProductStudyCategory;
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
class ProductStudyCategoryRepository extends Repository
{
    /**
     * @return string
     *  Return the model
     */
    public function paginate(Request $request)
    {
        $key = $request->query->get("key");
        $queryBuilder = new UriQueryBuilder(new ProductStudyCategory(), $request);
        $queryBuilder->setRelations($this->getRelations());

        $searchInColumns        = [];
        $queryBuilder->query    = $this->filterRecords($queryBuilder->query, $request, $searchInColumns);
        $statusQuery            = clone  $queryBuilder->query;
        $queryBuilder->query    = $this->fetchDataAccordingToStatus($queryBuilder->query, $key);

        $queryBuilder->query->orderBy("created_at", 'desc');
        $productStudyCategory         = $queryBuilder->build()->paginate()->getData();
        $extraData = [];
        $productStudyCategory = $this->getStatusCount($statusQuery, $productStudyCategory, $extraData);
        return response()->json($productStudyCategory);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $productStudyCategory['category_name'] = $request->input("category_name");
            $attrModel = ProductStudyCategory::create($productStudyCategory);
            $data[] = $attrModel->load($this->getRelations())->refresh();

            DB::commit();
            return response()->json($data);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 500);
        }
    }

    public function show($id)
    {
        $productStudyCategory = ProductStudyCategory::findOrFail($id);
        if ($productStudyCategory) {
            return $this->successResponse($productStudyCategory);
        }
        return $this->errorResponse("Not Found");
    }

    public function update(Request $request)
    {
        try {
            $productStudyCategoryId = $request->input("id");
            $productStudyCategory = ProductStudyCategory::findOrFail($productStudyCategoryId);
            $attributes['category_name'] = $request->input("category_name");

            $productStudyCategory->update($attributes);
            return $this->successResponse($productStudyCategory);
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
            //code...
            DB::beginTransaction();
            $productStudyCategory = ProductStudyCategory::withTrashed()->whereIn('id', $ids)->get();
            // $reasons = Reason::whereIn('id', $reason_ids)->get();
            // ADD REASON
            foreach ($productStudyCategory as $s) {
                if (!$s->trashed()) {
                    // $s->reasons()->syncWithoutDetaching($reasons);
                    $s->delete();
                } else {
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

    public function storeRules()
    {
        return [
            'category_name' => ['required'],
        ];
    }

    public function updateRules()
    {
        return [
            "id" => ['required'],
            'category_name' => ['required'],
        ];
    }

    public function search(Request $request)
    {
    }
}
