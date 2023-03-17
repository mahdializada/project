<?php

namespace App\Http\Controllers;

use ActivityLog;
use App\Repositories\LanguageRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    private $repository;


    public function __construct()
    {
        $this->repository = new LanguageRepository();

        $this->middleware('scope:cnv')->only(['show']);
        $this->middleware('scope:cnc')->only(['store']);
        $this->middleware('scope:cnu')->only(['update']);
        $this->middleware('scope:cnd')->only(['destroy']);

        $this->middleware('dailylogs:masters,language,insert')->only(['store']);
        $this->middleware('dailylogs:masters,language,update')->only(['update']);
        $this->middleware('dailylogs:masters,language,delete')->only(['destroy']);
        // $this->middleware('dailylogs:masters,language,change status')->only(['changeLanguageStatus']);
    }


    // Display a listing of the resource.

    public function index(Request $request): JsonResponse
    {
        return $this->repository->paginate($request);
    }



    // Store a newly created resource in storage.

    public function store(Request $request): JsonResponse
    {
        $request->validate($this->repository->storeRules());
        return $this->repository->store($request);
    }


    // Update the specified resource in storage.

    public function update(Request $request): JsonResponse
    {
        $this->repository->updateRules($request);
        return $this->repository->update($request);
    }


    // Return the specified resource if exists

    public function show($id): JsonResponse
    {
        return $this->repository->show($id);
    }


    // Remove the specified resource from storage.

    public function destroy(Request $request): JsonResponse
    {
        return $this->repository->destroy($request->all());
    }
}
