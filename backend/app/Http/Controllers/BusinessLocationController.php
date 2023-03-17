<?php

namespace App\Http\Controllers;

use ActivityLog;
use App\Exports\BusinessLocationExport;
use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use App\Repositories\BusinessLocationRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\BusinessLocation;
use Illuminate\Http\Response;

class BusinessLocationController extends Controller
{

    private $repository;
    protected $init_status = 'active';
    private $ActivityLog;


    public function __construct()
    {
        $this->repository = new BusinessLocationRepository();
        $this->ActivityLog = new ActivityLog();

        $this->middleware('scope:blv')->only(['index', 'show', 'search', 'checkUniqueness']);
        $this->middleware('scope:blc')->only(['store']);
        $this->middleware('scope:blu')->only(['update', 'changeStatus']);
        $this->middleware('scope:bld')->only(['destroy']);
    }


    // Display a listing of the resource.

    public function index(Request $request): JsonResponse
    {
        return $this->repository->paginate($request);
    }
    public function search(Request $request)
    {
        return $this->repository->search($request);
    }


    // Store a newly created resource in storage.
    public function store(Request $request): JsonResponse
    {
        $request->validate($this->repository->storeRules());
        return $this->repository->store($request);
    }


    // Update the specified resource in storage.

    public function update(Request $request): JsonResponse
    {
        $this->repository->updateRules($request);
        return $this->repository->update($request);
    }


    // Return the specified resource if exists
    public function show($ids): JsonResponse
    {
        $ids = explode(",", $ids);
        return $this->repository->show($ids);
    }


    // Remove the specified resource from storage.
    public function destroy(Request $request, $ids): JsonResponse
    {
        $this->ActivityLog->setLog($request, 'masters', 'business_location', 'delete');
        $ids = explode(",", $ids);
        return $this->repository->destroy($ids, $request->query->get('reasonId'));
    }


    public function checkUniquenesOfCulumnsWithIndex(Request $request)
    {
        return $this->repository->checkUniquenesOfCulumnsWithIndex($request);
    }


    // check a column uniqueness
    public function checkUniqueness(Request $request): JsonResponse
    {
        return $this->repository->checkUniqueness($request);
    }

    public function exportBusinessLocationTemplate(): JsonResponse
    {
        $companies = $this->getRelatedCompanies();
        $departments = $this->getRelatedDepartments();
        Excel::store(
            new BusinessLocationExport(
                $companies,
                $this->init_status,
                $departments
            ),
            'export-excel-files/business-location.xlsx',
            'public'
        );
        return response()->json(env("APP_URL") . Storage::url('export-excel-files/business-location.xlsx'));
    }

    public function changeStatus(Request $request): JsonResponse
    {
        $this->ActivityLog->setLog($request, 'masters', 'business_location', 'change status');
        return $this->repository->changeBusinessLocationStatus($request);
    }

    function getRelatedCompanies()
    {
        $loggedInUser = auth()->user()->id;
        $companies = Company::whereHas('users', function (Builder $builder) use ($loggedInUser) {
            $builder->whereIn('user_id', [$loggedInUser]);
        })->where('status', $this->init_status)->orderBy('name', 'asc')->get();
        return collect($companies);
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

    public function forSelect()
    {
        $bl = BusinessLocation::select(['id', 'code', 'name'])
        ->where('status', 'active')
        ->get();
        return response()->json(['data' => $bl], Response::HTTP_OK);
    }
}
