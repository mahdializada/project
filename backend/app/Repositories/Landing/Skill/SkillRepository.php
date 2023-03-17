<?php

namespace App\Repositories\Landing\Skill;

use App\Models\Landing\Skill;
use Illuminate\Http\Request;
use App\Repositories\Landing\LandingRepository;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class SkillRepository extends LandingRepository
{
    public function paginate(Request $request)
    {
        $queryBuilder = new UriQueryBuilder(new Skill(), $request);
        $queryBuilder->setRelations($this->getRelations())->setWithTrashed();
        $searchInColumns = ['name', 'description'];
        $queryBuilder->query = $this->filterRecords($queryBuilder->query, $request, $searchInColumns);
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

    function getRelations()
    {
        return [
            "createdBy",
            "subCategory.category:id,name"
        ];
    }

    public function show(Request $request, $id)
    {
    }

    private function storeRules($request)
    {
        $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:32'],
            'description' => ['required', 'string', 'min:5', 'max:300'],
            'sub_category_id' => ["required", "exists:product_skill_sub_categories,id", "uuid"],
        ]);
    }

    public function store(Request $request)
    {
        $this->storeRules($request);
        try {
            DB::beginTransaction();
            $skillModel = new Skill();
            $attr = $request->only($skillModel->getFillable());
            $attr['created_by'] = $request->user()->id;
            $attr['code'] = rand(100000, 9999999999);
            $skillModel = Skill::create($attr);
            DB::commit();
            return response()->json(['result' => true, 'data' => $skillModel->load($this->getRelations())], Response::HTTP_CREATED);
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function ItemsSearch($request): JsonResponse
    {
        $categoryQueryBuilder = new UriQueryBuilder(new Skill(), $request);
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

    public function changeItemsStatus(Request $request)
    {
        $this->statusValidations($request);
        try {
            DB::beginTransaction();
            $type               = $request->input('type');
            $itemIds            = $request->input('item_ids');

            foreach ($itemIds as $id) {
                $item  = Skill::withTrashed()->findOrFail($id);
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
            $item  = Skill::withTrashed()->findOrFail($id);                    
            $item->delete();

            return $this->successResponse(null, Response::HTTP_OK);
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->getMessage());
        }
    }
}
