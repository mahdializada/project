<?php

namespace App\Http\Controllers\SingleSales;

use App\Http\Controllers\Controller;
use App\Models\SingleSales\Ipa;
use App\Repositories\SingleSales\Ipa\IpaRepository;
use Illuminate\Http\Request;

class IPAController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {

        $this->repository = new IpaRepository();
    }

    public function index(Request $request)
    {
        return $this->repository->paginate($request);
    }


    public function store(Request $request)
    {
        //
        $request->validate($this->repository->storeRules());
        return $this->repository->store($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        //
        return $this->repository->show($request);
    }


    public function update(Request $request, $id)
    {
        //
        $request->validate($this->repository->storeRules());
        return $this->repository->update($request);
    }


    public function destroy(Request $request, $ids)
    {
        //
        $ids        = explode(",", $ids);
        $reason_ids    = explode(',', $request->get('reasons'));
        return $this->repository->destroy($ids, $reason_ids);
    }

    public function changeStatus(Request  $request){
        return $this->repository->changeIpaStatus($request);
    }
    public function searchIpa(Request  $request){
        return $this->repository->search($request);
    }
}
