<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Repositories\RequestStorageRepository;


class RequestStorageController extends Controller
{
    private $repository;

    public function __construct()
    {
        $this->repository = new RequestStorageRepository();
    }

    public function index(Request $request): JsonResponse
    {
        return $this->repository->paginate($request);
    }

    public function store(Request $request)
    {
        return $this->repository->store($request);
    }

    public function changeRequestStorageStatus(Request $request)
    {
        return $this->repository->changeRequestStorageStatus($request);
    }

    public function searchItem(Request  $request): JsonResponse
    {
        return $this->repository->search($request);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function destroy($id)
    {
        return $this->repository->destroy($id);
    }
}
