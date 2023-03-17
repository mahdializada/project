<?php

namespace App\Repositories\ProductManagement;

use App\Models\ProductManagement\Product;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use App\Repositories\Repository;
use App\Traits\FileTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Class ProductRepository.
 */
class ProductRepository extends Repository
{
    use FileTrait;

    public function paginate(Request $request)
    {
        $key = $request->query->get("key");
        return $this->getProductsAccordingToStatus($request, $key);
    }
    private function getProductsAccordingToStatus($request, $key): JsonResponse
    {
        $queryBuilder = new UriQueryBuilder(new Product(), $request);
        $queryBuilder->setRelations($this->getRelations());
        $queryBuilder->setWithTrashed();

        $searchInColumns = [
            'sku', 'pcode', 'name', 'unit', 'description'
        ];
        $queryBuilder->query = $this->filterRecords($queryBuilder->query, $request, $searchInColumns);
        $statusQuery = clone $queryBuilder->query;
        $queryBuilder->query = $this->fetchDataAccordingToStatus($queryBuilder->query, $key, [], 'available');

        $userData = $queryBuilder->build()->paginate()->getData();
        $extraData = [
            'allTotal',
            'availableTotal, available, now',
            'commingSoonTotal, available, comming_soon',
            'removedTotal'
        ];
        $userData = $this->getStatusCount($statusQuery, $userData, $extraData, true, "available");
        return response()->json($userData);
    }


    private function getRelations(): array
    {

        return [];
    }



    public function images(Request $request)
    {
        $imagePath = "";
        try {
            if ($request->file_path) {
                $this->deleteFile($request->file_path);
                return response()->json(['result' => true]);
            }
            $request->validate($this->imagesRules());
            $this->prefix = '/product-management/pr-products/images';
            $image = $request->file('image');
            $imagePath = $this->storeFile($image);
            return response()->json(['result' => true, 'path' => $imagePath]);
        } catch (Exception $exception) {
            if ($imagePath) {
                $this->deleteFile($imagePath);
            }
        }
    }

    public function imagesRules(): array
    {
        return [
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg'],
        ];
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $data = [];
        $data['sku']            = $request->sku;
        $data['type']           = $request->type;
        $data['name']           = $request->name;
        $data['available']      = $request->available;
        $data['description']    = $request->description;
        $data['unit']           = $request->unit;
        $data['brand_id']       = $request->brand_id;
        $data['category_id']    = $request->category_id;
        $data['created_by']     = $user->id;
        $product                = Product::create($data);
        foreach ($request->images as $image) {
            $product->attachments()->create([
                'path' => $image,
                'file_type' => pathinfo($image, PATHINFO_EXTENSION)
            ]);

        }
        return response()->json(['result' => true, 'data' => $product->load('attachments')]);
    }

    public function storeRules(): array
    {
        return [
            'sku' => ['required'],
            'type' => ['required'],
            'name' => ['required'],
            'available' => ['required'],
            'description' =>  ['required'],
            'unit' =>  ['required'],
            'images' =>  ['required'],
            'brand_id' =>  ['required', "exists:pdm_brands,id", "uuid"],
            'category_id' =>  ['required', "exists:pdm_categories,id", "uuid"],
        ];
    }

    public function update(Request $request, $id)
    {
    }
    public function search(Request $request)
    {
    }
    public function destroy($ids, $reason_ids)
    {
    }



    public function updateRules(): array
    {
        return [];
    }

    public function checkSku(Request $request)
    {
        $res = Product::where('sku', $request->query('sku'))->first();
        return response()->json(['result' => $res ? true : false]);
    }
}
