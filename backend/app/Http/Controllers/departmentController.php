<?php

namespace App\Http\Controllers;

use ActivityLog;
use App\Repositories\DepartmentRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Exports\DepartmentExport;
use App\Models\BusinessLocation;
use App\Models\Company;
use App\Models\Department;
use App\Models\Industry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class DepartmentController extends Controller
{
    private $repository;
    private $init_status = 'active';
    private $ActivityLog;

    public function __construct()
    {
        $this->repository = new DepartmentRepository();
        $this->ActivityLog = new ActivityLog();

        $this->middleware('scope:dpv')->only(['validateDepartmentName', 'searchDepartment', 'show']);
        $this->middleware('scope:dpc')->only(['store']);
        $this->middleware('scope:dpu')->only(['update', 'changeDepartmentStatus']);
        $this->middleware('scope:dpd')->only(['destroy']);
    }

    // Display a listing of the resource.

    public function index(Request $request)
    {
        if (in_array('dpv', Auth::user()->get_scopes())) {
            return $this->repository->paginate($request);
        }
        return $this->repository->getAllowedDepartmentsOfCompanies($request);
    }

    // Store a newly created resource in storage.

    public function store(Request $request)
    {

        // $this->ActivityLog->setLog($request, 'masters', 'department', 'insert');
        $request->validate($this->repository->storeRules());
        return $this->repository->store($request);
    }

    public function searchDepartment(Request $request): JsonResponse
    {
        return $this->repository->search($request);
    }

    // Update the specified resource in storage.

    public function update(Request $request): JsonResponse
    {
        $this->repository->updateRules($request);
        $this->ActivityLog->setLog($request, 'masters', 'department', 'update');
        return $this->repository->update($request);
    }

    // Return the specified resource if exists

    public function show($id): JsonResponse
    {
        return $this->repository->show($id);
    }

    // Remove the specified resource from storage.
    public function destroy(Request $request, $ids)
    {
        $ids = explode(",", $ids);
        $this->ActivityLog->setLog($request, 'masters', 'department', 'delete');
        return $this->repository->destroy($ids, $request->query->get('reasonId'), $request);
    }

    public function changeDepartmentStatus(Request $request): JsonResponse
    {
        $this->ActivityLog->setLog($request, 'masters', 'department', 'change status');
        return $this->repository->changeDepartmentStatus($request);
    }

    public function exportDepartmentTemplate(): JsonResponse
    {
        $companies = $this->getRelatedCompanies();
        $businessLocations = $this->getRelatedBusinessLocation();
        Excel::store(
            new DepartmentExport(
                $companies,
                $businessLocations
            ),
            'export-excel-files/department.xlsx',
            'public'
        );
        return response()->json(env("APP_URL") . Storage::url('export-excel-files/department.xlsx'));
    }

    public function checkUniqueness(Request $request): JsonResponse
    {
        return $this->repository->checkUniqueness($request);
    }

    function getRelatedCompanies()
    {
        $loggedInUser = auth()->user()->id;
        $companies = Company::whereHas('users', function (Builder $builder) use ($loggedInUser) {
            $builder->whereIn('user_id', [$loggedInUser]);
        })->where('status', $this->init_status)->orderBy('name', 'asc')->get();
        return collect($companies);
    }

    function getRelatedBusinessLocation()
    {
        $businessLocations = BusinessLocation::where('status', $this->init_status)->orderBy('name', 'asc')->get();
        return collect($businessLocations);
    }
}
