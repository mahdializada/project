<?php

namespace App\Http\Controllers;

use ActivityLog;
use App\Repositories\CompanyRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Exports\CompanyExport;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class CompanyController extends Controller
{
    private $repository;
    private $init_status = 'active';
    private $investment_type = ['Main Comapny', 'Other'];
    private $ActivityLog;

    public function __construct()
    {
        $this->ActivityLog = new ActivityLog();
        $this->repository = new CompanyRepository();
        $this->middleware('scope:cmv')->only(['validateCompanyName', 'validateCompanyEmail', 'searchCompany']);
        $this->middleware('scope:cmc')->only(['store', 'exportCompanyTemplate']);
        $this->middleware('scope:cmu')->only(['update', 'restore', 'updateCompany', 'changeCompanyStatus']);
        $this->middleware('scope:cmd')->only(['destroy']);

        // log middlware
        // $this->middleware('dailylogs:masters,company,insert')->only(['store']);
        // $this->middleware('dailylogs:masters,company,update')->only(['updateCompany']);
        // $this->middleware('dailylogs:masters,company,delete')->only(['destroy']);
        // $this->middleware('dailylogs:masters,company,change status')->only(['changeCompanyStatus']);
    }

    public function validateCompanyName(Request $request): JsonResponse
    {
        return $this->repository->validateCompanyName($request);
    }
    public function validateCompanyEmail(Request $request): JsonResponse
    {
        return $this->repository->validateCompanyEmail($request);
    }


    // Display a listing of the resource.

    public function index(Request $request): JsonResponse
    {
        if ($this->tokenCan('cmv')) {
            return $this->repository->paginate($request);
        }
        return $this->repository->getAllowedCompaniesOfCountries($request);
    }
    // Display a listing of the resource.

    public function paginate(Request $request): JsonResponse
    {
        return $this->repository->paginateCompanies($request);
    }

    public function searchCompany(Request $request)
    {
        return $this->repository->search($request);
    }

    public function restore($id)
    {
        return DB::transaction(function () use ($id) {
            $project = Company::onlyTrashed()->findOrFail($id);
            $project->restore();
            return response()->json(['result' => true, "data" => $project->load('businessType')], Response::HTTP_CREATED);
        });
    }

    // Store a newly created resource in storage.

    public function store(Request $request): JsonResponse
    {
        $request->validate($this->repository->storeRules());
        // $this->ActivityLog->setLog($request, 'masters', 'company', 'insert');

        return $this->repository->store($request);
    }


    // Update the specified resource in storage.

    public function update(Request $request)
    {

        $this->repository->updateRules($request);
        // $this->ActivityLog->setLog($request, 'masters', 'company', 'update');
        return $this->repository->update($request);
    }

    // Return the specified resource if exists

    public function show($id): JsonResponse
    {
        return $this->repository->show($id);
    }



    // Remove the specified resource from storage.
    public function destroy(Request $request, $ids): JsonResponse
    {
        // $this->ActivityLog->setLog($request, 'masters', 'company', 'delete');
        $ids = explode(",", $ids);
        return $this->repository->destroy($ids, $request->query->get('reasonId'), $request);
    }

    public function changeCompanyStatus(Request $request): JsonResponse
    {
        // $this->ActivityLog->setLog($request, 'masters', 'company', 'change status');
        return $this->repository->changeCompanyStatus($request);
    }

    public function exportCompanyTemplate(): JsonResponse
    {
        Excel::store(
            new CompanyExport(
                $this->investment_type,
                $this->init_status,
            ),
            'export-excel-files/company.xlsx',
            'public'
        );
        return response()->json(env("APP_URL") . Storage::url('export-excel-files/company.xlsx'));
    }

    public function checkUniqueness(Request $request): JsonResponse
    {
        return $this->repository->checkUniqueness($request);
    }
    public function checkNameUniquenessOnCrud(Request $request): JsonResponse
    {
        return $this->repository->checkNameUniquenessOnCrud($request);
    }
    public function checkEmailUniquenessOnCrud(Request $request): JsonResponse
    {
        return $this->repository->checkEmailUniquenessOnCrud($request);
    }

    public function getCompaniesUsedInConnection(Request $request)
    {
        $companyQuery = Company::where('status', 'active')->whereHas('connections')->orderBy("name", "ASC");
        return  $companyQuery->select(['id', 'name', 'code', 'logo'])->get();
    }
    public function changeCompanyStatusAndAddUser(Request $request)
    {

        try {
            DB::beginTransaction();
            $company = Company::find($request->company_id);
            $company->status = @$request->status ?? 'active';
            $company->save();
            if($request->users && count($request->users)>0)
            $company->users()->attach($request->users);
            DB::commit();
            return response()->json(['result' => true], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
}
