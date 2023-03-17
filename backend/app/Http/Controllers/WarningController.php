<?php

namespace App\Http\Controllers;

use App\Repositories\UserWarningRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WarningController extends Controller
{
    private $repository;

    public function __construct()
    {
        $this->repository = new UserWarningRepository();
        $this->middleware('scope:users-veiw')->only(['index', 'show']);
        $this->middleware('scope:uc')->only(['store']);
        $this->middleware('scope:uu')->only(['update']);
        $this->middleware('scope:ud')->only(['destroy']);
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
