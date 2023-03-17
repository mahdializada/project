<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\ActivityRepository;
use Illuminate\Http\JsonResponse;

class ActivityController extends Controller
{
    private $repository;

    public function __construct()
    {
        $this->repository = new ActivityRepository();
        // $this->middleware('scope:activity-view')->only(['index']);
        // $this->middleware('scope:activity-create')->only(['store', 'exportCompanyTemplate']);
        // $this->middleware('scope:activity-update')->only(['update', 'restore', 'updateCompany']);
        // $this->middleware('scope:activity-delete')->only(['destroy']);

        // // log middlware
        // $this->middleware('dailylogs:hr,activity,insert')->only(['store']);
        // $this->middleware('dailylogs:hr,activity,update')->only(['updateactivity']);
        // $this->middleware('dailylogs:hr,activity,delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): JsonResponse
    {
        return $this->repository->paginate($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate($this->repository->storeRules());
        return $this->repository->store($request);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
