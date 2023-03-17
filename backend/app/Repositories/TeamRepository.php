<?php


namespace App\Repositories;

use Exception;
use Carbon\Carbon;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use Illuminate\Support\Facades\Request as RequestFacade;
use App\Events\Updated;

class TeamRepository extends Repository
{
    private $model;


    public function __construct()
    {
        $this->model = new Team();
    }

    public function paginate($request): JsonResponse
    {
        if ($request->query->has('key') && $request->get('key') === 'teams-of-logged-in-company') {
            return $this->paginateTeamsOfLoggedInCompany($request);
        }
        if ($request->query->has("team_meta_data") && $request->query->has("team_ids")) {
            return $this->getTeamsMetaData($request);
        }
        if ($request->query->has('key') && $request->get('key') === 'teams-of-company') {
            return $this->paginateTeamsOfCompany($request);
        }
        if ($request->query->has("key") && $request->get('key') === 'teams-only') {
            return $this->paginateOnlyTeams($request);
        }
        $key = $request->query->get("key");
        return $this->getTeamsAccordingToStatus($request, $key);
    }

    function paginateTeamsOfCompany(Request $request)
    {
        $companyId = $request->get('companyId');
        $teams = Team::whereHas('departments', function (Builder $builder) use ($companyId) {
            $builder->whereHas('companies', function (Builder $builder) use ($companyId) {
                $builder->where('companies.id', $companyId);
            });
        })
            ->where('status', 'active')
            ->get(['id', 'name', 'code'])
            ->toArray();
        return $this->successResponse($teams, Response::HTTP_OK);
    }

    function paginateOnlyTeams($request)
    {
        $teams = Team::whereHas('permissions', function (Builder $builder) {
            $builder->orWhereHas('sub_system', function (Builder $builder) {
                $builder->where('name', 'like', '%My Order%');
            });
        })
            ->whereStatus('active')
            ->get(['id', 'name', 'code'])
            ->toArray();
        return $this->successResponse($teams);
    }

    private function paginateTeamsOfLoggedInCompany(Request $request)
    {
        $departments = Team::whereHas('departments', function (Builder $builder) use ($request) {
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

    private function getTeamsMetaData($request)
    {
        $team_ids = $request->team_ids;
        $teams = Team::withTrashed()->whereIn('id', $team_ids)->with(
            [
                'departments:id,name',
                'departments.companies:id,name,logo',
                'departments.companies.countries:id,name,iso2',
                'departments.companies.systems:id,name,logo',
                'members',
                'permissions.action',
                'permissions.action.subActions',
                'permissions.sub_system'
            ]
        )->get();
        return $this->successResponse($teams);
    }
    private function getTeamsAccordingToStatus($request, $key): JsonResponse
    {
        $queryBuilder = new UriQueryBuilder(new Team(), $request);
        $queryBuilder->setRelations($this->getRelations());
        $queryBuilder->setWithTrashed();

        $searchInColumns    = ['name', 'status', 'number_of_people', 'schedule_type', 'date_range', 'time_range', 'updated_at', 'created_at'];
        $queryBuilder->query = $this->filterRecords($queryBuilder->query, $request, $searchInColumns);
        $statusQuery = clone  $queryBuilder->query;
        $queryBuilder->query = $this->fetchDataAccordingToStatus($queryBuilder->query, $key);

        $teamData = $queryBuilder->build()->paginate()->getData();
        $extraData = [
            'allTotal',
            'activeTotal, status, active',
            'inactiveTotal, status, inactive',
            'blockedTotal, status, blocked',
            'pendingTotal, status, pending',
            'warningTotal, status, warning',
            'removedTotal'
        ];
        $teamData = $this->getStatusCount($statusQuery, $teamData, $extraData);
        return response()->json($teamData);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $team = new Team();
            $attributes = $request->only($team->getFillable());

            $attributes["created_by"] = $request->user()->id;
            $attributes["updated_by"] = $request->user()->id;
            $attributes["code"]       = rand(100000, 9999999999);
            list($dateRange, $timeRange)        = $this->getTeamTimeSchedule($request);
            $attributes["date_range"]           = $dateRange;
            $attributes["time_range"]           = $timeRange;

            $attributes["number_of_people"] = $request->user_ids != null ? count($request->user_ids) : 0;

            $attributes["status"] = "pending";

            $team = Team::create($attributes);
            // team department insert
            $team->departments()->sync($request->department_ids);
            // team users insert
            $members = $team->members()->sync($request->user_ids);

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
                $team->permissions()->attach($systems);
            }
            DB::commit();
            broadcast(new Updated('team', ['team_id' => $team->id], 'created'));
            return $this->successResponse(null, Response::HTTP_CREATED);
        } catch (Exception $exception) {
            DB::rollback();
            return $this->errorResponse($exception->getMessage());
        }
    }

    private function getTeamTimeSchedule(Request $request): ?array
    {
        $scheduleType = $request->input("schedule_type");
        if ($scheduleType === "unlimited") {
            return null;
        }
        $dateRange = json_encode($request->input("date_range"));
        if ($dateRange == "null" || $dateRange = '"null"') {
            $dateRange = null;
        }
        $timeRange = json_encode($request->input("time_range"));

        return array($dateRange, $timeRange);
    }


    public function storeRules(): array
    {
        return [
            "department_ids" => ["required", "array"],
            "department_ids.*" => ["exists:departments,id", "uuid"],
            "user_ids" => ["array"],
            "user_ids.*" => ["exists:users,id", "uuid"],
            "name" => ["required", "string", "min:2", "max:50", "unique:teams,name"],
            "schedule_type"           => ["required"],
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
    }

    public function update(Request $request): JsonResponse
    {
        try {
            $teamModal = new Team();
            $teamId = $request->input("id");
            $team = $teamModal::find($teamId);
            $attributes = $request->only($teamModal->getFillable());

            $attributes["updated_by"] = $request->user()->id;

            list($dateRange, $timeRange)        = $this->getTeamTimeSchedule($request);
            $attributes["date_range"]           = $dateRange;
            $attributes["time_range"]           = $timeRange;

            $attributes["number_of_people"] = $request->user_ids != null ? count($request->user_ids) : 0;

            $team->update($attributes);

            $members = $team->members()->sync($request->user_ids);

            $team->departments()->sync($request->department_ids);

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
                $team->permissions()->detach();
                $team->permissions()->attach($systems);
            }
            $team->save();

            broadcast(new Updated('team', ['team_id' => $team->id], 'updated'));
            return $this->successResponse($team->load($this->getRelations()), Response::HTTP_ACCEPTED);
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function updateRules(Request $request)
    {
        $teamId = $request->input("id");
        $rules = [
            "id" => ["required", "exists:teams,id"],
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


    public function show($id): JsonResponse
    {
        $team = $this->model->withTrashed()->with($this->getRelations())->find($id);
        if ($team) {
            return $this->successResponse($team);
        }
        return $this->errorResponse("Not Found");
    }

    private function getRelations()
    {
        return [
            "createdBy:id,firstname,lastname",
            "updatedBy:id,firstname,lastname",
            "members:id,firstname,lastname,email,phone,whatsapp,image",
            "departmentCompanyCountry:id,name,code",
            "reasons",
            'changedBy:id,firstname,lastname',
            "permissions"
        ];
    }

    public function countMembers($TeamIds, $team_id)
    {
        for ($i = 0; $i < count($TeamIds); $i++) {
            Team::find($TeamIds[$i])->update(["team_id", $team_id]);
        }
        return Team::where("team", $team_id)->count();
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
            $team = Team::query()->where('name', '=', $request->name)->where('id', '!=', $request->id)->first();
        } else {
            $team = Team::query()->where('name', '=', $request->name)->first();
        }
        return response()->json(['exist' => $team ? true : false]);
    }

    function checkMulitUniqueness($request)
    {
        $result = [];
        $columns = $request->get('names');
        foreach ($columns as $column) {
            $parsedColumn = collect($column);
            $isExists = Team::whereName($parsedColumn->get("value"))->exists();
            $result[] = ['row_name_value' => $parsedColumn->get("value"), 'value' => $isExists, 'index' => $parsedColumn->get('index')];
        }
        unset($result['']);
        return $result;
    }

    public function changeTeamStatus(Request $request)
    {
        return $this->changeStatus($request, Team::class, 'teams', 'reason_team', 'team_id', $this->getRelations(), 'team');
    }


    public function destroy(array $ids, $reasonId, $request)
    {
        return $this->destroyItems(
            Team::class,
            $ids,
            'reason_team',
            $reasonId,
            'team_id',
            null,
            $this->getRelations(),
            'team',
            $request
        );
    }

    public function search($request): JsonResponse
    {
        $queryBuilder = new UriQueryBuilder(new Team(), $request);
        $data = $this->searchCode($queryBuilder->query, $request, $this->getRelations());
        return $data ? response()->json($data, 200) : response()->json(['result' => false], Response::HTTP_NO_CONTENT);
    }
}
