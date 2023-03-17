<?php

namespace App\Http\Controllers\SingleSales;

use App\Http\Controllers\Controller;
use App\Repositories\SingleSales\StudyRequestRepository;
use Illuminate\Http\Request;

class StudyRequestController extends Controller
{
    private $repository;
    public function __construct()
    {
        $this->repository = new StudyRequestRepository();
    }

    public function index(Request $request)
    {
        return $this->repository->paginate($request);
    }

    public function store(Request $request)
    {
        $attributes = $request->validate($this->repository->rules());
        return $this->repository->storeOrUpdate($attributes);
    }

    public function update(Request $request, $id)
    {
        $attributes = $request->validate($this->repository->rules());
        return $this->repository->storeOrUpdate($attributes, $id);
    }

    public function destroy(Request $request, $ids)
    {
    }
}
