<?php

namespace App\Http\Controllers\ProductManagement;

use App\Http\Controllers\Controller;
use App\Repositories\ProductManagement\BrandRepository;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $repository;

    public  function __construct()
    {
        $this->repository = new BrandRepository();
    }
    public function index(Request $request)
    {
        //
        return $this->repository->paginate($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->id){
            return $this->update($request);
        }
        $request->validate($this->repository->storeRules());
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
        if ($request->filter_brand) {
            return $this->repository->filterBrands(["id", "code", "name"]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //
        $request->validate($this->repository->updateRules());
        return $this->repository->update($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $ids)
    {
        $ids        = explode(",", $ids);
        $reason_ids    = explode(',', $request->get('reasons'));
        return $this->repository->destroy($ids, $reason_ids);
    }
    public function searchItem(Request  $request)
    {
        return $this->repository->search($request);
    }
    public function changeBrandStatus(Request $request)
    {
        return $this->repository->changeBrandStatus($request);
    }
}
