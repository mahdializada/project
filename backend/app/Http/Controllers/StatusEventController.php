<?php

namespace App\Http\Controllers;

use App\Models\Reason;
use App\Models\SubSystem;
use App\Models\System;
use App\Models\ReasonType;
use App\Repositories\StatusEventRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class StatusEventController extends Controller
{
    private $repository;
    public function __construct()
    {
        $this->repository = new StatusEventRepository();
        // $this->middleware('dailylogs:status_management,status_event,insert')->only(['store']);
        // $this->middleware('dailylogs:status_management,status_event,update')->only(['update']);
        // $this->middleware('dailylogs:status_management,status_event,delete')->only(['destroy']);
        // $this->middleware('dailylogs:status_management,status_event,change status')->only(['changeDepartmentStatus']);
    }

    public function findScope($subSystem)
    {
        $allScope1 = [
            'user' => ['seud', 'seuu', 'seuv', 'seuc', 'seue'],
            'master' => ['semd', 'semu', 'semv', 'semc', 'seme'],
            'single_sale' => ['sesd', 'sesu', 'sesv', 'sesc', 'sese'],
            'advertisement' => ['sead', 'seau', 'seav', 'seac', 'seae'],
            'online_sales' => ['seosd', 'seosu', 'seosv', 'seosc', 'seose'],
            'Product' => ['seprd', 'sepru', 'seprv', 'seprc', 'sepre'],
            'supplier' => ['sesupd', 'sesupu', 'sesupv', 'sesupc', 'sesupe'],
            'content_library' => ['sead', 'seau', 'seav', 'seac', 'seae'],
            // 'content_library' => ['secld', 'seclu', 'seclv', 'seclc', 'secle'],

        ];
        if (array_key_exists($subSystem, $allScope1)) {
            $ressult = $allScope1[$subSystem];
            return $ressult;
        }
        return false;
    }

    public function index(Request $request)
    {
        // $scope = 'se' . substr($request->input('slug'), 0, 1) . 'v';
        $scope = $this->findScope($request->input('slug'));
        if ($scope && $this->tokenCan(strtolower($scope[2])))
            return $this->repository->paginate($request);
    }



    public function store(Request $request)
    {

        // $scope = 'se' . substr($request->input('slug'), 0, 1) . 'c';
        $scope = $this->findScope($request->input('slug'));
        if ($scope &&  $this->tokenCan(strtolower($scope[3]))) {
            $request->validate($this->repository->storeRules());
            return $this->repository->store($request);
        }
    }

    public function show(Request $request, $id)
    {
        // $scope = 'se' . substr($request->input('slug'), 0, 1) . 'v';
        $scope = $this->findScope($request->input('slug'));
        if ($scope && $this->tokenCan(strtolower($scope[2]))) {
            return $this->repository->show($request);
            $sub_system_id = SubSystem::where('name',  'LIKE', '%' . $request->query->get('sub_system') . '%')->first();
            if (!$sub_system_id) {
                return response()->json(["result" => false, 'reasons' => []], Response::HTTP_NOT_FOUND);
            }
            $system  = System::where('name', 'LIKE', '%' . $request->query->get('system_name') . '%')->first();
            if (!$system) {
                return response()->json(["result" => false, 'reasons' => []], Response::HTTP_NOT_FOUND);
            }
            $reasons = Reason::where('system_id', $system->id)->join('reason_types', 'reasons.id', '=', 'reason_types.reason_id')
                ->join('reason_sub_system', 'reasons.id', '=', 'reason_sub_system.reason_id')
                ->where(['type' => $request->query->get('type'), 'sub_system_id' => $sub_system_id->id])->get();
            return response()->json(['reasons' => $reasons], Response::HTTP_OK);
        }
    }

    public function update(Request $request, $id)
    {
        // $scope = 'se' . substr($request->input('slug'), 0, 1) . 'u';
        $scope = $this->findScope($request->input('slug'));
        if ($scope && $this->tokenCan(strtolower($scope[1]))) {
            $this->repository->updateRules($request);
            return $this->repository->update($request);
        }
    }


    public function destroy($id)
    {
        //
    }

    public function storeValidation($request)
    {
        $request->validate([
            'title'         => 'required|array',
            'type'         => 'required|array',
            'subsystem'    => 'required|array'
        ]);
    }
    public function updateValidation($request)
    {
        $request->validate([
            'reason'       => 'required|array',
            'type'         => 'required|array',
            'subSystem'    => 'required|array'
        ]);
    }
    public function search(Request $request)
    {
        return $this->repository->search($request);
    }
}
