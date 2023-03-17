<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\System;
use App\Models\SubSystem;
use Illuminate\Http\Response;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SystemController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */

    public function __construct()
    {
        $this->middleware('scope:syv')->only(['show']);
        $this->middleware('scope:syc')->only(['store']);
        $this->middleware('scope:syu')->only(['update', 'restore']);
        // log middleware
        $this->middleware('dailylogs:masters,system,insert')->only(['store']);
        $this->middleware('dailylogs:masters,system,update')->only(['update']);
        $this->middleware('dailylogs:masters,system,delete')->only(['destroy']);
        $this->middleware('dailylogs:masters,system,change status')->only(['changeDepartmentStatus']);
    }


    // Display a listing of the resource.

    public function paginate(Request $request): JsonResponse
    {
        $companyId = $request->query("companyId");
        $companySystems = System::query()->whereHas("companies", function (Builder $query) use ($companyId) {
            $query->where("company_id", $companyId);
        })->whereHas('subSystems', function (Builder $query) {
            $query->where("has_file", true);
        })->select("id", "logo", "name", "phrase")->cursorPaginate(20);

        $nextCursor = $companySystems->nextCursor();
        $data = [
            "cursor" => $nextCursor ? $nextCursor->encode() : NULL,
            "systems" => $companySystems->items(),
        ];
        return response()->json($data);
    }

    public function index(Request $request): JsonResponse
    {
        // return only companies systems with sub systems and actions

        try {
            //code...

            if ($request->query->has("company-ids")) {
                $companyIds = $request->query->get("company-ids");
                $companyIds = explode(",", $companyIds);
                $companySystems = System::query()->whereHas("companies", function (Builder $query) use ($companyIds) {
                    $query->whereIn("company_id", $companyIds);
                })->with(["subSystems:id,name,phrase,system_id", "subSystems.actions:id,name,phrase"])->select("id", "logo", "name", "phrase")->get();
                return response()->json($companySystems, Response::HTTP_OK);
            }
            // $this->tokenCan('syv')
            if (in_array('syv', Auth::user()->get_scopes())) {
                // return only Systems
                if ($request->query->has("for_select")) {
                    return response()->json(System::select(['id', 'logo', 'name'])->get(), Response::HTTP_OK);
                }

                if ($request->query->has("file_systems_company_id")) {
                    return  $this->getSystemsFolders($request);
                }

                $query = System::with(array('subSystems' => function ($query) {
                    $query->with('actions');
                }));
                $query = $this->search($query, $request);

                return response()->json($query->get(), Response::HTTP_OK);
            } else {
                if ($request->query->has("file_systems_company_id")) {
                    return  $this->getSystemsFolders($request);
                }

                return response()->json(["error" => true], 403);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["error" => true, 'message' => $th->getMessage()], 403);
        }
    }
    private function getSystemsFolders($request): JsonResponse
    {
        $companyId = $request->get("file_systems_company_id");
        $companySystems = System::query()->whereHas("companies", function (Builder $query) use ($companyId) {
            $query->where("company_id", $companyId);
        })->whereHas('subSystems', function (Builder $query) {
            $query->where("has_file", true);
        })->select("id", "logo", "name", "phrase")->get();

        return response()->json($companySystems, Response::HTTP_OK);
    }

    public function searchSystem(Request $request): JsonResponse
    {
        $type = $request->type;
        $content = $request->content;

        $query = System::with(array('subSystems' => function ($query) {
            $query->with('actions');
        }));
        if ($content != null) {
            if ($type == 1) {
                $query = $query->where('code', last($content))->with($this->getRelations())->first();
                if ($query) {
                    return response()->json($query, 200);
                }
            }
        }
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    private function search($query, $request)
    {
        $type = $request->type;
        $content = $request->contentData;
        if ($content != null) {
            if ($type == 0) {
                $query = $query->where(function ($query) use ($content) {
                    return $query->where('name', 'like', '%' . $content . '%');
                });
            }
        }
        return $query;
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        if ($request->input('isUpdate')) {
            // Update System, sub_system with actions

            $request->validate($this->updateRules($request));
            return DB::transaction(function () use ($request) {
                $system = System::find($request->input('id'));
                $system->name = $request->input('name');
                $system->logo = $request->input("logo");
                $system->save();

                $subSystem = json_decode($request->input('subSystem'), true);
                for ($i = 0; $i < count($subSystem); $i++) {
                    $sub_system = SubSystem::firstOrNew(['id' => $subSystem[$i]['id']]);
                    $sub_system->name = $subSystem[$i]['name'];
                    $sub_system->system_id = $system->id;
                    $sub_system->save();
                    $sub_system->actions()->sync($subSystem[$i]['actionIds']);
                }

                $system->load('subSystems.actions');
                return response()->json(["result" => true, "data" => $system], Response::HTTP_OK);
            });
        } else {
            // Store System, sub_system with actions

            $request->validate($this->storeRules());
            return DB::transaction(function () use ($request) {
                $system = new System();
                $system->name = $request['name'];
                $system->logo = $request->input("logo");
                $system->save();

                $subSystem = json_decode($request->input('subSystem'), true);
                for ($i = 0; $i < count($subSystem); $i++) {
                    $sub_system = new SubSystem();
                    $sub_system->name = $subSystem[$i]['name'];
                    $sub_system->system_id = $system->id;
                    $sub_system->save();
                    $sub_system->actions()->sync($subSystem[$i]['actionIds']);
                }
                $system->load('subSystems.actions');
                return response()->json(["result" => true, "data" => $system], Response::HTTP_CREATED);
            });
        }
    }


    public function storeRules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:50', 'unique:systems,name'],
            'logo' => ['required'],
            'subSystem.*.name' => ['required', 'string', 'min:2', 'max:50', 'unique:sub_systems,name'],
            'subSystem.*.actions' => ['required'],
        ];
    }


    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $query = System::with(array('subSystems' => function ($query) {
            $query->with('actions');
        }))->find($id);
        return response()->json($query, Response::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        // $request->validate($this->updateRules($request));

        // DB::transaction(function () use ($request, $id) {
        //     $system         = System::find($id);
        //     $system->name   = $request->input('name');
        //     // if ($request->hasFile("logo")) {
        //     //     $system->logo   = $this->storeAndRemove($request->file('logo'), $system->getRawOriginal('logo'));
        //     // }
        //     $system->save();

        //     $subSystem  = $request->input('subSystem');
        //     for ($i=0; $i < count($subSystem); $i++) {
        //         $sub_system             = SubSystem::firstOrNew(['id' => $subSystem[$i]['id']]);
        //         $sub_system->name       = $subSystem[$i]['name'];
        //         $sub_system->system_id  = $system->id;
        //         $sub_system->save();
        //         $sub_system->actions()->sync($subSystem[$i]['actionIds']);
        //     }

        //     return response()->json($system, Response::HTTP_CREATED);
        // });
    }

    public function updateRules($request): array
    {
        $rules = [
            'name' => ['required', 'string', 'min:2', 'max:50', 'unique:systems,name,' . $request['id']],
            'logo' => ['required'],
            'subSystem.*.actions' => ['required'],
        ];
        $subSystem2 = json_decode($request->input('subSystem'), true);
        foreach ($subSystem2 as $key => $subSystem) {
            if (array_key_exists('id', $subSystem) && $subSystem['id']) { // if have an id, means an update, so add the id to ignore
                $rules = array_merge($rules, ['subSystem.*.name' => 'required|unique:sub_systems,name,' . $subSystem['id']]);
            }
        }
        return $rules;
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }


    public function getSubSystems(Request $request)
    {
        $system_id      = System::where('name', 'LIKE', '%' . $request->query->get('system_name') . '%')->first();
        if (!$system_id) {
            return response()->json(["result" => false], Response::HTTP_NOT_FOUND);
        }
        $sub_systems = SubSystem::where(['system_id' => $system_id->id, 'has_status' => "1"])->get();
        foreach ($sub_systems as $sub) {
            if (strpos($sub->name, 'Labels') !== false) {
                $sub->name = 'Labels';
            }
        }
        return response()->json(["result" => true, "data" => $sub_systems], Response::HTTP_OK);
    }

    public function forSelect()
    {
        $systems = System::select(['id', 'logo', 'name'])->get();
        return response()->json(['result' => true, 'data' => $systems], Response::HTTP_OK);
    }
    public function getSystemWithSubsystem(Request $request)
    {
        # code...
        if (!$request->with_columns) {
            $systems =  System::select('id', 'name', 'logo', 'created_at', 'updated_at')->with("subSystems:id,name,system_id")->get();
            return response()->json($systems);
        } else {
            $systems =  System::select('id', 'name', 'logo', 'created_at', 'updated_at')->with([
                "subSystems:id,name,system_id",
                "subSystems.tabs.columns",
                "subSystems.categories",
            ])->get();
        }

        return response()->json($systems);
    }
}
