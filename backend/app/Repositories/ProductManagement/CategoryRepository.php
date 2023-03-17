<?php

namespace App\Repositories\ProductManagement;

use App\Models\ProductManagement\Category;
use App\Models\ProductManagement\Product;
use Illuminate\Http\Request;
use App\Repositories\Repository;
use Illuminate\Http\JsonResponse;

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
        return response()->json(['haha']);
    }



    public function storeValidations()
    {
        return [
            "itemType" => ["required", "in:category,sub_category"],
            "name" => ["required", "min:2", "max:150"],
            // "icon" => ["required", "file"],
            "attribute" => ["required", "array"],
            "attribute.*" => ["required", "exists:pdm_attributes,id"],
            "newAttribute" => ["nullable", "array"],
        ];
    }

    public function store(Request $request)
    {
        $attribute_ids = $request->attribute;
        $new_attribute_ids = $this->storeAttributes($request->newAttribute);
        dd($request->all());
    }


    private function storeAttributes($attributes)
    {
        $ids = [];
        if ($attributes) {
            dd($attributes);
        }
        return $ids;
    }


    public function update(Request $request)
    {
    }

    public function destroy($ids, $reason_ids)
    {
    }


    private function getRelations(): array
    {
        return [];
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
}
