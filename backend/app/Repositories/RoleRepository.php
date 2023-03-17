<?php


namespace App\Repositories;

use Exception;
use Carbon\Carbon;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use Illuminate\Support\Facades\Request as RequestFacade;
use App\Events\Updated;
use Illuminate\Database\Eloquent\Builder;

class RoleRepository extends Repository
{

    public function paginate(Request $request): JsonResponse
    {

        if ($request->query->has('key') && $request->get('key') === 'roles-of-logged-in-company') {
            return $this->paginateRolesOfLoggedInCompany($request);
        }
        if ($request->query->has("role_meta_data") && $request->query->has("role_ids")) {
            return $this->getRolesMetaData($request);
        }
        if ($request->query->has('key') && $request->get('key') === 'roles-of-company') {
            return $this->paginateRolesOfCompany($request);
        }
        $key = $request->query->get("key");
        return  $this->getRolesAccordingToStatus($request, $key);
    }

    function paginateRolesOfCompany(Request $request)
    {
        $companyId = $request->get('companyId');
        $teams = Role::whereHas('users', function (Builder $builder) use ($companyId) {
            $builder->whereHas('companies', function (Builder $builder) use ($companyId) {
                $builder->where('companies.id', $companyId);
            });
        })
            ->where('status', 'active')
            ->get(['id', 'name', 'code'])
            ->toArray();
        return $this->successResponse($teams, Response::HTTP_OK);
    }

    private function getRolesMetaData($request)
    {
        $role_ids = $request->role_ids;
        $roles = Role::withTrashed()->whereIn('id', $role_ids)->with(
            [
                'departments:id,name',
                'departments.companies:id,name,logo',
                'departments.companies.countries:id,name,iso2',
                'members',
                'permissions.action',
                'permissions.action.subActions',
                'permissions.sub_system'
            ]
        )->get();
        return $this->successResponse($roles);
    }
    // return all role  of a specific status role
    private function getRolesAccordingToStatus($request, $key): JsonResponse
    {
        $queryBuilder = new UriQueryBuilder(new Role(), $request);
        $queryBuilder->setWithTrashed();
        $queryBuilder->setRelations($this->getRelations());
        $searchInColumns    = ['name', 'code', 'status', 'number_of_people', 'schedule_type', 'deleted_at', 'created_at', 'updated_at', 'date_range', 'time_range'];
        $queryBuilder->query = $this->filterRecords($queryBuilder->query, $request, $searchInColumns);
        $statusQuery = clone  $queryBuilder->query;
        $queryBuilder->query = $this->fetchDataAccordingToStatus($queryBuilder->query, $key);
        $roleData    = $queryBuilder->build()->paginate()->getData();
        $extraData = [
            'allTotal',
            'activeTotal, status, active',
            'inactiveTotal, status, inactive',
            'warningTotal, status, warning',
            'removedTotal',
            'pendingTotal, status, pending',
            'blockedTotal, status, blocked',
        ];
        $roleData = $this->getStatusCount($statusQuery, $roleData, $extraData);
        return response()->json($roleData);
    }

    private function getRelations()
    {
        return [
            "createdBy:id,firstname,lastname",
            "updatedBy:id,firstname,lastname",
            'departments:id,name',
            'reasons',
            'changedBy:id,firstname,lastname',
        ];
    }

    public function show($id)
    {
        try {
            $role = Role::withTrashed()->with($this->getRelations())->findOrFail($id);
            return $this->successResponse($role, Response::HTTP_OK);
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $team = new Role();
            $attributes = $request->only($team->getFillable());

            $attributes["created_by"] = $request->user()->id;
            $attributes["updated_by"] = $request->user()->id;
            $attributes["code"] = rand(100000, 9999999999);
            list($dateRange, $timeRange)        = $this->getRoleTimeSchedule($request);
            $attributes["date_range"]           = $dateRange;
            $attributes["time_range"]           = $timeRange;
            // $attributes['end_time'] = $attributes['end_time'] != 'null' ? $attributes['end_time'] : null;
            $attributes["status"] = "pending";


            $attributes["number_of_people"] = $request->user_ids != null ? count($request->user_ids) : 0;

            $role = Role::create($attributes);
            // team department insert
            $role->departments()->sync($request->department_ids);
            // team users insert
            $members = $role->members()->sync($request->user_ids);

            $permissions = $request->permissions;

            $actionSubSystems = array();
            $systems = array();
            if ($permissions) {
                foreach ($permissions as $permission) {
                    $permission = json_decode($permission);
                    array_push($actionSubSystems, ['sub_system_id' => $permission->sub_system_id, 'action_id' => $permission->action_id]);
                }
                foreach ($actionSubSystems as $sub) {
                    $res = DB::table("action_sub_system")
                        ->where("sub_system_id", $sub['sub_system_id'])
                        ->where("action_id", $sub['action_id'])
                        ->get();
                    array_push($systems, [
                        'action_sub_system_id' => $res[0]->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                }
                $role->permissions()->attach($systems);
            }

            DB::commit();
            broadcast(new Updated('role', ['role_id' => $role->id], 'created'));
            return $this->successResponse(null, Response::HTTP_CREATED);
        } catch (Exception $exception) {
            DB::rollback();
            return $this->errorResponse($exception->getMessage());
        }
    }

    private function getRoleTimeSchedule(Request $request): ?array
    {
        $scheduleType = $request->input("schedule_type");
        if ($scheduleType === "unlimited") {
            return null;
        }
        $dateRange = json_encode($request->input("date_range"));
        $timeRange = json_encode($request->input("time_range"));

        return array($dateRange, $timeRange);
    }

    public function storeRules(): array
    {
        return [
            "department_ids"        => ["required", "array"],
            "department_ids.*" => ["exists:departments,id", "uuid"],
            "user_ids"        => ["array"],
            "user_ids.*" => ["exists:users,id", "uuid"],
            "name"                 => ["required", "string", "min:2", "max:50", "unique:roles,name"],
            "schedule_type"           => ["required"],
            "date_range"               => Rule::requiredIf(function () {
                $scheduleType         = (string)RequestFacade::input("schedule_type");
                return $scheduleType !== "unlimited";
            }),
            "time_range"              => Rule::requiredIf(function () {
                $scheduleType         = (string)RequestFacade::input("schedule_type");
                return $scheduleType !== "unlimited";
            }),
            "note"                 => ["required"],
            "permissions" => ["array"],
            "permissions.*.action_id"     => ["exists:actions,id"],
            "permissions.*.sub_system_id" => ["exists:sub_systems,id"],
            "permissions.*.system_id"     => ["exists:systems,id"],
        ];
    }

    public function update(Request $request): JsonResponse
    {
        try {
            $roleModal = new Role();
            $roleId = $request->input("id");
            $role = $roleModal::find($roleId);
            $attributes = $request->only($roleModal->getFillable());
            $attributes["updated_by"] = $request->user()->id;
            list($dateRange, $timeRange)        = $this->getRoleTimeSchedule($request);
            $attributes["date_range"]           = $dateRange;
            $attributes["time_range"]           = $timeRange;

            $attributes["number_of_people"] = $request->user_ids != null ? count($request->user_ids) : 0;

            $attributes["number_of_people"] = $request->user_ids != null ? count($request->user_ids) : 0;

            $role->update($attributes);

            $members = $role->members()->sync($request->user_ids);

            $role->departments()->sync($request->department_ids);

            $permissions = $request->permissions;

            $actionSubSystems = array();
            $systems = array();
            if ($permissions) {
                foreach ($permissions as $permission) {
                    $permission = json_decode($permission);
                    array_push($actionSubSystems, ['sub_system_id' => $permission->sub_system_id, 'action_id' => $permission->action_id]);
                }
                foreach ($actionSubSystems as $sub) {
                    $res = DB::table("action_sub_system")
                        ->where("sub_system_id", $sub['sub_system_id'])
                        ->where("action_id", $sub['action_id'])
                        ->get();
                    array_push($systems, [
                        'action_sub_system_id' => $res[0]->id,
                        'updated_at' => Carbon::now()
                    ]);
                }
                $role->permissions()->detach();
                $role->permissions()->attach($systems);
            }
            $role->save();
            broadcast(new Updated('role',  ['role_id' => $role->id], 'updated'));
            return $this->successResponse($role->load($this->getRelations()), Response::HTTP_ACCEPTED);
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function updateRules(Request $request)
    {
        $teamId = $request->input("id");
        $rules = [
            "id" => ["required", "exists:roles,id"],
            "department_ids" => ["required", "array"],
            "department_ids.*" => ["exists:departments,id", "uuid"],
            "user_ids" => ["array"],
            "user_ids.*" => ["exists:users,id", "uuid"],
            "name" => ["required", "string", "min:2", "max:50"],
            "schedule_type" => ["required"],
            "date_range"               => Rule::requiredIf(function () {
                $scheduleType         = (string)RequestFacade::input("schedule_type");
                return $scheduleType !== "unlimited";
            }),
            "time_range"              => Rule::requiredIf(function () {
                $scheduleType         = (string)RequestFacade::input("schedule_type");
                return $scheduleType !== "unlimited";
            }),
            "note" => ["required"],
            "permissions" => ["array"],
            "permissions.*.action_id"     => ["exists:actions,id"],
            "permissions.*.sub_system_id" => ["exists:sub_systems,id"],
            "permissions.*.system_id"     => ["exists:systems,id"],
        ];

        $request->validate($rules);
    }


    public function restore(array $ids): JsonResponse
    {
        try {
            $role = Role::onlyTrashed()->findOrFail($ids);
            $role->each(function (Model $query) {
                $query->restore();
            });
            return $this->successResponse($role);
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function checkUniqueness(Request $request): JsonResponse
    {
        $result = [];
        $hasMulti = $request->query->has('multi');
        $multi = $request->query->getBoolean('multi');
        if ($hasMulti && $multi) {
            $result = $this->checkMulitUniqueness($request);
            return response()->json($result);
        }
        if ($request->id) {
            $role = Role::query()->where('name', '=', $request->name)->where('id', '!=', $request->id)->first();
        } else {
            $role = Role::query()->where('name', '=', $request->name)->first();
        }
        return response()->json(['exist' => $role ? true : false]);
    }

    function checkMulitUniqueness($request)
    {
        $result = [];
        $columns = $request->get('names');
        foreach ($columns as $column) {
            $parsedColumn = collect($column);
            $isExists = Role::whereName($parsedColumn->get("value"))->exists();
            $result[] = ['row_name_value' => $parsedColumn->get("value"), 'value' => $isExists, 'index' => $parsedColumn->get('index')];
        }
        unset($result['']);
        return $result;
    }


    public function changeRoleStatus(Request $request)
    {
        return $this->changeStatus($request, Role::class, 'roles', 'reason_role', 'role_id', $this->getRelations(), 'role');
    }


    public function destroy(array $ids, $reasonId, $request)
    {
        return $this->destroyItems(
            Role::class,
            $ids,
            'reason_role',
            $reasonId,
            'role_id',
            null,
            $this->getRelations(),
            'role',
            $request
        );
    }
    private function paginateRolesOfLoggedInCompany(Request $request)
    {

        $departments = Role::whereHas('departments', function (Builder $builder) use ($request) {
            $builder->whereHas('companies', function (Builder $builder) use ($request) {
                $builder->whereHas('users', function (Builder $builder) use ($request) {
                    $builder->where('users.id', $request->user()->id);
                });
            });
        })
            ->where('status', 'active')
            ->with('departments:id,name')
            ->get(['id', 'code', 'name']);
        return $this->successResponse($departments, Response::HTTP_OK);
    }

    public function search(Request $request)
    {
        $queryBuilder = new UriQueryBuilder(new Role(), $request);
        $data = $this->searchCode($queryBuilder->query, $request, $this->getRelations());
        return $data ? response()->json($data, 200) : response()->json(['result' => false], Response::HTTP_NO_CONTENT);
    }
}
