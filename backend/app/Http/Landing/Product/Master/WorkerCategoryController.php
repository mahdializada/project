<?php

namespace App\Http\Controllers\Landing\Product\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Landing\RootController;
use App\Repositories\Landing\Product\Master\ProductRepository;
use App\Models\Landing\Master\Worker\WorkerCategory;


class WorkerCategoryController extends RootController
{
    protected $repository;
    protected $filePath = 'product/worker-category/icon';

    public function __construct()
    {
        $this->repository = new ProductRepository(new WorkerCategory(), $this->filePath);
    }

    public function index(Request $request)
    {
        return $this->repository->paginate($request);
    }

    public function show(Request $request, $id)
    {
        return $this->repository->show($request, $id);
    }

    public function store(Request $request)
    {
        return $this->repository->store($request);
    }

    public function search(Request $request)
    {
        return $this->repository->search($request);
    }

    public function changeStatus(Request $request)
    {
        return  $this->repository->changeCategoriesStatus($request);
    }

    public function destroy(Request $request, $id)
    {
        return $this->repository->changedRemovedStatus($id);
    }

    public function fetchAllCategories(Request $request)
    {
        return $this->repository->fetchAllCategories($request);
    }
}
