<?php

namespace App\Http\Controllers;

use ActivityLog;
use App\Exports\RoleExport;
use App\Exports\TeamExport;
use App\Models\Department;
use App\Repositories\RoleRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class RoleController extends Controller
{
    private $init_status = 'active';
    private $schedule_type = ['time', 'date', 'unlimited'];
    private $repository;
    private $ActivityLog;


    public function __construct()
    {
        $this->repository = new RoleRepository();
        $this->middleware('scope:rv')->only(['index', 'show', 'checkUniqueness']);
        $this->middleware('scope:rc')->only(['store']);
        $this->ActivityLog = new ActivityLog();

        // $this->middleware('scope:ru')->only(['update', 'restore', 'changeRoleStatus']);
        // $this->middleware('scope:rd')->only(['destroy']);

        // $this->middleware('dailylogs:users,role,insert')->only(['store']);
        // $this->middleware('dailylogs:users,role,update')->only(['update']);
        // $this->middleware('dailylogs:users,role,delete')->only(['destroy']);
        // $this->middleware('dailylogs:users,role,change status')->only(['changeRoleStatus']);
    }

    public function index(Request $request): JsonResponse
    {
        return $this->repository->paginate($request);
    }

    public function searchRole(Request $request)
    {
        return $this->repository->search($request);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate($this->repository->storeRules());
        // $this->ActivityLog->setLog($request, 'users', 'role', 'insert');
        return $this->repository->store($request);
    }


    public function update(Request $request): JsonResponse
    {
        $this->repository->updateRules($request);
        // $this->ActivityLog->setLog($request, 'users', 'role', 'update');
        return $this->repository->update($request);
    }


    public function show($id): JsonResponse
    {
        return $this->repository->show($id);
    }

    public function checkUniqueness(Request $request): JsonResponse
    {
        return $this->repository->checkUniqueness($request);
    }

    public function restore($ids): JsonResponse
    {
        $ids = explode(',', $ids);
        return $this->repository->restore($ids);
    }

    public function destroy(Request $request, $ids): JsonResponse
    {
        // $this->ActivityLog->setLog($request, 'users', 'role', 'delete');
        $ids = explode(",", $ids);
        return $this->repository->destroy($ids, $request->query->get('reasonId'), $request);
    }

    public function changeRoleStatus(Request $request): JsonResponse
    {
        // $this->ActivityLog->setLog($request, 'users', 'role', 'change status');
        return $this->repository->changeRoleStatus($request);
    }

    public function exportRoleTemplate(): JsonResponse
    {
        $departments = $this->getRelatedDepartments();
        Excel::store(
            new RoleExport(
                $this->schedule_type,
                $departments
            ),
            'export-excel-files/roles.xlsx',
            'public'
        );
        return response()->json(env("APP_URL") . Storage::url('export-excel-files/roles.xlsx'));
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
