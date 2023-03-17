<?php

namespace App\Http\Controllers\SingleSales;

use App\Http\Controllers\Controller;
use App\Repositories\SingleSales\ProductStudyRepository;
use Illuminate\Http\Request;

class ProductStudyController extends Controller
{
    private $repository;
    public function __construct()
    {
        $this->repository = new ProductStudyRepository();
    }

    public function index(Request $request)
    {
        return  $this->repository->paginate($request);
    }

    public function store(Request $request)
    {

        $request->validate([
            'product_id'=>["required"],
            'study_language_id'=>["required"],
            'study_reason'=>["required"],
        ]);
        return $this->repository->store($request);
    }

    public function show(Request $request)
    {
        if ($request->get('languages') || $request->products) {
            $type = $request->products ? "products" : "languages";
            return $this->repository->filterData($request->order, $type);
        }
        return $this->repository->show($request);
    }

    public function update(Request $request, $id)
    {
     
        return $this->repository->update($request);
    }

    public function destroy(Request $request, $ids)
    {
        $ids        = explode(",", $ids);
        $reason_ids    = explode(',', $request->get('reasons'));
        return $this->repository->destroy($ids, $reason_ids);
    }
    public function changeStatus(Request $request)
    {
        return $this->repository->changeProductStudyStatus($request);
    }

    public function assignUser(Request $request){
        if ($request->type === "unassign") {
            return  $this->repository->unAssign($request);
        }
        return $this->repository->assign($request);
    }
}
