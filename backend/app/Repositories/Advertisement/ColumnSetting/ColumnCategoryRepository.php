<?php

namespace App\Repositories\Advertisement\ColumnSetting;

use App\Models\Advertisement\ColumnCategory;
use App\Models\Reason;
use Illuminate\Http\Request;
use App\Models\SingleSales\Category;
use App\Models\SingleSales\Attribute;
use App\Models\SingleSales\Ispp;
use App\Models\SingleSales\Product;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use App\Repositories\Repository;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * Class CategoryRepository.
 */
class ColumnCategoryRepository extends Repository
{

    public function paginate(Request $request): JsonResponse
    {


        $key = $request->query->get("key");
        // $key = 'all';
        $queryBuilder = new UriQueryBuilder(new ColumnCategory(), $request);
        $queryBuilder->setWithTrashed();
        $queryBuilder->setRelations($this->getRelations());

        $searchInColumns        = ['code', 'category_name', 'updated_at', 'created_at'];
        $queryBuilder->query    = $this->filterRecords($queryBuilder->query, $request, $searchInColumns);
        $statusQuery            = clone  $queryBuilder->query;
        $queryBuilder->query    = $this->fetchDataAccordingToStatus($queryBuilder->query, $key);

        $queryBuilder->query->orderBy("created_at", 'desc');
        $category         = $queryBuilder->build()->paginate()->getData();
        $extraData = [
            'allTotal',
            'activeTotal, status, active',
            'inactiveTotal, status, inactive',
            'removedTotal'

        ];
        $category = $this->getStatusCount($statusQuery, $category, $extraData, true);
        return response()->json($category);
    }



    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            foreach ($request->category_ids as $id) {
                $category = ColumnCategory::findOrFail($id);
                $category->subSystems()->syncWithoutDetaching($request->subsystem_ids);
            }
            DB::commit();
            return response()->json(['result' => true]);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 400);
            //throw $th;
        }
    }


    public function update(Request $request)
    {
    }

    public function destroy($ids)
    {

        try {
            //code...
            DB::beginTransaction();
            $categories = ColumnCategory::withTrashed()->whereIn('id', $ids)->get();
            foreach ($categories as $category) {
                if (!$category->trashed()) {
                    $category->status = 'removed';
                    $category->save();
                    $category->delete();
                } else {
                    $category->forceDelete();
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


    public function changeActionStatus(Request $request)
    {
        $this->statusValidations($request);
        try {
            DB::beginTransaction();
            $itemIds = $request->input('item_ids');

            $newStatus = $request->input('status');
            foreach ($itemIds as $id) {
                $item = ColumnCategory::withTrashed()->findOrFail($id);

                if ($item->status == 'removed') {
                    $item->restore();
                }
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
    private function statusValidations($request)
    {
        $request->validate([
            'type' => ['required', 'string'],
            'status' => ['string'],
            'item_ids' => ['required'],
            'reasons' => ['required_if:type,hasReason', 'array'],
        ]);
    }

    public function storeRules(): array
    {
        return [
            'category_ids'            => ['required'],
            'subsystem_ids'     => ['required'],
        ];
    }

    public function updateRules(): array
    {
        return [
            'name'            => ['required'],
            'description'     => ['required'],
        ];
    }

    private function getRelations(): array
    {
        return ['subSystems:id,name', 'user:id,firstname,lastname'];
    }

    public function search(Request $request)
    {
        $queryBuilder = new UriQueryBuilder(new ColumnCategory(), $request);

        $data = $this->searchCode($queryBuilder->query, $request, [], false, "code");
        return $data ? response()->json($data, 200) : response()->json(['result' => false], Response::HTTP_NO_CONTENT);
    }
}
