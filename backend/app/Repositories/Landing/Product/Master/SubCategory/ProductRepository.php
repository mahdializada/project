<?php

/**
 * @author [Ahmad Reza Azimi]
 * @email [teebalhoor@info.org]
 * @create date 2022-05-26 10:56:36
 * @modify date 2022-05-26 10:56:36
 * @desc [description]
 * all reserved for Teeb-al-Hoor Commercial Broker 
 */

namespace App\Repositories\Landing\Product\Master\SubCategory;

use App\Models\Landing\Master\Product\Skill\SkillSubCategory;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Repositories\Landing\LandingRepository;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ProductRepository extends LandingRepository
{
    public function __construct($model,$filePath = "")
    {
        parent::__construct($model, $filePath);
    }

    public function paginate(Request $request)
    {
        $queryBuilder = new UriQueryBuilder($this->model, $request);
         $queryBuilder->setRelations($this->getRelations())->setWithTrashed();
        $searchInColumns = ['name', 'description'];
        $queryBuilder->query = $this->filterRecords($queryBuilder->query, $request, $searchInColumns);
        if ($request->category_id) {
            $queryBuilder->query = $queryBuilder->query ->where('category_id', $request->category_id);
        }
        $statusQuery = clone  $queryBuilder->query;
        $queryBuilder->query = $this->fetchDataAccordingToStatus($queryBuilder->query, $request->key);

        $products = $queryBuilder->build()->paginate()->getData();
        $extraData = [
            'allTotal',
            'activeTotal, status, active',
            'inactiveTotal, status, inactive',
            'blockedTotal, status, blocked',
            'pendingTotal, status, pending',
            'warningTotal, status, warning',
            'removedTotal'
        ];
        $products = $this->getStatusCount($statusQuery, $products, $extraData);
        return response()->json($products);
    }

    public function getRelations()
    {
        return [
            "category",
            // "createdBy"
        ];
    }
   
    public function search($request): JsonResponse
    {
        $categoryQueryBuilder = new UriQueryBuilder($this->model, $request);
        $data = $this->searchCode($categoryQueryBuilder->query, $request, $this->getRelations());
        return $data ? response()->json($data, 200) : response()->json(['result' => false], Response::HTTP_NO_CONTENT);
    }

    
    private function statusValidations($request)
    {
        $request->validate([
            'type'              => ['required', 'string'],
            'status'            => ['string'],
            'item_ids'          => ['required'],
            'reasons'           => ['required_if:type,hasReason', 'array'],
        ]);
    }

    public function changeSubCategoriesStatus(Request $request)
    {
        $this->statusValidations($request);
        try {
            DB::beginTransaction();
            $type               = $request->input('type');
            $itemIds            = $request->input('item_ids');

            foreach ($itemIds as $id) {
                $item  = $this->model::withTrashed()->findOrFail($id);
                $newStatus = $request->input('status');
                $item->updated_at = Carbon::now();
                $item->status = $newStatus;
                $item->save();
            }
            DB::commit();
            return $this->successResponse(null, Response::HTTP_OK);
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function changedRemovedStatus($id)
    {
        try {
            $item  = $this->model::withTrashed()->findOrFail($id);
            $item->delete();

            return $this->successResponse(null, Response::HTTP_OK);
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->getMessage());
        }
    }
    
}
