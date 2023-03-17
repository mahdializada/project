<?php

namespace App\Http\Controllers;

use App\Models\ProductManagement\ProductList;
use Illuminate\Http\Request;
use App\Repositories\ProductListRepository;

class ProductListController extends Controller
{
    private $repository;
    public function __construct(){
        $this->repository = new ProductListRepository();
        $this->middleware('scope:prlv')->only(['index', 'show']);
        $this->middleware('scope:prlc')->only(['store']);
        $this->middleware('scope:prlu')->only(['update']);
        $this->middleware('scope:prld')->only(['destroy']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->repository->pagenate($request);
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
     * @param  \App\Models\ProductList  $productList
     * @return \Illuminate\Http\Response
     */
    public function show(ProductList $productList)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProductList  $productList
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        return $this->repository->update($id,$request);
    }

    public function changeProductSupplier(Request $request){
        return $this->repository->changeProductSupplier($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProductList  $productList
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductList $productList)
    {
        //
    }
}
