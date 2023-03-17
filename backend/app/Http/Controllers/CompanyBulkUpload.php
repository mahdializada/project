<?php

namespace App\Http\Controllers;

use App\Events\Updated;
use App\Models\Company;
use App\Models\Country;
use App\Models\System;
use Exception;
use Facade\FlareClient\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CompanyBulkUpload extends Controller
{
    public function __construct()
    {

        $this->middleware('scope:cmc')->only(['store']);
        //log middlware
        // $this->middleware('dailylogs:users,user,insert')->only(['store']);
        // $this->middleware('dailylogs:users,user,update')->only(['update']);

    }

    public function prepareStoreValidations($company)
    {
        $comapanyValidatedData = Validator::make($company, [
            'name'            => ['required', 'string', 'min:2', 'max:100', 'unique:companies,name'],
            'email'           => ['required', 'email', 'unique:companies,email'],
            "note"            => ['required'],
            'country'     => ['required', 'exists:countries,name'],
            'system'     => ['required', 'exists:systems,name'],
            'investment_type' => ['required'],
        ]);
        if ($comapanyValidatedData->fails()) {
            return (object)['errors' => $comapanyValidatedData->errors()->all()];
        }
        return (object)['errors' => null, 'validated_data' => $comapanyValidatedData->valid()];
    }

    public function store(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $errors = [];
            $companies = $request->all();
            foreach ($companies as $company) {
                $comapanyValidatedData = $this->prepareStoreValidations($company);
                if(is_null($comapanyValidatedData->errors)){
                    $company = new Company();
                    $attributes['email'] = $comapanyValidatedData->validated_data['email'];
                    $attributes['name'] = $comapanyValidatedData->validated_data['name'];
                    $attributes['note'] = $comapanyValidatedData->validated_data['note'];
                    $attributes['investment_type'] = $comapanyValidatedData->validated_data['investment_type'];
                    $attributes['logo'] = null;
                    $attributes['code'] = rand(100000, 999999999);
                    $attributes['created_by'] = $request->user()->id;
                    $attributes['updated_by'] = $request->user()->id;
                    $company = Company::create($attributes);
                    $systemIds = System::whereName($comapanyValidatedData->validated_data['system'])->first()->id;
                    $company->systems()->sync($systemIds);
                    $countryIds = Country::whereName($comapanyValidatedData->validated_data['country'])->first()->id;
                    $company->countries()->sync($countryIds);
                    broadcast(new Updated('company', $company->id, 'created'));
                }
                else {
                   array_push($errors, $comapanyValidatedData->errors);
                }
            }
            DB::commit();
            if(count($errors)){
                return \response()->json($errors, 500);
            }
            return response()->json(['message' => 'Companies have success fully inserted'], 200);
        } catch (Exception $exception) {
            return response()->json($errors, 500);
        }
    }

    private function getRelations(): array
    {

        return [
            "createdBy:id,firstname,lastname",
            "updatedBy:id,firstname,lastname",
            "systems:id,name,logo",
            "departments:id,name",
            "countries:id,name,iso2",
            "reasons",
            'changedBy:id,firstname,lastname',
            'socialMedia:id,code,logo,name,website,sample_url_account',
        ];
    }

}
