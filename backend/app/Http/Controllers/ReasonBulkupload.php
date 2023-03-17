<?php

namespace App\Http\Controllers;

use App\Models\Reason;
use App\Models\System;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ReasonBulkupload extends Controller
{
    public function __construct()
    {

        $this->middleware('scope:rnc')->only(['store']);
        //log middlware
        // $this->middleware('dailylogs:users,user,insert')->only(['store']);
        // $this->middleware('dailylogs:users,user,update')->only(['update']);

    }
    
    public function prepareStoreValidations($reasonRawData)
    {
        $depValidData = Validator::make($reasonRawData, [
            'title' => ['required', 'string', 'min:2', 'max:100'],
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
            $reasons = $request->get('reasons');
            $system = System::where('name', 'LIKE', '%' . $request->get('slug') . '%')->first();
            foreach ($reasons as $reason) {
                $depValidData = $this->prepareStoreValidations($reason);
                if(is_null($depValidData->errors)){
                    $attributes['title'] = $depValidData->validated_data['title'];
                    $attributes['code'] = rand(100000, 9999999999);
                    $attributes['system_id'] = $system->id;
                    reason::create($attributes);
                }
                else {
                   array_push($errors, $depValidData->errors);
                }
            }
            DB::commit();
            if(count($errors) > 0){
                return response()->json($errors, 500);
            }
            return response()->json(['message' => 'Reasons have success fully inserted']);
        } catch (Exception $exception) {
            DB::rollback();
            return response()->json($exception);
        }
    }
}
