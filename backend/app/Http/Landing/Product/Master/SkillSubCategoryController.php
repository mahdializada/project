<?php

namespace App\Http\Controllers\Landing\Product\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Landing\RootController;
use App\Models\Landing\Master\Product\Skill\SkillSubCategory;
use App\Repositories\Landing\Product\Master\SubCategory\ProductRepository;

class SkillSubCategoryController extends RootController
{
    protected $repository;

    public function __construct()
    {
        $this->repository = new ProductRepository(new SkillSubCategory());
    }

    public function index(Request $request){
        return $this->repository->paginate($request);
    }

    public function search(Request $request)
    {
        return $this->repository->search($request);
    }

    public function changeStatus(Request $request)
    {
        return  $this->repository->changeSubCategoriesStatus($request);
    }

    public function destroy(Request $request, $id)
    {
        return $this->repository->changedRemovedStatus($id);
    }
}
