<?php

namespace App\Http\Controllers;

use App\Models\BusinessLocation;
use App\Models\Company;
use App\Models\Country;
use App\Models\Department;
use App\Models\State;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BusinessLocationBulkupload extends Controller
{
    public function __construct()
    {

        $this->middleware('scope:blc')->only(['store']);
        //log middlware
        // $this->middleware('dailylogs:users,user,insert')->only(['store']);
        // $this->middleware('dailylogs:users,user,update')->only(['update']);

    }

    public function prepareStoreValidations($team)
    {
        $businessLocationValidatedData = Validator::make($team, [
            "name" => ["required", "string", "min:2", "max:50", "unique:business_locations,name"],
            "email" => ["required", "unique:business_locations,email"],
            "state" => ["required", "exists:states,name"],
            // "country", ["required", "exists:countries,name"],
            // "company", ["required", "exists:companies,name"],
            "department" => ["required", "exists:departments,name"],
            "location_type" => ["required"],
            "map_link" => ["required"],
            "address" => ["required", "string"],
        ]);
        if ($businessLocationValidatedData->fails()) {
            return (object)['errors' => $businessLocationValidatedData->errors()->all()];
        }
        return (object)['errors' => null, 'validated_data' => $businessLocationValidatedData->valid()];
    }


    public function store(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $errors = [];
            $roles = $request->all();
            foreach ($roles as $role) {
                $businessLocationValidatedData = $this->prepareStoreValidations($role);
                if (is_null($businessLocationValidatedData->errors)) {
                    $attributes['name'] = $businessLocationValidatedData->validated_data['name'];
                    $attributes['email'] = $businessLocationValidatedData->validated_data['email'];
                    $attributes['map_link'] = $businessLocationValidatedData->validated_data['map_link'];
                    $attributes['address'] = $businessLocationValidatedData->validated_data['address'];
                    $attributes['location_type'] = $businessLocationValidatedData->validated_data['location_type'];
                    $attributes['code'] = rand(100000, 999999999);
                    $attributes['created_by'] = $request->user()->id;
                    $attributes['updated_by'] = $request->user()->id;
                    $attributes['department_id'] = Department::where('name', $businessLocationValidatedData->validated_data['department'])->first()->id;
                    $attributes['company_id'] = Company::where('name', $businessLocationValidatedData->validated_data['company'])->first()->id;
                    $attributes['country_id'] = Country::where('name', $businessLocationValidatedData->validated_data['country'])->first()->id;
                    $attributes['state_id'] = State::where('name', $businessLocationValidatedData->validated_data['state'])->first()->id;
                    BusinessLocation::create($attributes);
                } else {
                    array_push($errors, $businessLocationValidatedData->errors);
                }
            }
            DB::commit();
            if(count($errors)){
                return \response()->json($errors, 500);
            }
            return \response()->json(['message' => 'Business Locations have been created successfully'], 200);
        } catch (Exception $exception) {
            DB::rollback();
            return \response()->json($exception->getMessage());
        }
    }
}
