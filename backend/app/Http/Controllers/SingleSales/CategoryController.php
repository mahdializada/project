<?php

namespace App\Http\Controllers\SingleSales;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Repositories\SingleSales\CategoryRepository;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

        private $repository;
    public function __construct()
    {

        $this->repository = new CategoryRepository();
    }

    public function index(Request $request)
    {
        return  $this->repository->paginate($request);
    }

    public function store(Request $request)
    {
        if($request->id){
            return $this->update($request);
        }
        $request->validate($this->repository->storeRules());
        return $this->repository->store($request);
    }

    public function show(Request $request)
    {
        if($request->filter_category){
            return $this->repository->filterCategory();
        }
    }

    public function update(Request $request)
    {

          $request->validate($this->repository->updateRules());
        return $this->repository->update($request);
    }

    public function searchItem(Request  $request): JsonResponse
    {
        return $this->repository->search($request);
    }

    public function changeCategoriesStatus(Request $request)
    {
        return $this->repository->changeCategoriesStatus($request);
    }

    public function destroy(Request $request, $ids)
    {
        $ids        = explode(",", $ids);
        $reason_ids    = explode(',', $request->get('reasons'));
        return $this->repository->destroy($ids, $reason_ids);
    }

    public function categoryAttributes(Request $request)
    {
        return $this->repository->categoryAttributes($request);
    }
}
