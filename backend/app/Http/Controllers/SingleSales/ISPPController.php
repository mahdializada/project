<?php

namespace App\Http\Controllers\SingleSales;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Repositories\SingleSales\ISPPRepository;
use Illuminate\Http\Request;

class ISPPController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {

        $this->repository = new ISPPRepository();
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
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
        //
        $ids        = explode(",", $ids);
        $reason_ids    = explode(',', $request->get('reasons'));
        return $this->repository->destroy($ids, $reason_ids);
    }

    public function searchItem(Request  $request): JsonResponse
    {
        return $this->repository->search($request);
    }

    public function changeStatus(Request  $request): JsonResponse
    {
        return $this->repository->changeIsppStatus($request);
    }
}
