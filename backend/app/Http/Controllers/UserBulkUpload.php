<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Country;
use App\Models\Language;
use App\Models\Role;
use App\Models\State;
use App\Models\Team;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserBulkUpload extends Controller
{

    public function __construct()
    {

        $this->middleware('scope:uc')->only(['store']);
        //log middlware
        // $this->middleware('dailylogs:users,user,insert')->only(['store']);
        // $this->middleware('dailylogs:users,user,update')->only(['update']);

    }

    public function prepareStoreRules($userRawData)
    {
        $userValidData = Validator::make($userRawData, [
            'firstname' => ['required', 'string', 'min:2', 'max:32'],
            'lastname' => ['required', 'string', 'min:2', 'max:32'],
            'username' => ['required', 'string', 'min:2', 'max:32', "unique:users,username"],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['required', 'unique:users,phone'],
            'whatsapp' => ['required', 'unique:users,whatsapp'],
            "gender" => ["required", "string"],
            "birth_date" => ["required", 'date'],
            "address" => ["required", "string"],
            "note" => ["required", "string"],
            'password' => ['required'],
            "country" => ["required", "exists:countries,name"],
            "state" => ["required", "exists:states,name"],
            "current_country" => ["required", "exists:countries,name"],
            "role" => ["required", "exists:roles,name"],
            "team" => ["exists:teams,name"],
            "company" => ["required"],
            "language" => ["required", "exists:languages,name"],
        ]);
        if ($userValidData->fails()) {
            return (object)['errors' => $userValidData->errors()->all()];
        }
        return (object)['errors' => null, 'validated_data' => $userValidData->valid()];
    }


    public function store(Request $request): JsonResponse
    {
        $errors = [];
        try {
            DB::beginTransaction();
            $users = $request->all();
            foreach ($users as $user){
                $imagePath = '';
                $userValidData = $this->prepareStoreRules($user);
                if(is_null($userValidData->errors)){
                    $userModel = new User();
                    $attributes["password"] = Hash::make($userValidData->validated_data['password']);
                    $attributes["code"] = $this->randomUserCode();
                    $attributes["birth_date"] = $userValidData->validated_data['birth_date'];
                    $attributes["change_password"] = false;
                    if($userValidData->validated_data['gender'] == 'male'){
                        $imagePath = '/user-profiles/boy.svg';
                    }
                    else {
                        $imagePath = '/user-profiles/girl.svg';
                    }
                    $attributes['image'] = $imagePath;
                    $attributes["created_by"] = $request->user()->id;
                    $attributes['email'] = $userValidData->validated_data['email'];
                    $attributes['current_country_id'] = Country::where('name', $userValidData->validated_data['current_country'])->first()->id;
                    $attributes['country_id'] = Country::where('name', $userValidData->validated_data['country'])->first()->id;
                    $attributes['gender'] = $userValidData->validated_data['gender'];
                    $attributes['firstname'] = $userValidData->validated_data['firstname'];
                    $attributes['lastname'] = $userValidData->validated_data['lastname'];
                    $attributes['address'] = $userValidData->validated_data['address'];
                    $attributes['language_id'] = Language::whereName($userValidData->validated_data['language'])->first()->id;
                    $attributes['note'] = $userValidData->validated_data['note'];
                    $attributes['phone'] = $userValidData->validated_data['phone'];
                    $attributes['state_id'] = State::whereName($userValidData->validated_data['state'])->first()->id;
                    $attributes['username'] = $userValidData->validated_data['username'];
                    $attributes['whatsapp'] = $userValidData->validated_data['whatsapp'];
                    $userModel = $userModel->create($attributes);
                    $attributes = [];
                    $attributes['company_id'] = Company::where('name', $userValidData->validated_data['company'])->first()->id;
                    $userModel->companies()->sync($attributes);
                    $attributes = [];
                    $attributes['role_id'] = Role::where('name', $userValidData->validated_data['role'])->first()->id;
                    $userModel->roles()->sync($attributes);
                    $attributes = [];
                    $attributes['team_id'] = Team::where('name', $userValidData->validated_data['team'])->first()->id;
                    $userModel->teams()->sync($attributes);
                    $attributes = [];
                }
                else {
                    array_push($errors, $userValidData->errors);
                }
            }
            DB::commit();
            if(count($errors) > 0){
                return response()->json($errors, 500);
            }
            return response()->json(['message' => 'Users created successfully!']);
        } catch (Exception $exception) {
            throw new Exception($exception);
        }
    }
    private function randomUserCode(): int
    {
        return rand(10000, 8999999999);
    }
}
