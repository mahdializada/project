<?php

namespace App\Http\Controllers;

use ActivityLog;
use App\Models\User;
use App\Models\Company;
use App\Traits\FileTrait;
use App\Exports\UserExport;
use Illuminate\Http\Request;
use App\Rules\MatchOldPassword;
use App\Rules\CheckSamePassword;
use Illuminate\Http\JsonResponse;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

    use FileTrait;

    private $filePath = "usermanagement/user/images";

    private $repository;
    private $ActivityLog;

    private $init_status = 'active';
    private $gender  = ['male', 'female'];
    private $schedule_type = ['limited', 'unlimited'];

    public function __construct()
    {
        $this->middleware('scope:uv')->only(['index', 'searchUser', 'show', 'getUserDetails', 'checkUniqueness', 'allUsers']);
        $this->middleware('scope:uc')->only(['store']);
        $this->middleware('scope:uu')->only(['update', 'changeUserStatus']);
        $this->middleware('scope:ud')->only(['destroy']);


        $this->repository = new UserRepository();
        $this->ActivityLog = new ActivityLog();
    }

    // Display a listing of the resource.

    public function index(Request $request): JsonResponse
    {
        return $this->repository->{'paginate'}($request);
        return $this->repository->paginate($request);
    }

    public function searchUser(Request $request)
    {
        return $this->repository->search($request);
    }

    // Store a newly created resource in storage.

    public function store(Request $request): JsonResponse
    {

        if ($request->query->getBoolean("update-permissions")) {
            $request->validate($this->repository->permissionRules());
            $userId = $request->query->get("user-id");
            return $this->repository->editUserPermissions($request, $userId);
        }
        $request->validate($this->repository->storeRules());
        // $this->ActivityLog->setLog($request, 'users', 'user', 'insert');
        return $this->repository->store($request);
    }


    // Update the specified resource in storage.

    public function update(Request $request): JsonResponse
    {
        $this->repository->updateRules($request);
        // $this->ActivityLog->setLog($request, 'users', 'user', 'update');

        return $this->repository->update($request);
    }


    // Return the specified resource if exists

    public function show($id)
    {
        return $this->repository->show($id);
    }

    // return users details like systems, sub systems, actions, etc

    public function getUserDetails($userId): JsonResponse
    {
        return $this->repository->getUserDetails($userId);
    }


    // Remove the specified resource from storage.

    public function destroy(Request $request, $userIds)
    {
        // $this->ActivityLog->setLog($request, 'users', 'user', 'delete');
        $userIds = explode(",", $userIds);
        return $this->repository->destroy($userIds, $request->query->get('reasonId'));
    }


    public function checkUniqueness(Request $request): JsonResponse
    {
        return $this->repository->checkUniqueness($request);
    }


    public function checkUniquenesOfCulumnsWithIndex(Request $request)
    {
        return $this->repository->checkUniquenesOfCulumnsWithIndex($request);
    }

    public function changeUserStatus(Request $request)
    {
        // $this->ActivityLog->setLog($request, 'users', 'user', 'change status');
        return $this->repository->changeUserStatus($request);
    }

    public function allUsers(Request $request): JsonResponse
    {
        $users = User::all();
        return response()->json($users);
    }

    public function exportUserTemplate(): JsonResponse
    {
        $companies = Company::all();
        Excel::store(
            new UserExport(
                $this->init_status,
                $this->gender,
                $this->schedule_type,
                $companies
            ),
            'export-excel-files/users.xlsx',
            'public'
        );
        return response()->json(env("APP_URL") . Storage::url('export-excel-files/users.xlsx'));
    }

    public function changeLanguage(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->update(['translated_language_id' => $request->params['language_id']]);
        $user->save();
        return response()->json($user);
    }
    public function getContentDataOfChangeStatus($request)
    {
    }

    // change just user photo
    public function updateUserProfilePhoto(Request $request)
    {
        $user = $request->user();
        $newImagePath = null;
        $this->prefix = $this->filePath;
        $newImagePath = $this->storeAndRemove($request->file("image"), $user->image);
        $request->validate([
            'image' => ['required', 'mimes:jpg,png,gif']
        ]);
        $item = User::find($request->id);
        $result = $item->update([
            'image' => $newImagePath
        ]);
        return response()->json(['massage' => 'photo updated', 'image' => $user->image], 200);
    }

    public function changePassword(Request $request)
    {
        if ($request->query->getBoolean("check-current")) {
            $request->validate([
                'password' => ['required', 'confirmed', 'min:6', new CheckSamePassword]
            ]);
            $user = $request->user();
            $user->password = Hash::make($request->password);
            $user->change_password = false;
            $user->save();
        } else {
            $request->validate([
                'currentPassword' => ['required', new MatchOldPassword],
                'password' => ['required', 'confirmed', 'min:6', new CheckSamePassword]
            ]);

            $user = $request->user();
            $user->password = Hash::make($request->password);

            $user->save();
        }

        return response()->json(['massage' => 'password updated'], 200);
    }

    public function profileUserEdit(Request $request)
    {

        $user = $request->user();

        $request->validate([
            $this->rules()
        ]);


        $item = User::find($request->id);
        $result = $item->update([
            'username' => $request->username
        ]);

        return response()->json(['massage' => 'username updated'], 200);
    }
    public function rules()
    {
        return [
            'username' => 'required|unique:users,username'
        ];
    }

    public function changeLoaction(Request $request)
    {
        $user = $request->user();
        $user->coords = $request->all();
        $user->save();
    }

    public function changeAuthCurrency(Request $request)
    {
        $request->validate([
            "currency_id" => ["required", "exists:currencies,id"],
        ]);
        $auth = $request->user();
        $currency = $request->currency_id;
        if ($currency == $auth->currency_id)
            return response()->json(["message" => "Currency already selected!"]);
        $auth->currency_id = $currency;
        $auth->save();
        return response()->json(["status" => "success"]);
    }
}
