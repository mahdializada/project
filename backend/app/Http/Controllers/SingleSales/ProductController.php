<?php

namespace App\Http\Controllers\SingleSales;

use App\Http\Controllers\Controller;
use App\Models\SingleSales\InventorySsp;
use App\Models\SingleSales\Product;
use Illuminate\Http\Request;
use App\Repositories\SingleSales\ProductRepository;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $repository;

    public function __construct()
    {
        $this->repository = new ProductRepository();
    }
    public function index(Request $request)
    {
        return  $this->repository->paginate($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $request->validate($this->repository->storeRules());
        return $this->repository->store($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        if ($request->filter_product) {
            return $this->repository->filterProduct(["id", "code", "name"]);
        }
        if ($request->category_id) {
            return $this->repository->productByCateogry($request->category_id);
        }
        if ($request->filter_supplier) {
            return $this->repository->filterSuppliers(["id", "code"]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return $this->repository->update($request, $id);
    }

    public function search(Request $request)
    {
        return $this->repository->search($request);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $ids)
    {
        if ($request->type == 'inventory') {
            return  InventorySsp::find($ids)->destroy();
        }

        $ids        = explode(",", $ids);
        $reason_ids    = explode(',', $request->get('reasons'));
        return $this->repository->destroy($ids, $reason_ids);
    }

    public function changeProductsStatus(Request $request)
    {
        return $this->repository->changeProductsStatus($request);
    }
    public function inventoryInsertion(Request $request)
    {
        return $this->repository->inventoryInsertion($request);
    }
    function getProductCountry(){
        return $this->repository->getProductCountry();
     }
    public function lastItemCode(){
        try {
            // $product = new Product();
            $product_code = Product::latest()->first();
            // return $product_code;
            $final = '';
            if($product_code){
                $text = substr($product_code->pcode,0,2);
                $code = substr($product_code->pcode,2);
                if($code==99){
                    $text++;
                    $code = 1;
                }else{
                    $code++;
                }
                $final = $text.$code;
            }else{
                $final = 'PL1';
            }
            return $final;
        } catch (\Throwable $th) {
            return response()->json($th->getMessage());
        }
    }
    public function generateSKU( Request $request){
        $product = new Product();
        $attributes         = $request->only($product->getFillable());
        $attributes['sku'] = rand(100000, 999999);
        return $attributes['sku'] ;
    }
}
