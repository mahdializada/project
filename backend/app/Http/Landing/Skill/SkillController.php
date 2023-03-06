<?php

namespace App\Http\Controllers\Landing\Skill;

use Illuminate\Http\Request;
use App\Http\Controllers\Landing\RootController;
use App\Models\Landing\Skill;
use App\Repositories\Landing\Skill\SkillRepository;

class SkillController extends RootController
{
    protected $repository;

    public function __construct()
    {
        $this->repository = new SkillRepository(new Skill());
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
        return $this->repository->ItemsSearch($request);
    }
 
    public function changeStatus(Request $request)
    {
        return  $this->repository->changeItemsStatus($request);
    }

    public function destroy(Request $request, $id)
    {
        return $this->repository->changedRemovedStatus($id);
    }



}
