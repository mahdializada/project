<?php

namespace App\Http\Controllers;

use App\Events\Updated;
use App\Models\BusinessLocation;
use App\Models\Company;
use App\Models\Department;
use App\Models\Industry;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DepartmentBulkUpload extends Controller
{
    public function __construct()
    {

        $this->middleware('scope:dpc')->only(['store']);
        //log middlware
        // $this->middleware('dailylogs:users,user,insert')->only(['store']);
        // $this->middleware('dailylogs:users,user,update')->only(['update']);

    }

    public function prepareStoreValidations($departmentRawData)
    {
        $depValidData = Validator::make($departmentRawData, [
            'name' => ['required', 'string', 'min:2', 'max:100', 'unique:departments,name'],
            "note" => ['required'],
            'company' => ['required', 'exists:companies,name'],
            'industry' => ['required', 'exists:industries,name'],
            'business_location' => ['required', 'exists:business_locations,name'],
        ]);
        if ($depValidData->fails()) {
            return (object)['errors' => $depValidData->errors()->all()];
        }
        return (object)['errors' => null, 'validated_data' => $depValidData->valid()];
    }

    public function store(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $errors = [];
            $departments = $request->all();
            $department = new Department();
            $departmentCollection = [];
            foreach ($departments as $department) {
                $depValidData = $this->prepareStoreValidations($department);
                if(is_null($depValidData->errors)){
                    $attributes['name'] = $depValidData->validated_data['name'];
                    $attributes['note'] = $depValidData->validated_data['note'];
                    $business_location_id = BusinessLocation::whereName($depValidData->validated_data['business_location'])->first()->id;
                    $attributes['business_location_id'] = $business_location_id;
                    $attributes['code'] = rand(100000, 999999999);
                    $attributes['created_by'] = $request->user()->id;
                    $attributes['updated_by'] = $request->user()->id;
                    $department = Department::create($attributes);
                    $companyIds = Company::where('name', $depValidData->validated_data['company'])->first()->id;
                    $industry_ids = Industry::whereName($depValidData->validated_data['industry'])->first()->id;
                    $department->industries()->sync($industry_ids);
                    $department->companies()->sync($companyIds);
                    broadcast(new Updated('department', $department->id, 'created'));
                }
                else {
                   array_push($errors, $depValidData->errors);
                }
            }
            DB::commit();
            if(count($errors) > 0){
                return response()->json($errors, 500);
            }
            return response()->json(null, 200);
        } catch (Exception $exception) {
            DB::rollback();
            return response()->json($exception);
        }
    }

    private function getRelations(): array
    {

        return [
            "companies:id,name,code",
            "companies.countries:id,name,iso2",
            "businessLocation:id,name,code",
            "createdBy:id,firstname,lastname",
            "updatedBy:id,firstname,lastname",
            "industries:id,name"
        ];
    }
}
