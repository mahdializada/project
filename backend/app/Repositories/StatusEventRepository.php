<?php

namespace App\Repositories;

use App\Models\Reason;
use App\Models\ReasonSubSystem;
use App\Models\ReasonType;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Models\SubSystem;
use App\Models\System;
use Carbon\Carbon;

class StatusEventRepository extends Repository
{



    /**
     * The function is used to paginate the data
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function paginate(Request $request): JsonResponse
    {
        $system       = System::where('name', 'LIKE', '%' . $request->slug . '%')->first();
        $queryBuilder = new UriQueryBuilder(new ReasonSubSystem(), $request);
        $queryBuilder->setRelations(['subsystem', "reason"]);
        $queryBuilder->query->whereHas('subsystem', function ($q) use ($system) {
            return  $q->where('system_id', $system->id);
        });
        $searchInColumns    = ['whereHasOne,subsystem,name', 'whereHasOne,reason,title', 'updated_at', 'created_at'];
        $queryBuilder->query = $this->filterRecords($queryBuilder->query, $request, $searchInColumns, 'id');

        $reasonData   = $queryBuilder->build()->paginate()->getData();
        return response()->json($reasonData);
    }

    /**
     * the function is used to get the relation of Reason model
     *
     * @return array
     */
    private function getRelations(): array
    {
        return [
            'types',
            'subSystems'
        ];
    }


    public function show(Request $request)
    {
        try {
            $sub_system = SubSystem::where('name', $request->sub_system)->first();
            if (!$sub_system) {
                return response()->json(["result" => false, 'reasons' => []], Response::HTTP_NOT_FOUND);
            }
            $system       = System::where('name', 'LIKE', '%' . $request->slug . '%')->first();
            $queryBuilder = new UriQueryBuilder(new ReasonSubSystem(), $request);
            $queryBuilder->setRelations(['reasonTypes', 'subsystem', "reason"]);
            $queryBuilder->query->whereHas('subsystem', function ($q) use ($system) {
                return    $q->where('system_id', $system->id);
            });
            $queryBuilder->query->whereHas('reason', function ($q) use ($request) {
                return    $q->where('type', $request->type);
            });
            $reasonData   = $queryBuilder->query->get();

            return response()->json(['reasons' => $reasonData], Response::HTTP_OK);
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function store(Request $request): JsonResponse
    {

        return DB::transaction(function () use ($request) {

            $subsystem = $request->sub_systems;
            $type = $request->types;
            $subSystemModel = SubSystem::find($subsystem);

            foreach ($request->reasons as  $res) {
                // $reason = Reason::find($res);
                foreach ($request->types as  $type) {
                    ReasonSubSystem::create([
                        'type' => $type,
                        'sub_system_id' => $subSystemModel->id,
                        'reason_id' => $res
                    ]);
                }
            }
            return response()->json(["result" => true ], Response::HTTP_CREATED);
        });
    }


    private function randomCode(): int
    {
        return rand(10000, 8999999999);
    }



    public function storeRules(): array
    {
        return [
            'reasons'       => 'required|array',
            'types'         => 'required|array',
            'sub_systems'    => 'required|min:1'
        ];
    }

    public function update(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $reason = Reason::find($request->reason[0]);
            $reason_subsystem = ReasonSubSystem::find($request->id);

            if ($reason_subsystem->sub_system_id != $request->subSystem[0]) {
                $reason->subSystems()->detach($reason_subsystem->sub_system_id);
            } else {
                ReasonType::where('reason_sub_system_id', $request->id)->delete();
            }


            $reason->subSystems()->sync($request->subSystem, false);
            $pivot = DB::table('reason_sub_system')->where("reason_id", $reason->id)->where("sub_system_id", $request->subSystem)->first();
            foreach ($request->type as  $type) {
                $count =  ReasonType::where('type', $type)->where('reason_sub_system_id', $pivot->id)->count();
                if ($count < 1) {
                    ReasonType::create([
                        "code" => $this->randomCode(),
                        "type" => $type,
                        "reason_sub_system_id" => $pivot->id,
                        "created_by" => $request->user()->id,
                    ]);
                }
            }
            DB::commit();
            return $this->successResponse(["result" => true], Response::HTTP_OK);
        } catch (Exception $exception) {
            DB::rollback();
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function updateRules(Request $request)
    {
        $rules = [
            'reason'       => 'required|array',
            'type'         => 'required|array',
            'subSystem'    => 'required|array'
        ];
        $request->validate($rules);
    }

    public function getSubsystemTypes(Request $request)
    {
    }
    public function search($request): JsonResponse
    {
        $system       = System::where('name', 'LIKE', '%' . $request->slug . '%')->first();
        $queryBuilder = new UriQueryBuilder(new ReasonSubSystem(), $request);
        $queryBuilder->setRelations(['reasonTypes', 'subsystem', "reason"]);
        $queryBuilder->query->whereHas('subsystem', function ($q) use ($system) {
            return    $q->where('system_id', $system->id);
        });
        $queryBuilder->query->where('id', $request->code);
        $data   = $queryBuilder->build()->paginate()->getData();
        // $data = $this->searchCode($queryBuilder->query, $request, ['reasonTypes', 'subsystem', "reason"]);
        return $data ? response()->json($data, 200) : response()->json(['result' => false], Response::HTTP_NO_CONTENT);
    }
}
