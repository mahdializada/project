<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Team;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TeamBulkUpload extends Controller
{
    public function __construct()
    {

        $this->middleware('scope:tc')->only(['store']);
        //log middlware
        // $this->middleware('dailylogs:users,user,insert')->only(['store']);
        // $this->middleware('dailylogs:users,user,update')->only(['update']);

    }

    public function prepareStoreValidations($team)
    {
        $teamValidatedData = Validator::make($team, [
            "department" => ["required", "exists:departments,name"],
            "name" => ["required", "string", "min:2", "max:50", "unique:teams,name"],
            "schedule_type" => ["required"],
            "note" => ["required"],
        ]);
        if ($teamValidatedData->fails()) {
            return (object)['errors' => $teamValidatedData->errors()->all()];
        }
        return (object)['errors' => null, 'validated_data' => $teamValidatedData->valid()];
    }


    public function store(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $errors = [];
            $companies = $request->all();
            foreach ($companies as $team) {
                $teamValidatedData = $this->prepareStoreValidations($team);
                if (is_null($teamValidatedData->errors)) {
                    $attributes['name'] = $teamValidatedData->validated_data['name'];
                    $attributes['date_range'] = json_encode($teamValidatedData->validated_data['date_range']);
                    $attributes['time_range'] = json_encode($teamValidatedData->validated_data['time_range']);
                    $attributes['note'] = $teamValidatedData->validated_data['note'];
                    $attributes['schedule_type'] = $teamValidatedData->validated_data['schedule_type'];
                    $attributes['code'] = rand(100000, 999999999);
                    $attributes['created_by'] = $request->user()->id;
                    $attributes['updated_by'] = $request->user()->id;
                    $team = Team::create($attributes);
                    $department_id = Department::where('name', $teamValidatedData->validated_data['department'])->first()->id;
                    $team->departments()->sync($department_id);
                } else {
                    array_push($errors, $teamValidatedData->errors);
                }
            }
            DB::commit();
            if(count($errors)){
                return \response()->json($errors, 404);
            }
            return \response()->json(['message' => 'Team has been created successfully'], 200);
        } catch (Exception $exception) {
            DB::rollback();
            return \response()->json($exception->getMessage());
        }
    }
}
