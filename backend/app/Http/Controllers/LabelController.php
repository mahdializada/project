<?php

namespace App\Http\Controllers;

use App\Models\Advertisement\Labelable;
use App\Repositories\LabelRepository;
use Illuminate\Http\JsonResponse;
use App\Models\Label;
use App\Models\LabelCategory;
use App\Models\SubSystem;
use App\Models\System;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use PhpParser\JsonDecoder;

class LabelController extends Controller
{

    private $repository;

    public function __construct()
    {
        $this->repository = new LabelRepository();

        // $this->middleware('scope:lv')->only(['index', 'show']);
        // $this->middleware('scope:lc')->only(['store']);
        // $this->middleware('scope:lu')->only(['update']);
        // $this->middleware('scope:ld')->only(['destroy']);
    }


    public function findScope($subSystem)
    {
        $allScope1 = [
            'user' => ['lud', 'luu', 'luv', 'luc', 'lue'],
            'master' => ['lmd', 'lmu', 'lmv', 'lmc', 'lme'],
            // 'single_sale'=>['sesd','sesu','sesv','sesc','sese'],
            'advertisement' => ['lad', 'lau', 'lav', 'lac', 'lae'],
            'online_sales' => ['osd', 'osu', 'osv', 'osc', 'ose'],
            'product' => ['lprd', 'lpru', 'lprv', 'lprc', 'lpre'],
            'supplier' => ['lsupd', 'lsupu', 'lsupv', 'lsupc', 'lsupe'],
            'content_library' => ['lcld', 'lclu', 'lclv', 'lclc', 'lcle'],

        ];
        if (array_key_exists($subSystem, $allScope1)) {
            $ressult = $allScope1[$subSystem];
            return $ressult;
        }
        return false;
    }
    public function index(Request $request): JsonResponse
    {
        // $scope = 'l' . substr($request->input('system_name'), 0, 1) . 'v';
        // if ($this->tokenCan(strtolower($scope)))
        return $this->repository->paginate($request);
    }



    public function store(Request $request): JsonResponse
    {
        // $scope = 'l' . substr($request->input('system_name'), 0, 1) . 'c';
        $scope = $this->findScope($request->input('slug'));
        if ($scope && $this->tokenCan(strtolower($scope[3]))) {
        }
        $request->validate($this->repository->storeRules());
        return $this->repository->store($request);
    }

    public function show(Request $request)
    {
        // $scope = 'l' . substr($request->input('system_name'), 0, 1) . 'v';
        $scope = $this->findScope($request->input('slug'));
        // if ($scope && $this->tokenCan(strtolower($scope[2]))) {
        $sub_system = SubSystem::where('phrase', $request->input('sub_system'))->first();
        if (!$sub_system) {
            return response()->json(["result" => false, 'labels' => []], Response::HTTP_NOT_FOUND);
        }
        $labels = Label::join('label_sub_systems', 'labels.id', '=', 'label_sub_systems.label_id')
            //->where(['sub_system_id' => $sub_system->id, 'status' => 'live', 'label_category_id' => $request->category_id])->select(['labels.id', 'labels.title', 'labels.label'])->get();
            ->where(['sub_system_id' => $sub_system->id, 'status' => 'live'])->select(['labels.id', 'labels.title'])->get();

        return response()->json(['labels' => $labels], Response::HTTP_OK);
        // }
    }

    public function update(Request $request): JsonResponse
    {
        // return response()->json($request);
        // $scope = 'l' . substr($request->input('system_name'), 0, 1) . 'u';
        // if ($this->tokenCan(strtolower($scope))) {
        $request->validate($this->repository->updateRules());
        return $this->repository->update($request);
        // }
    }


    public function destroy(Request $request, $ids): JsonResponse
    {
        // $scope = 'l' . substr($request->input('system_name'), 0, 1) . 'd';
        $scope = $this->findScope($request->input('slug'));
        if ($scope && $this->tokenCan(strtolower($scope[0]))) {
            $ids = explode(",", $ids);
            return $this->repository->destroy(
                $ids,
                $request
            );
        }
    }

    public function changeStatus(Request $request): JsonResponse
    {
        return $this->repository->changeLabelStatus($request);
    }
    function getRelatedlabels($request)
    {
        $sub_system = SubSystem::where('phrase', $request->subsystem_name)->first();
        if (!$sub_system) {
            return [];
        }
        $labels = Label::join('label_sub_systems', 'labels.id', '=', 'label_sub_systems.label_id')
            ->where(['sub_system_id' => $sub_system->id, 'status' => 'live', 'label_category_id' => $request->category_id])->select(['labels.id', 'labels.title'])->get();
        return $labels;
    }
    public function getCategoryLabels(Request $request)
    {
        # code...

        $sub_system = SubSystem::where('phrase', $request->subsystem_name)->first();
        if (!$sub_system) {
            return [];
        }
        $id =  $sub_system->id;
        $tab = $request->tab;
        $labels = LabelCategory::select(['id', 'title'])->with(['label' => function ($query) use ($id, $tab) {

            $query->whereHas('subSystems', function ($query) use ($id, $tab) {
                $query->where('sub_system_id', $id);
                $query->whereJsonContains('tabs', $tab);
            });
        }])->get();
        return response()->json($labels);
    }
    public function assignLabelToItems(Request $request)
    {
        # code...

        return $this->repository->assignLabelToItems($request);
    }

    public function getLabelsHistory(Request $request)
    {
        # code...
        return $this->repository->getLabelsHistory($request);
    }
    public function getAssignedLabelId(Request $request)
    {

        return  Labelable::where('labelable_id', $request->id)->where('current_status', 'active')->get()->pluck('label_id');
    }
}
