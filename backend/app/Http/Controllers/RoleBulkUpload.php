<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Role;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RoleBulkUpload extends Controller
{
    public function __construct()
    {

        $this->middleware('scope:rc')->only(['store']);
        //log middlware
        // $this->middleware('dailylogs:users,user,insert')->only(['store']);
        // $this->middleware('dailylogs:users,user,update')->only(['update']);

    }

    public function prepareStoreValidations($team)
    {
        $roleValidatedData = Validator::make($team, [
            "department" => ["required", "exists:departments,name"],
            "name" => ["required", "string", "min:2", "max:50", "unique:roles,name"],
            "schedule_type" => ["required"],
            "note" => ["required"],
        ]);
        if ($roleValidatedData->fails()) {
            return (object)['errors' => $roleValidatedData->errors()->all()];
        }
        return (object)['errors' => null, 'validated_data' => $roleValidatedData->valid()];
    }


    public function store(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $errors = [];
            $roles = $request->all();
            foreach ($roles as $role) {
                $roleValidatedData = $this->prepareStoreValidations($role);
                if (is_null($roleValidatedData->errors)) {
                    $attributes['name'] = $roleValidatedData->validated_data['name'];
                    $attributes['date_range'] = json_encode($roleValidatedData->validated_data['date_range']);
                    $attributes['time_range'] = json_encode($roleValidatedData->validated_data['time_range']);
                    $attributes['note'] = $roleValidatedData->validated_data['note'];
                    $attributes['schedule_type'] = $roleValidatedData->validated_data['schedule_type'];
                    $attributes['code'] = rand(100000, 999999999);
                    $attributes['created_by'] = $request->user()->id;
                    $attributes['updated_by'] = $request->user()->id;
                    $role = Role::create($attributes);
                    $department_id = Department::where('name', $roleValidatedData->validated_data['department'])->first()->id;
                    $role->departments()->sync($department_id);
                } else {
                    array_push($errors, $roleValidatedData->errors);
                }
            }
            DB::commit();
            if(count($errors)){
                return \response()->json($errors, 500);
            }
            return \response()->json(['message' => 'Role has been created successfully'], 200);
        } catch (Exception $exception) {
            DB::rollback();
            return \response()->json($exception->getMessage());
        }
    }
}
