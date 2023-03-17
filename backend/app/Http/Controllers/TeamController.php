<?php

namespace App\Http\Controllers;

use ActivityLog;
use App\Exports\TeamExport;
use App\Models\Department;
use App\Models\Team;
use App\Repositories\TeamRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class TeamController extends Controller
{
    private $repository;
    private $ActivityLog;
    private $init_status = 'active';
    private $schedule_type = ['time', 'date', 'unlimited'];

    public function __construct()
    {
        $this->repository = new TeamRepository();
        $this->ActivityLog = new ActivityLog();

        $this->middleware('scope:tv')->only(['index', 'show', 'checkUniqueness']);
        $this->middleware('scope:tc')->only(['store']);
    }


    // Display a listing of the resource.

    public function index(Request $request): JsonResponse
    {
        return $this->repository->paginate($request);
    }



    // Store a newly created resource in storage.

    public function store(Request $request)
    {
        $request->validate($this->repository->storeRules());
        // $this->ActivityLog->setLog($request, 'users', 'team', 'insert');
        return $this->repository->store($request);
    }

    public function searchTeam(Request $request)
    {
        return $this->repository->search($request);
    }

    // Update the specified resource in storage.

    public function update(Request $request)
    {
        $this->repository->updateRules($request);
        // $this->ActivityLog->setLog($request, 'users', 'team', 'update');
        return $this->repository->update($request);
    }


    // Return the specified resource if exists

    public function show($id): JsonResponse
    {
        return $this->repository->show($id);
    }

    public function checkUniqueness(Request $request): JsonResponse
    {
        return $this->repository->checkUniqueness($request);
    }
    // Remove the specified resource from storage.
    public function destroy(Request $request, $ids)
    {
        // $this->ActivityLog->setLog($request, 'users', 'team', 'delete');

        $ids = explode(",", $ids);
        return $this->repository->destroy($ids, $request->query->get('reasonId'), $request);
    }

    public function changeTeamStatus(Request $request): JsonResponse
    {
        // $this->ActivityLog->setLog($request, 'users', 'team', 'change status');

        return $this->repository->changeTeamStatus($request);
    }

    public function exportTeamTemplate(): JsonResponse
    {
        $departments = $this->getRelatedDepartments();
        Excel::store(
            new TeamExport(
                $this->schedule_type,
                $departments
            ),
            'export-excel-files/team.xlsx',
            'public'
        );
        return response()->json(env("APP_URL") . Storage::url('export-excel-files/team.xlsx'));
    }

    function getRelatedDepartments()
    {
        $loggedInUser = auth()->user()->id;
        $departments = Department::whereHas('companies', function (Builder $builder) use ($loggedInUser) {
            $builder->whereHas('users', function (Builder $builder) use ($loggedInUser) {
                $builder->whereIn('user_id', [$loggedInUser]);
            });
        })->where('status', $this->init_status)->orderBy('name', 'asc')->get();
        return collect($departments);
    }
}
