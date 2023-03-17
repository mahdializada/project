<?php

namespace App\Repositories\SingleSales;

use App\Models\Reason;
use App\Models\SingleSales\InventorySsp;
use Exception;
use App\Traits\FileTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use App\Models\SingleSales\Product;
use App\Models\SingleSales\ProductAttribute;
use App\Models\SingleSales\Supplier;
use App\Repositories\Files\CloudinaryFileUtils;
use Illuminate\Support\Facades\Validator;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use App\Models\Country;
/**
 * Class ProductRepository.
 */
class ProductRepository extends Repository
{

    public function paginate(Request $request)
    {
        if ($request->query->get("fetch_for_form")) {
            return $this->getForForm($request);
        }
        // echo "product repository"; exit;
        $key = $request->query->get("key");
        $queryBuilder = new UriQueryBuilder(new Product(), $request);
        $queryBuilder->setRelations($this->getRelations());
        $searchInColumns = ['id', 'code', 'name','arabic_name', 'arabic_description',  'is_published', 'parent_sku', 'pcode','quantity' ,'const_per_unit',  'created_at'];
        $queryBuilder->query    = $this->filterRecords($queryBuilder->query, $request, $searchInColumns);
        $statusQuery            = clone  $queryBuilder->query;
        // $customColumn = ['now,available,now', 'comming soon,available,comming_soon'];

        $queryBuilder->query    = $this->fetchDataAccordingToStatus($queryBuilder->query, $key );

        $queryBuilder->query->orderBy("created_at", 'desc');
        $queryBuilder         = $queryBuilder->build()->paginate()->getData();
        $extraData = [
            'allTotal',
            'removedTotal',
            // 'nowTotal,available,now',
            // 'commingSoonTotal,available,comming_soon',
            'activeTotal, status , active',
            'inactiveTotal, status, inactive'
        ];
        $queryBuilder = $this->getStatusCount($statusQuery, $queryBuilder, $extraData);
        return response()->json($queryBuilder);
    }
    private function getRelations(): array
    {
        return [
            'category:id,name',
            'supplier:id,supplier_name',
            "brand:id,name",
            "attachments",
            "productAttributes.attributes:id,name",
            "company",
            // "InventorySsp.productAttribute.attributes",
            // 'productAttributes.attributes'

        ];
    }
    public function filterProduct(array $columns)
    {
        $products = Product::where("status", "active")->select($columns)->get();
        return response()->json($products);
    }
    public function productByCateogry($category_id)
    {
        $columns = ["id", "name", "code"];
        $products = Product::where("status", "active")->where("category_id", $category_id)->select($columns)->get();
        return response()->json($products);
    }
    public function filterSuppliers(array $columns)
    {
        $suppliers = Supplier::select($columns)->get();
        return response()->json($suppliers);
    }
    public function store(Request $request)
    {
        // return $request;
        try {
            DB::beginTransaction();
            $data = [];
            foreach($request->products as $value){
                // return $value['link_value']['id'];
                $product = Product::create([
                    'code'=> rand(100000, 9999999999),
                    'parent_sku'=> $value['parent_sku'],
                    'type'=> $value['parent_sku'],
                    'pcode'=> $value['pcode'],
                    'name'=> $value['name'],
                    'description'=> $value['description'],
                    'arabic_name'=> $value['arabic_name'],
                    'arabic_description'=> $value['arabic_description'],
                    'quantity'=> $value['quantity'],
                    'cost_per_unit'=> $value['cost_per_unit'],
                    'category_id'=> $request->category_id,
                    'supplier_id'=> $request->supplier_id,
                    'created_by'=>  auth()->user()->id,
                ]);
                if($value['attribute'])
                ProductAttribute::create([
                    'product_id'=>$product->id,
                    'atrribute_id'=>$value['attribute'],
                    'type'=>'value_select'
                ]);
                if($value['text_value']['id'])
                    ProductAttribute::create([
                        'product_id'=>$product->id,
                        'atrribute_id'=>$value['text_value']['id'],
                        'attribute_value'=>$value['text_value']['value'],
                        'type'=>'text'
                    ]);
                if($value['number_value']['id'])
                    ProductAttribute::create([
                        'product_id'=>$product->id,
                        'atrribute_id'=>$value['number_value']['id'],
                        'attribute_value'=>$value['number_value']['value'],
                        'type'=>'number'
                    ]);
                if($value['link_value']['id'])
                    ProductAttribute::create([
                        'product_id'=>$product->id,
                        'atrribute_id'=>$value['link_value']['id'],
                        'attribute_value'=>$value['link_value']['value'],
                        'type'=>'link'
                    ]);
                $product->company()->sync($request->companyIds);
                // $storeFile =    CloudinaryFileUtils::storeFiles($product,$value['images']);
                //     if (!$storeFile['result']) {
                //         DB::rollBack();
                //         return response()->json($storeFile, 500);
                //     }
                $data[]=$product->load($this->getRelations())->refresh();
            }
            DB::commit();
            return response()->json(['result' => true, 'product' => $data], Response::HTTP_CREATED);
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->getMessage());
        }
    }
    public function update(Request $request, $id)
    {

        try {
            DB::beginTransaction();
            $property =  $request->property;
            if($request->editType == "updateFile"){
                $product = Product::find($id);
                CloudinaryFileUtils::updateFiles($product, $id, $request->path);
                DB::commit();
            return response()->json(['result' => true, 'updateData' => $product->load($this->getRelations())], Response::HTTP_OK);

            }else if ($request->editType == 'inventory') {
                $inventroy =  InventorySsp::find($id);
                $inventroy->update([$property => $request->value]);
                DB::commit();
                return response()->json(['result' => true, 'data' => $inventroy->refresh()], Response::HTTP_OK);
            } else {
                $product = Product::find($id);
                if ($property == 'category') {
                    $property = 'category_id';
                }
                if ($property == 'brand') {
                    $property = 'brand_id';
                }
                $product->update([$property => $request->value]);
                DB::commit();
                return response()->json(['result' => true, 'data' => $product->load($this->getRelations())], Response::HTTP_OK);
            }
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->getMessage());
        }
    }
    public function search(Request $request)
    {
        $queryBuilder = new UriQueryBuilder(new Product(), $request);
        $queryBuilder = $this->searchCode($queryBuilder->query, $request, $this->getRelations());
        return $queryBuilder ? response()->json($queryBuilder, 200) : response()->json(["result" => false], Response::HTTP_NO_CONTENT);
    }
    public function destroy($ids, $reason_ids)
    {
        try {
            //code...
            DB::beginTransaction();
            $products = Product::withTrashed()->whereIn('id', $ids)->get();
            $reasons = Reason::whereIn('id', $reason_ids)->get();
            // ADD REASON
            foreach ($products as $product) {
                if (!$product->trashed()) {
                    foreach ($reasons as $reason) {
                        $product->reasons()->syncWithoutDetaching($reason);
                    }
                    $product->delete();
                } else {
                    $this->deleteFile($product->getRawOriginal('product_img'));
                    $product->reasons()->detach();
                    $product->forceDelete();
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
            'parent_sku' => 'required',
            'pcode' => 'required',
            'name' => 'required',
            'type' => 'required',
            'description' => 'required',
            'VAT_tax' => 'required',
            'unit' => 'required',
            'available' => 'required',
            'number_of_sales' => 'required',
            "images" => ["required", "array"],
            "category_id" => ["required", "uuid"],
            "supplier_id" => ["required", "uuid"],
            "brand_id" => ["required", "uuid"],
        ];
    }

    public function updateRules(): array
    {
        return [];
    }

    public function changeProductsStatus(Request $request)
    {
        $this->statusValidations($request);
        // return "some change status";
        try {
            DB::beginTransaction();
            $itemIds = $request->input('item_ids');
            $noReasonTabs = array_map('trim', explode(',', $request->input('no_reason_tabs')));
            $noReasonOperations = array_map('trim', explode(',', $request->input('no_reason_operations')));
            $newStatus = $request->input('status');
            $reasons = $request->input('reasons');
            $boradcasetData = [];
            foreach ($itemIds as $id) {
                $item = Product::withTrashed()->findOrFail($id);
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
        try {
            DB::beginTransaction();

            $type               = $request->input('type');
            $itemIds            = $request->input('item_ids');
            $statusForRestore   = $request->input('status');

            foreach ($itemIds as $id) {
                $item           = Product::withTrashed()->find($id);
                $newStatus      = $request->input('status');
                $data = [];

                if ($statusForRestore == "restore") {
                    $item->restore();
                } else {

                    if ($newStatus == "active") {
                        $data = ['status' => $newStatus];
                    } elseif ($newStatus == "inactive") {
                        $data = ['status' => $newStatus];
                    }
                }
            }
            DB::commit();
            return response()->json(['result' => true], Response::HTTP_OK);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
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
    public function getProductCountry()
    {
        $columns = ["id", "name", "code", "iso2"];
        // $countries = Country::where(['status' => 'active', 'advertisement_status' => "active"])->whereHas("companies")->select($columns)->get();
        // return $countries;
        return Country::whereHas("companies")->select($columns)->get();
        // $countries = Country::where(['status' => 'active', 'advertisement_status' => "active"])->whereHas("companies")->select($columns)->get();

    }
    private function getForForm(Request $request)
    {
        $data = [];
        if ($request->query->get('category_id')) {
            $data = Product::where('category_id', $request->query->get('category_id'))
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get();
        }
        return response()->json($data);
    }
    public function inventoryInsertion(Request $request)
    {
        if ($request->product_id) {

            try {
                DB::beginTransaction();
                foreach ($request->inventory  as $key => $inventroy) {
                    $inv = [
                        'product_id' => $request->product_id,
                        'sku' => $inventroy['sku'],
                        'quantity' => $inventroy['quantity'],
                        'price_per_unit' => $inventroy['price_per_unit'],
                    ];
                    $invent = InventorySsp::create($inv);
                    if ($inventroy['attributes']) {
                        foreach ($inventroy['attributes'] as $key => $value) {
                            $attr = [
                                'product_id' => $request->product_id,
                                'attribute_id' => $value['attribute_id'],
                                'product_inventory_id' => $invent->id,
                                'attribute_value' => json_encode($value['value']),
                            ];
                            ProductAttribute::create($attr);
                        }
                    }
                }
                DB::commit();
                return response()->json( $invent, Response::HTTP_CREATED);
            } catch (\Throwable $th) {
                DB::rollBack();
                return response()->json($th->getMessage(), 500);
            }
        }
    }
}
