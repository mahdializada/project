<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Repositories\SingleSales\AttributeRepository;




class AttributeController extends Controller
{
    private $repository;
    public function __construct()
    {

        $this->repository = new AttributeRepository();
    }

    public function index(Request $request)
    {
        $repository = new AttributeRepository();
        return  $repository->paginate($request);
    }

    public function store(Request $request)
    {
        if($request->id){
            return $this->update($request);
        }
        $request->validate($this->repository->storeRules());
        return  $this->repository->store($request);
    }

    public function show($id)
    {
        //
    }

    public function update(Request $request)
    {

        $request->validate($this->repository->storeRules());
        return  $this->repository->update($request);
    }

    public function destroy(Request $request, $ids)
    {
        $ids        = explode(",", $ids);
        $reason_ids    = explode(',', $request->get('reasons'));
        return $this->repository->destroy($ids, $reason_ids);
    }

    public function searchItem(Request  $request): JsonResponse
    {
        return $this->repository->search($request);
    }
    public function changeAttributesStatus(Request $request)
    {
        // return response()->json($request);
        return $this->repository->changeAttributesStatus($request);
    }
}
