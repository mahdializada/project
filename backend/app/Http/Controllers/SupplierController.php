<?php

namespace App\Http\Controllers;

use App\Exports\SupplierExport;
use App\Repositories\SupplierRepository;
use App\Models\Supplier;
use App\Models\Country;
use App\Models\Sourcer;
use App\Models\Label;
use App\Models\SubSystem;
use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class SupplierController extends Controller
{
    private $repository;
    private $init_status = 'active';

    public function __construct()
    {
        $this->repository = new SupplierRepository();
        // $this->middleware('scope:ssuprv')->only(['index', 'show', 'searchSupplier', 'checkUniqueness']);
        // $this->middleware('scope:ssuprc')->only(['store']);
        // $this->middleware('scope:ssupru')->only(['update', 'changeSupplierStatus']);
        // $this->middleware('scope:ssuprd')->only(['destroy']);

        //daily logs
        // $this->middleware('dailylogs:suppliers,supplier,insert')->only(['store']);
        // $this->middleware('dailylogs:suppliers,supplier,update')->only(['update']);
        // $this->middleware('dailylogs:suppliers,supplier,delete')->only(['destroy']);
        // $this->middleware('dailylogs:suppliers,supplier,change status')->only(['changeUserStatus']);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): JsonResponse
    {
        return $this->repository->paginate($request);
    }


    public function list()
    {
        return Supplier::selectRaw('id,supplier_name')->get();
    }



    public function searchSupplier(Request $request)
    {
        return $this->repository->search($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate($this->repository->storeRules());
        return $this->repository->store($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // $sub_system = SubSystem::where('name', 'Suppliers')->first();
        // if (!$sub_system) {
        //     return response()->json(["result" => false, 'labels' => []], Response::HTTP_NOT_FOUND);
        // }

        $allCompanies   = Company::where('status', 'active')->get();
        // $allLabels    = Label::join('label_sub_systems', 'labels.id', '=', 'label_sub_systems.label_id')
        //     ->where(['sub_system_id' => $sub_system->id, 'status' => 'live'])->select(['labels.id', 'labels.title'])->get();
        $allSourcers = Sourcer::get();
        $allCountries   = Country::whereHas('companies')->where('status', 'active')->get();
        return response()->json([
            "result"        => true,
            'allCompanies'  => $allCompanies,
            // 'allLabels'   => $allLabels,
            'allCountries'  => $allCountries,
            'allSourcers'  => $allSourcers
        ], Response::HTTP_OK);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request): JsonResponse
    {
        $this->repository->updateRules($request);
        return $this->repository->update($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $ids): JsonResponse
    {
        $ids = explode(",", $ids);
        return $this->repository->destroy($ids, $request->query->get('reasonId'), $request);
    }

    public function changeSupplierStatus(Request $request): JsonResponse
    {
        return $this->repository->changeSupplierStatus($request);
    }
    // checkunique columns
    public function checkUniqueness(Request $request): JsonResponse
    {
        return $this->repository->checkUniqueness($request);
    }


    public function checkUniquenesOfCulumnsWithIndex(Request $request)
    {
        return $this->repository->checkUniquenesOfCulumnsWithIndex($request);
    }

    public function exportSupplierTemplate(): JsonResponse
    {
        $labels = parent::getRelatedlabels('Supplier Management System');
        $companies = $this->getRelatedCompanies();
        Excel::store(new SupplierExport($companies), 'export-excel-files/supplier.xlsx', 'public');
        return response()->json(env("APP_URL") . Storage::url('export-excel-files/supplier.xlsx'));
    }

    public function getLocations($id)
    {
        return $this->repository->getLocations($id);
    }

    function getRelatedCompanies()
    {
        $loggedInUser = auth()->user()->id;
        $companies = Company::whereHas('users', function (Builder $builder) use ($loggedInUser) {
            $builder->whereIn('user_id', [$loggedInUser]);
        })->where('status', $this->init_status)->orderBy('name', 'asc')->get();
        return collect($companies);
    }

    public function storeLocation(Request $request){
        return $this->repository->storeLocation($request);
    }
    function updateLocations(Request $request){
        return $this->repository->updateLocations($request);
     }

    function storeSupplierBankAccount(Request $request): JsonResponse
    {

        // $request->validate($this->repository->storeBankAccountRules());
        return $this->repository->storeSupplierBankAccount($request);
     }
     function getSupplierBankAccount($id){
        return $this->repository->getSupplierBankAccount($id);
     }
     function updateSupplierBankAccount(Request $request){
        return $this->repository->updateSupplierBankAccount($request);
     }
     function getSupplierCountry(){
        return $this->repository->getSupplierCountry();
     }
     public function images(Request $request)
     {
         return $this->repository->images($request);
     }
}
