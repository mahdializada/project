<?php

namespace App\Repositories\SingleSales;

use App\Models\Reason;
use Illuminate\Http\Request;
use App\Models\SingleSales\Category;
use App\Models\SingleSales\Attribute;
use App\Models\SingleSales\Ispp;
use App\Models\SingleSales\Product;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use App\Repositories\Repository;
use Illuminate\Http\Response;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * Class CategoryRepository.
 */
class CategoryRepository extends Repository
{

    public function paginate(Request $request): JsonResponse
    {
        if ($request->query->get("fetch_for_form")) {
            return $this->getForForm($request);
        }
        if ($request->query->get("search_for_from")) {
            return $this->searchForForm($request);
        }
        if ($request->query->get("get_for_dropdown")) {
            return  $this->getForDropDown($request);
        }
        $key = $request->query->get("key");
        if ($key == 'getAttributes') {
            return $this->getAttributes();
        }
        $queryBuilder = new UriQueryBuilder(new Category(), $request);
        $queryBuilder->setRelations($this->getRelations());

        $searchInColumns        = ['status', 'name', 'description','arabic_name','arabic_description','whereHasOne,attributes,name', 'updated_at', 'created_at'];
        $queryBuilder->query    = $this->filterRecords($queryBuilder->query, $request, $searchInColumns);
        $statusQuery            = clone  $queryBuilder->query;
        $queryBuilder->query    = $this->fetchDataAccordingToStatus($queryBuilder->query, $key);

        $queryBuilder->query->orderBy("created_at", 'desc');
        $category         = $queryBuilder->build()->paginate()->getData();
        $extraData = [
            'allTotal',
            'removedTotal',
            'activeTotal,status,active',
            'inactiveTotal,status,inactive',
        ];
        $category = $this->getStatusCount($statusQuery, $category, $extraData);
        return response()->json($category);
    }


    public function filterCategory()
    {
        try {
            $columns = ["id", "code", "name", "icon"];
            $categories = Category::whereStatus("active")->select($columns)->get();
            return response()->json($categories);
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }
    public function store(Request $request)
    {
        try {
            $exist = $this->checkNewAttrbutesUniqueness($request);
            if (count($exist) != 0)
                return response()->json(['duplicate_error' => $exist], Response::HTTP_CREATED);


            $categoryModel = new Category();
            DB::beginTransaction();
            $category_attributes = $request->get('attribute');
            // $new_attributes = $request->newAttribute;
            $attributes         = $request->only($categoryModel->getFillable());
            if ($request->icon) {
                $attributes['icon'] = $this->storeFile($request->icon);
            }
            // if ($request->banner) {
            //     $attributes['banner'] = $this->storeFile($request->banner);
            // }
            // if ($request->selectedCategory) {
            //     $attributes['parent_id'] = $request->selectedCategory;
            // }
            $attributes['created_by'] = auth()->user()->id;
            $attributes['arabic_name'] = $request->arabic_name;
            $attributes['arabic_description'] = $request->arabic_description;
            // $attributes['company_id'] = json_encode($request->get('companyIds'));
            $attributes['code'] = rand(100000, 9999999999);
            $result =  Category::create($attributes);
            $attributes = $request->all();
            // insert attribute
            // if ($new_attributes) {
            //     $attrModel = $this->insertAttribute($new_attributes);
            //     $category_attributes = array_merge($attrModel, $category_attributes);
            // }

            $result->attributes()->sync($category_attributes);
            DB::commit();
            return response()->json(['result' => true, 'category' => $result->load($this->getRelations())->refresh()], Response::HTTP_CREATED);
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->getMessage());
        }
    }
    public function insertAttribute($new_attributes)
    {

        $attrModel = [];
        foreach ($new_attributes as $key) {
            $key = json_decode($key, true);
            $key['values'] =  json_encode($key['value']);
            $key['created_by'] = auth()->user()->id;
            $key['code'] = rand(100000, 9999999999);
            $attrModel[] =   Attribute::create($key);
        }
        $attrModel = array_column($attrModel, 'id');
        return $attrModel;
    }

    public function update(Request $request)
    {

        $exist = $this->checkNewAttrbutesUniqueness($request);
        if (count($exist) != 0)
            return response()->json(['duplicate_error' => $exist], Response::HTTP_CREATED);


        $attributes = [];
        $category_attributes = $request->get('attribute');
        $new_attributes = $request->newAttribute;
        try {
            DB::beginTransaction();
            $categoryModel = Category::findOrFail($request->id);
            if (is_file($request->icon)) {

                $categoryModel->icon = $this->storeAndRemove($request->icon, $categoryModel->getRawOriginal("icon"));
            }
            // if (is_file($request->banner)) {

            //     $categoryModel->banner = $this->storeAndRemove($request->banner, $categoryModel->getRawOriginal("banner"));
            // }
            $categoryModel->name = $request->name;
            $categoryModel->arabic_name = $request->arabic_name;
            $categoryModel->description = $request->description;
            $categoryModel->arabic_description = $request->arabic_description;
            // insert attribute
            // if ($new_attributes) {
            //     $attrModel = $this->insertAttribute($new_attributes);
            //     $category_attributes = array_merge($attrModel, $category_attributes);
            // }
            $categoryModel->attributes()->sync($category_attributes);
            $categoryModel->save();

            DB::commit();
            return response()->json(['result' => true, 'category' => $categoryModel->load($this->getRelations())], Response::HTTP_CREATED);
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function destroy($ids, $reason_ids)
    {

        try {
            //code...
            DB::beginTransaction();
            $categories = Category::withTrashed()->whereIn('id', $ids)->get();
            $reasons = Reason::whereIn('id', $reason_ids)->get();
            // ADD REASON
            foreach ($categories as $category) {
                if (!$category->trashed()) {
                    foreach ($reasons as $reason) {
                        $category->reasons()->save($reason);
                    }
                    $category->reasons()->syncWithoutDetaching($reasons);
                    $category->delete();
                } else {
                    $this->deleteFile($category->getRawOriginal('icon'));
                    // $this->deleteFile($category->getRawOriginal('banner'));
                    $category->reasons()->detach();
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


    public function storeRules(): array
    {
        return [
            'name'            => ['required'],
            'description'     => ['required'],
            // 'arabic_descrarabic_description'     => ['required'],
            'newAttribute.*.name'    => ['unique:attributes_ssp,name'],
        ];
    }

    public function updateRules(): array
    {
        return [
            'name'            => ['required'],
            'description'            => ['required'],
            // 'arabic_descrarabic_description'            => ['required'],

        ];
    }


    private function getRelations(): array
    {

        return [
            'attributes:id,name,values',
            'user:id,firstname,lastname',

        ];
    }

    public function search(Request $request)
    {
        $queryBuilder = new UriQueryBuilder(new Category(), $request);
        $data = $this->searchCode($queryBuilder->query, $request, [], false);
        return $data ? response()->json($data, 200) : response()->json(['result' => false], Response::HTTP_NO_CONTENT);
    }

    public function changeCategoriesStatus(Request $request)
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
                $item = Category::withTrashed()->findOrFail($id);
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
    public function getAttributes()
    {
        $attributes = Attribute::select(['id', 'name'])->get();
        return response()->json($attributes);
    }
    private function getForForm(Request $request)
    {
        if ($request->query->get('category_id')) {
            $data = Category::where('parent_id', $request->query->get('category_id'))
                ->select(['id', 'name', 'parent_id'])
                ->orderBy('name')
                ->get();
        } else {
            $data = Category::select(['id', 'name', 'parent_id'])
                ->where('parent_id', null)
                ->orderBy('name')
                ->get();
        }
        return response()->json($data);
    }
    private function getForDropDown(Request $request)
    {
        $data = Category::select(['id', 'name', 'parent_id'])
            ->where('parent_id', null)
            ->orderBy('name')
            ->get();
        return response()->json($data);
    }

    private function searchForForm(Request $request)
    {
        $search = $request->query->get('search');
        $category_id = $request->query->get('category_id');
        $category_ids = $this->getCategories($category_id);
        $foundProducts = null;
        $foundCategories = null;
        $foundCategories = Category::where('name', 'like', '%' . $search . '%')
            ->whereIn('id', $category_ids)
            ->get();
        if (!$request->query->get('only_category')) {
            $foundProducts = Product::where('name', 'like', '%' . $search . '%')
                ->whereIn('category_id', $category_ids)
                ->get();
        }
        return response()->json([
            'products' => $foundProducts,
            'categories' => $foundCategories
        ]);
    }

    private function getCategories($category_id, $arr = [])
    {
        $categories = Category::where('parent_id', $category_id)->pluck('id');
        if (count($categories) > 0) {
            $arr = array_merge($arr, $categories->toArray());
            foreach ($categories as $category) {
                return $this->getCategories($category, $arr);
            }
        } else {
            return $arr;
        }
    }

    function checkNewAttrbutesUniqueness($request)
    {
        $duplicates = [];
        $columns = $request->newAttribute;
        if ($columns)
            foreach ($columns  as $column) {
                $column = json_decode($column, true);

                $isExists = Attribute::whereName($column['name'])->exists();
                if ($isExists)
                    $duplicates[] = $column['name'];
            }

        return $duplicates;
    }
    function categoryAttributes($request)
    {

        $category_id = $request->category_id;

        try {
            if ($category_id) {
                $attribues = Category::where('id', $category_id)->with(['attributes:id,name'])->get(['id']);
                return $this->successResponse($attribues, Response::HTTP_OK);
            }
        } catch (Exception $exception) {

            return $this->errorResponse($exception->getMessage());
            //throw $th;
        }
    }
}
