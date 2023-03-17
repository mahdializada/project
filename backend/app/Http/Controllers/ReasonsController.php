<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Reason;
use App\Models\ReasonSubSystem;
use App\Models\System;
use App\Models\SubSystem;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Repositories\ReasonRepository;
use Illuminate\Database\Eloquent\Builder;

class ReasonsController extends Controller
{
    private $repository;

    public function __construct()
    {
        $this->repository = new ReasonRepository();
        // $this->middleware('scope:rnv')->only(['index', 'show']);
        // $this->middleware('scope:rnc')->only(['store']);
        // $this->middleware('scope:rnu')->only(['update']);
        // $this->middleware('scope:rnd')->only(['destroy']);
    }
    public function findScope($subSystem)
    {
        $allScope1 = [
            'user' => ['rnud', 'rnuu', 'rnuv', 'rnuc', 'rnue'],
            'master' => ['rnmd', 'rnmu', 'rnmv', 'rnmc', 'rnme'],
            'single_sale' => ['rnsd', 'rnsu', 'rnsv', 'rnsc', 'rnse'],
            'advertisement' => ['rnad', 'rnau', 'rnav', 'rnac', 'rnae'],
            'online_sales' => ['rnosd', 'rnosu', 'rnosv', 'rnosc', 'rnose'],
            'Product' => ['rnprd', 'rnpru', 'rnprv', 'rnprc', 'rnpre'],
            'supplier' => ['rnsupd', 'rnsupu', 'rnsupv', 'rnsupc', 'rnsupe'],
            'content_library' => ['rnad', 'rnau', 'rnav', 'rnac', 'rnae'],
            // 'content_library' => ['rncld', 'rnclu', 'rnclv', 'rnclc', 'rncle'],

        ];
        if (array_key_exists($subSystem, $allScope1)) {
            $ressult = $allScope1[$subSystem];
            return $ressult;
        }
        return false;
    }
    public function index(Request $request)
    {
        // $scope = 'rn' . substr($request->input('slug'), 0, 1) . 'v';
        $scope = $this->findScope($request->input('slug'));
        if ($scope && $this->tokenCan($scope[2]))
            return $this->repository->paginate($request);
    }

    public function fetchReasonAccordingToSystemEvent(Request $request)
    {
        // $scope = 'rn' . substr($request->input('slug'), 0, 1) . 'v';
        $scope = $this->findScope($request->input('slug'));
        if ($scope && $this->tokenCan($scope[2]))
            return $this->repository->fetchReasonAccordingToSystemEvent($request);
        else throw new Exception("You are not authorized for this operation", 1);
    }
    public function store(Request $request)
    {
        // $scope = 'rn' . substr($request->input('slug'), 0, 1) . 'c';
        $scope = $this->findScope($request->input('slug'));
        if ($scope && $this->tokenCan($scope[3])) {
            $request->validate($this->repository->storeRules());
            return $this->repository->store($request);
        } else throw new Exception("You are not authorized for this operation", 1);
    }

    public function show(Request $request, $id)
    {
        return $this->repository->show($request);
    }

    public function update(Request $request, $id)
    {
        // $scope = 'rn' . substr($request->input('slug'), 0, 1) . 'u';
        $scope = $this->findScope($request->input('slug'));
        if ($scope && $this->tokenCan($scope[1])) {
            $this->repository->updateRules($request);
            return $this->repository->update($request);
        } else throw new Exception("You are not authorized for this operation", 1);
    }
    public function searchReason(Request $request)
    {
        // $scope = 'rn' . substr($request->input('slug'), 0, 1) . 'v';
        $scope = $this->findScope($request->input('slug'));
        if ($scope && $this->tokenCan($scope[3]))
            return $this->repository->search($request);
        else throw new Exception("You are not authorized for this operation", 1);
    }


    public function destroy(Request $request, $id)
    {
        // $scope = 'rn' . substr($request->input('slug'), 0, 1) . 'd';
        // $scope = $this->findScope($request->input('slug'));
        // if ($scope && $this->tokenCan($scope[0]))
        $result = Reason::findOrFail($id)->forceDelete();
        return $result;
        // } else throw new Exception("You are not authorized for this operation", 1);
    }
    public function assignReason(Request $request)
    {
        return $this->repository->assignReason($request);
    }
    public function reasonHistory(Request $request)
    {
        return $this->repository->reasonHistory($request);
    }

    public function reasonStatusEvent(Request $request)
    {

        $ReasonIdExist = ReasonSubSystem::where('sub_system_id', $request->subsystem)->whereIn('type', $request->selectedTypes)->pluck("reason_id");
        $query = Reason::whereHas('system.subSystems', function (Builder $builder) use ($request) {
            $builder->where('sub_systems.id', $request->subsystem);
        });
        // return $ReasonIdExist;
        if (count($ReasonIdExist) > 0) {
            $query = Reason::whereHas('system.subSystems', function (Builder $builder) use ($request) {
                $builder->where('sub_systems.id', $request->subsystem);
            })->whereNotIn('id', $ReasonIdExist)->get();
        } else {
            $query = Reason::whereHas('system.subSystems', function (Builder $builder) use ($request) {
                $builder->where('sub_systems.id', $request->subsystem);
            })->get();
        }
        return $query;
    }
    private function getRelations(): array
    {
        return [
            'system',
        ];
    }
}
