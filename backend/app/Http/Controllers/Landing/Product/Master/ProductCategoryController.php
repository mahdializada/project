<?php

/**
 * @author [Ahmad Reza Azimi]
 * @email [teebalhoor@info.org]
 * @create date 2022-05-26 10:56:36
 * @modify date 2022-05-26 10:56:36
 * @desc [description]
 * all reserved for Teeb-al-Hoor Commercial Broker
 */

namespace App\Http\Controllers\Landing\Product\Master;

use Illuminate\Http\Request;
use App\Models\Landing\Master\Product\ProductCategory;
use App\Http\Controllers\Landing\RootController;
use App\Repositories\Landing\Product\Master\ProductRepository;

class ProductCategoryController extends RootController
{
    protected $repository;
    protected $filePath = 'product/category/icon';

    public function __construct()
    {
        $this->repository = new ProductRepository(new ProductCategory(), $this->filePath);
    }

    public function index(Request $request)
    {
        return $this->repository->paginate($request);
    }

    public function show(Request $request, $id)
    {
        return $this->repository->show($request, $id);
    }

    public function update(Request $request, $id){
        return $this->repository->update($request, $id);
    }

    public function store(Request $request)
    {
        return $this->repository->store($request);
    }
    // search
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
