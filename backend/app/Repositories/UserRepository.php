<?php

namespace App\Repositories;

use Exception;
use Carbon\Carbon;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use App\Events\Updated;
use App\Models\Country;
use App\Models\Language;
use App\Models\SubAction;
use App\Models\SubSystem;
use App\Traits\FileTrait;
use App\Models\SystemFile;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Mail\UserPasswordMail;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use App\Models\DesignRequestOrder;
use App\Models\TranslatedLanguage;
use Illuminate\Support\Facades\DB;
use App\Models\FileLimitionForUsers;
use App\Repositories\Files\Personal;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Builder;
use App\Repositories\Files\System as Filesystem;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use Illuminate\Support\Facades\Request as RequestFacade;

class UserRepository extends Repository
{
    use FileTrait;

    private $filePath = "users/images";

    public function paginate(Request $request): JsonResponse
    {
        if ($request->not_assigned_users == 'true') {
            return $this->getTasksNotAssignedUser($request);
        }
        if ($request->query->has('key') && $request->get('key') === 'users-of-logged-in-company') {
            return $this->paginateUsersOfLoggedInCompany($request);
        } else if ($request->query->has('company_ids')) {
            return $this->getUsersBasedOnComapnyIds($request);
        } else if ($request->query->has('company_ids_for_files')) {
            return $this->getFileUsersBasedOnComapnyIds($request);
        } else if ($request->query->has('department_ids')) {
            return $this->getUsersBasedOnDepartmentIds($request);
        } else if ($request->query->has('team_ids')) {
            return $this->getUsersBasedOnTeams($request);
        } else if ($request->query->has('role_ids')) {
            return $this->getUsersBasedOnRoles($request);
        }

        $key = $request->query->get("key");
        return $this->getUsersAccordingToStatus($request, $key);
    }

    private function getUsersAccordingToStatus($request, $key): JsonResponse
    {
        $queryBuilder = new UriQueryBuilder(new User(), $request);
        $queryBuilder->setRelations($this->getRelations());
        $queryBuilder->setWithTrashed();

        $searchInColumns = [
            'firstname', 'lastname', 'username', 'phone', 'whatsapp', 'status', 'email', 'gender',
            'birth_date', 'tracing_status', 'schedule_type', 'date_range', 'time_range', 'updated_at', 'created_at'
        ];
        $queryBuilder->query = $this->filterRecords($queryBuilder->query, $request, $searchInColumns);
        $statusQuery = clone $queryBuilder->query;
        $customColumn = ['tracing, tracing_status, 1'];
        $queryBuilder->query = $this->fetchDataAccordingToStatus($queryBuilder->query, $key, $customColumn);

        $userData = $queryBuilder->build()->paginate()->getData();
        $extraData = [
            'allTotal',
            'activeTotal, status, active',
            'inactiveTotal, status, inactive',
            'blockedTotal, status, blocked',
            'pendingTotal, status, pending',
            'warningTotal, status, warning',
            'tracingTotal, tracing_status, 1',
            'removedTotal'
        ];
        $userData = $this->getStatusCount($statusQuery, $userData, $extraData);
        return response()->json($userData);
    }


    /**
     * Store user into DB and assign all accessibility and check for emailing user password
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $user = null;
        try {
            DB::beginTransaction();
            $userPassword = $this->userPassword($request);
            $user = $this->storeUser($request, $userPassword);
            // check and store user permissions
            $this->storeUserPermissions($user, $request);

            // send user password to email
            if ($request->boolean("email_password")) {
                $this->emailUserPassword($user, $userPassword);
            }
            if ($user->hasPersonalStorage()) {
                $this->storeDefaultStorage($user);
            }

            DB::commit();
            broadcast(new Updated('user', $user->id, 'created'));
            return $this->successResponse(null, Response::HTTP_CREATED);
        } catch (Exception $exception) {
            if ($user) {
                $this->deleteFile($user->getRawOriginal("image"));
            }
            DB::rollBack();
            return $this->errorResponse($exception->getMessage(), $exception->getCode() === 100 ? Response::HTTP_BAD_GATEWAY : Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Validations rules for creating new user into server
     * @return array
     */
    public function storeRules(): array
    {
        return [
            'firstname' => ['required', 'string', 'min:2', 'max:32'],
            'lastname' => ['required', 'string', 'min:2', 'max:32'],
            'username' => ['required', 'string', 'min:2', 'max:32', "unique:users,username"],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['required', 'unique:users,phone'],
            'whatsapp' => ['required', 'unique:users,whatsapp'],
            "gender" => ["required", "string"],
            "birth_date" => ["required", 'date'],
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg'],
            "address" => ["required", "string"],
            "note" => ["required", "string"],
            "generate_password" => ["required"],
            'confirm_password' => ['required_if:generate_password,0', 'same:password'],
            'password' => ['required_if:generate_password,0', "same:confirm_password"],
            "change_password" => ["required"],
            "permission_type" => ["required"],
            "email_password" => ["required"],
            "schedule_type" => ["required"],

            "dateRange" => Rule::requiredIf(function () {
                $scheduleType = (string)RequestFacade::input("schedule_type");
                return $scheduleType !== "unlimited";
            }),

            "timeRange" => Rule::requiredIf(function () {
                $scheduleType = (string)RequestFacade::input("schedule_type");
                return $scheduleType !== "unlimited";
            }),


            "country_id" => ["required", "exists:countries,id", "uuid"],
            "state_id" => ["required", "exists:states,id"],
            "current_country_id" => ["required", "exists:countries,id", "uuid"],
            "roles" => Rule::requiredIf(function () {
                $isPermissionsSkipped = (bool)RequestFacade::input("isPermissionsSkipped");
                $permissionType = (string)RequestFacade::input("permission_type");
                return $permissionType === "by_role" && $isPermissionsSkipped === false;
            }),
            "roles.*" => ["exists:roles,id", "uuid"],
            "teams" => Rule::requiredIf(function () {
                $isPermissionsSkipped = (bool)RequestFacade::input("isPermissionsSkipped");
                $permissionType = (string)RequestFacade::input("permission_type");
                return $permissionType === "by_team" && $isPermissionsSkipped === false;
            }),
            "teams.*" => ["exists:teams,id", "uuid"],
            "companies" => ["required", "array"],
            "companies.*" => ["exists:companies,id", "uuid"],
            "language_id" => ["required", "exists:languages,id", "uuid"],
            "permissions" => Rule::requiredIf(function () {
                $isPermissionsSkipped = (bool)RequestFacade::input("isPermissionsSkipped");
                $permissionType = (string)RequestFacade::input("permission_type");
                return $permissionType === "manually" && $isPermissionsSkipped === false;
            }),
            "permissions.*.action_id" => ["exists:actions,id"],
            "permissions.*.sub_system_id" => ["exists:sub_systems,id"],
            "permissions.*.system_id" => ["exists:systems,id"],
        ];
    }

    /**
     * Store user into DB
     *
     * @param Request $request
     * @param string $userPassword
     * @return User
     * @throws Exception
     */
    private function storeUser(Request $request, string $userPassword): User
    {
        $imagePath = "";
        try {
            $userModel = new User();
            $attributes = $request->only($userModel->getFillable());
            list($dateRange, $timeRange) = $this->getUserTimeSchedule($request);
            $attributes["date_range"] = $dateRange;
            $attributes["time_range"] = $timeRange;
            $attributes["password"] = Hash::make($userPassword);
            $attributes["code"] = $this->randomUserCode();
            $attributes["birth_date"] = Carbon::create($request->input("birth_date"));
            $attributes["change_password"] = $request->boolean("change_password");


            $attributes["created_by"] = $request->user()->id;
            $enLanguage = Language::where(['name' => 'English'])->first();
            $usCountry = Country::where(['name' => 'United States'])->first();
            $countryLanguage = DB::table('country_language')->where(['country_id' => $usCountry->id, 'language_id' => $enLanguage->id])->first();
            $translatedLanguage = TranslatedLanguage::where("country_language_id", $countryLanguage->id)->first();
            if ($translatedLanguage) {
                $attributes["translated_language_id"] = $translatedLanguage->id;
            }

            // // Store file in file management system
            // $storeSystemFile = new Filesystem();
            // $filename = $request->input("firstname") . ' ' . $request->input("lastname");
            // $systemFileModel = $storeSystemFile->storeSystemFile(
            //     $filename,
            //     'Users',
            //     $request->file('image'),
            //     $request->input('companies')[0]
            // );
            // $filenameSubStr = explode('storage/', $systemFileModel->path);
            // $attributes["image"] = $filenameSubStr[1];
            // comment
            $this->prefix                       = $this->filePath;
            $imagePath                          = $this->storeFile($request->file('image'));
            $attributes["image"]                = $imagePath;

            $userData = $userModel->create($attributes);
            // $systemFileModel->relateable_type = User::class;
            // $systemFileModel->relateable_id = $userData->id;
            // $systemFileModel->save();

            return $userData;
        } catch (Exception $exception) {
            if ($imagePath) {
                $this->deleteFile($imagePath);
            }
            throw new Exception($exception);
        }
    }


    /**
     * Check to generate random password or get entered password
     * @param Request $request
     * @return string
     */
    private function userPassword(Request $request): string
    {
        if ($request->boolean("generate_password")) {
            return Str::random();
        }
        return $request->input("password");
    }

    private function paginateUsersOfLoggedInCompany(Request $request)
    {
        if ($request->get('company_id')) {
            $departments = User::whereHas('companies', function (Builder $builder) use ($request) {
                $builder->whereHas('users', function (Builder $builder) use ($request) {
                    $builder->where('users.id', $request->user()->id);
                });
                $builder->where('companies.id', $request->get('company_id'));
            })
                ->where('status', 'active')
                ->get(['id', 'code', 'firstname', 'lastname', 'image']);
        } else {
            $departments = User::whereHas('companies', function (Builder $builder) use ($request) {
                $builder->whereHas('users', function (Builder $builder) use ($request) {
                    $builder->where('users.id', $request->user()->id);
                });
            })
                ->where('status', 'active')
                ->with('companies:id,name')
                ->get(['id', 'code', 'firstname', 'lastname', 'image']);
        }
        return $this->successResponse($departments, Response::HTTP_OK);
    }

    /**
     * Send user password to user email
     *
     * @param User $user
     * @param string $password
     * @throws Exception
     */
    public function emailUserPassword(User $user, string $password)
    {
        try {
            Mail::to([$user->email])->send(new UserPasswordMail($password));
        } catch (\Exception $exception) {
            throw new \Exception($exception, 100);
        }
    }

    /**
     * Analysis and return schedule type and schedule end time zone
     * @param Request $request
     * @return ?array
     */
    private function getUserTimeSchedule(Request $request): ?array
    {
        $scheduleType = $request->input("schedule_type");
        if ($scheduleType === "unlimited") {
            return null;
        }
        $dateRange = json_encode($request->input("dateRange"));
        $timeRange = json_encode($request->input("timeRange"));

        return array($dateRange, $timeRange);
    }

    /**
     * Generate Random Code for user
     * @return int
     */
    private function randomUserCode(): int
    {
        return rand(10000, 8999999999);
    }

    public function update(Request $request): JsonResponse
    {
        $newImagePath = null;
        try {
            DB::beginTransaction();

            $user = User::query()->find($request->input("id"));
            $attributes = $request->only($user->getFillable());
            $attributes["updated_by"] = $request->user()->id;
            list($dateRange, $timeRange) = $this->getUserTimeSchedule($request);
            $attributes["date_range"] = $dateRange;
            $attributes["time_range"] = $timeRange;

            if ($request->hasFile("image") && is_file($request->file("image"))) {
                // // Store file in file management system
                // $storeSystemFile = new Filesystem();
                // $userImage = SystemFile::whereHasMorph('relateable', User::class)->where('relateable_id', $user->id)->latest()->first();
                // $filename = $request->input("firstname") . ' ' . $request->input("lastname");
                // if ($userImage) {
                //     $filename = $storeSystemFile->getStringParenthesisValue($userImage->name, $filename);
                // }

                // $systemFileModel = $storeSystemFile->storeSystemFile($filename, 'Users', $request->file('image'), $request->input('companies')[0]);
                // $filenameSubStr = explode('storage/', $systemFileModel->path);
                // $attributes["image"] = $filenameSubStr[1];

                // $systemFileModel->relateable_type = User::class;
                // $systemFileModel->relateable_id = $user->id;
                // $systemFileModel->save();

                $this->prefix = $this->filePath;
                $newImagePath = $this->storeAndRemove($request->file("image"), $user->getRawOriginal("image"));
                $attributes["image"] = $newImagePath;
            } else {
                unset($attributes["image"]);
            }
            unset($attributes["created_by"]);
            unset($attributes["status"]);
            unset($attributes["currency_id"]);
            $user->tokens()->delete();
            $user->update($attributes);

            // check and store user permissions
            $this->storeUserPermissions($user, $request, true);

            if ($user->hasPersonalStorage()) {
                $this->storeDefaultStorage($user);
            }

            DB::commit();
            broadcast(new Updated('user', $user->id, 'updated'));
            return $this->successResponse(null, Response::HTTP_OK);
        } catch (Exception $exception) {
            if ($newImagePath) {
                $this->deleteFile($newImagePath);
            }
            DB::rollBack();
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function updateRules(Request $request)
    {
        $userId = $request->get("id");
        $validationRules = $this->storeRules();
        $validationRules["username"] = ['required', 'string', 'min:2', 'max:32', "unique:users,username," . $userId];
        $validationRules["email"] = ['required', 'email', 'unique:users,email,' . $userId];
        $validationRules["phone"] = ['required', 'unique:users,phone,' . $userId];
        $validationRules["whatsapp"] = ['required', 'unique:users,whatsapp,' . $userId];

        unset($validationRules["confirm_password"]);
        unset($validationRules["password"]);
        unset($validationRules["change_password"]);
        unset($validationRules["email_password"]);
        unset($validationRules["generate_password"]);


        $imageValidations = ["required"];
        if ($request->hasFile("image") && is_file($request->file("image"))) {
            $imageValidations[] = "image";
            $imageValidations[] = "mimes:jpeg,png,jpg";
        }

        $validationRules["image"] = $imageValidations;
        $request->validate($validationRules);
    }

    // check for uniqueness columns and values is already exists in database or not
    public function checkUniqueness(Request $request): JsonResponse
    {
        $columns = $request->all();
        $userId = $request->query->get("userId");
        $hasUserId = $request->query->has("userId");
        $result = [];

        foreach ($columns as $column) {

            $column = collect($column);
            $columnName = $column->get("columnName");
            $columnValue = $column->get("value");

            if ($hasUserId) {
                $isExists = User::query()->where($columnName, $columnValue)->where("id", "!=", $userId)->exists();
            } else {
                $isExists = User::query()->where($columnName, $columnValue)->exists();
            }
            $result[$columnName] = $isExists;
        }

        return response()->json($result);
    }


    // check the uniqueness of columns and its related values in database
    public function checkUniquenesOfCulumnsWithIndex(Request $request): JsonResponse
    {
        $result = [];
        $columns = $request->all();
        foreach ($columns as $column) {
            $column = collect($column);
            $isExists = User::where($column->get("name"), $column->get("value"))->exists();
            $result[] = [
                'column' => $column->get("name"),
                'if_exists' => $isExists,
                'column_value' => $column->get('value'),
                'index' => $column->get('index')
            ];
        }
        unset($result['']);
        return response()->json($result);
    }

    /**
     * return relations
     * @return string[]
     */
    private function getRelations(): array
    {
        return [
            "teams:id,name,code",
            "roles:id,name,code",
            "language:id,name,native",
            "country:id,name,iso2,iso3,national",
            "state:id,name",
            "currentCountry:id,name",
            "companies:id,name,email,logo",
            "updatedBy",
            'reasons',
            'changedBy:id,firstname,lastname',
            "createdBy",
        ];
    }

    private function getFileUsersBasedOnComapnyIds($request): JsonResponse
    {
        $company_ids = $request->query->get('company_ids_for_files');

        $users = User::whereHas('companies', function (Builder $query) use ($company_ids) {
            $query->whereIn('company_id', $company_ids);
        })->whereHas('permissions', function (Builder $builder) {
            $builder->whereHas('sub_system', function (Builder $builder) {
                //1. with permission of my order only,
                $builder->where('phrase', 'shared_files');
            });
        })->get();
        return \response()->json(['users' => $users], Response::HTTP_OK);
    }

    private function getUsersBasedOnComapnyIds($request): JsonResponse
    {
        $company_ids = $request->query->get('company_ids');

        $users = User::whereHas('companies', function (Builder $query) use ($company_ids) {
            $query->whereIn('company_id', $company_ids);
        })->get();
        return \response()->json(['result' => true, 'users' => $users], Response::HTTP_OK);
    }

    private function getUsersBasedOnTeams($request): JsonResponse
    {
        $team_ids = $request->query->get('team_ids');
        $users = User::whereHas('teams', function (Builder $query) use ($team_ids) {
            $query->whereIn('team_id', $team_ids);
        })
            ->select(['id', 'firstname', 'lastname', 'image', 'code'])
            ->get();
        return \response()->json(['result' => true, 'users' => $users], Response::HTTP_OK);
    }

    private function getUsersBasedOnRoles($request): JsonResponse
    {
        $role_ids = $request->query->get('role_ids');
        $users = User::whereHas('roles', function (Builder $query) use ($role_ids) {
            $query->whereIn('role_id', $role_ids);
        })
            ->select(['id', 'firstname', 'lastname', 'image', 'code'])
            ->get();
        return \response()->json(['result' => true, 'users' => $users], Response::HTTP_OK);
    }

    private function getUsersBasedOnDepartmentIds($request): JsonResponse
    {
        $department_ids = $request->query->get('department_ids');
        $teams = Team::whereHas('departments', function (Builder $query) use ($department_ids) {
            $query->whereIn('department_id', $department_ids);
        })->get()->pluck('id');
        $roles = Role::whereHas('departments', function (Builder $query) use ($department_ids) {
            $query->whereIn('department_id', $department_ids);
        })->get()->pluck('id');
        $teamUsers = User::whereHas('teams', function (Builder $query) use ($teams) {
            $query->whereIn('team_id', $teams);
        })->select(['id', 'firstname', 'lastname', 'image', 'code'])->get();
        $roleUsers = User::whereHas('roles', function (Builder $query) use ($roles) {
            $query->whereIn('role_id', $roles);
        })->select(['id', 'firstname', 'lastname', 'image', 'code'])->get();
        $users = [];

        foreach ($teamUsers as $teamUser) {
            if (!in_array($teamUser->id, $this->getUserIds($users))) {
                array_push($users, $teamUser);
            }
        }
        foreach ($roleUsers as $roleUser) {
            if (!in_array($roleUser->id, $this->getUserIds($users))) {
                array_push($users, $roleUser);
            }
        }

        return \response()->json(['result' => true, 'users' => $users], Response::HTTP_OK);
    }

    function getUserIds($users)
    {
        $newUsers = array_map(function ($user) {
            return $user->id;
        }, $users);
        return $newUsers;
    }

    private function getTasksNotAssignedUser($request): JsonResponse
    {
        $team_ids = $request->query->get('team_ids');
        $users = User::with('teams')->whereHas('permissions', function (Builder $builder) {
            $builder->whereHas('sub_system', function (Builder $builder) {
                //1. with permission of my order only,
                $builder->where('name', 'like', '%My Order%');
            });
        })
            ->where('status', 'active');
        if (!empty($team_ids)) {
            $users = $users->orWhereHas('teams', function (Builder $builder) use ($team_ids) {
                $builder->whereHas('permissions', function (Builder $builder) {
                    //2. check if user's team has permission of this part,
                    $builder->whereHas('sub_system', function (Builder $builder) {
                        $builder->where('name', 'like', '%My Order%');
                    });
                })
                    ->whereIn('teams.id', $team_ids);
            });
        }
        $users = $users->with(['teams'])
            ->select(['id', 'code', 'firstname', 'lastname', 'image'])
            ->get();

        return \response()->json(['users' => $users], Response::HTTP_OK);
    }


    // update euser permission rules and validations
    public function permissionRules()
    {
        return [
            "permission_type" => ["required"],

            "dateRange" => Rule::requiredIf(function () {
                $scheduleType = (string)RequestFacade::input("schedule_type");
                return $scheduleType !== "unlimited";
            }),

            "timeRange" => Rule::requiredIf(function () {
                $scheduleType = (string)RequestFacade::input("schedule_type");
                return $scheduleType !== "unlimited";
            }),


            "roles" => Rule::requiredIf(function () {
                $isPermissionsSkipped = (bool)RequestFacade::input("isPermissionsSkipped");
                $permissionType = (string)RequestFacade::input("permission_type");

                return $permissionType === "by_role" && $isPermissionsSkipped === false;
            }),
            "roles.*" => ["exists:roles,id", "uuid"],

            "teams" => Rule::requiredIf(function () {
                $isPermissionsSkipped = (bool)RequestFacade::input("isPermissionsSkipped");
                $permissionType = (string)RequestFacade::input("permission_type");

                return $permissionType === "by_team" && $isPermissionsSkipped === false;
            }),
            "teams.*" => ["exists:teams,id", "uuid"],

            "companies" => ["required", "array"],
            "companies.*" => ["exists:companies,id", "uuid"],

            "permissions" => Rule::requiredIf(function () {
                $isPermissionsSkipped = (bool)RequestFacade::input("isPermissionsSkipped");
                $permissionType = (string)RequestFacade::input("permission_type");

                return $permissionType === "manually" && $isPermissionsSkipped === false;
            }),
            "permissions.*.action_id" => ["exists:actions,id"],
            "permissions.*.sub_system_id" => ["exists:sub_systems,id"],
            "permissions.*.system_id" => ["exists:systems,id"],
        ];
    }

    // update only user permission
    public function editUserPermissions(Request $request, string $userId)
    {
        try {
            DB::beginTransaction();
            $user = User::query()->find($userId);
            $attributes = [];
            $attributes["updated_by"] = $request->user()->id;
            list($dateRange, $timeRange) = $this->getUserTimeSchedule($request);
            $attributes["date_range"] = $dateRange == "null" ? null : $dateRange;
            $attributes["time_range"] = $timeRange == "null" ? null : $timeRange;
            $user->update($attributes);

            if ($user) {
                $this->storeUserPermissions($user, $request, true);
                DB::commit();
                return $this->successResponse(null, Response::HTTP_OK);
            }

            throw new Exception("User not found");
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->getMessage());
        }
    }

    /**
     * store user permissions
     * @throws Exception
     */
    private function storeUserPermissions(User $user, Request $request, $isEdit = false)
    {
        try {
            $companies = $request->input("companies");
            $user->companies()->detach();
            $user->companies()->sync($companies);
            $user->selected_companies = json_encode($companies);
            $user->save();

            $isPermissionsSkipped = $request->boolean("isPermissionsSkipped");
            if ($isPermissionsSkipped === false || $isEdit === true) {

                $roles = $request->input("roles");
                $teams = $request->input("teams");

                $user->roles()->detach();
                $user->roles()->sync($roles);
                $user->teams()->detach();

                if ($teams !== null && count($teams) > 0) {
                    $user->teams()->sync($teams);
                    foreach ($teams as $team) {
                        $team = Team::query()->find($team);
                        $team->number_of_people = $team->number_of_people + 1;
                        $team->save();
                    }
                }

                $permissions = array();
                $systems = $request->input("permissions");
                if ($systems !== null) {
                    foreach ($systems as $system) {
                        $system = json_decode($system, true);
                        $actionSubAction = DB::table("action_sub_system")
                            ->where("sub_system_id", $system['sub_system_id'])
                            ->where("action_id", $system['action_id'])
                            ->get();

                        if ($actionSubAction) {
                            array_push($permissions, [
                                'action_sub_system_id' => $actionSubAction[0]->id,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now(),
                            ]);
                        }
                    }
                    $user->permissions()->detach();
                    $user->permissions()->attach($permissions);
                } else {
                    $user->permissions()->detach();
                }
            }
        } catch (\Exception $exception) {
            throw new \Exception($exception);
        }
    }

    public function show($id): JsonResponse
    {
        $user = User::withTrashed()->with($this->getRelations())->find($id);
        $permissions = $user->userPermissions;
        $newPermissions = [];
        $subSystems = [];
        $actions = [];
        foreach ($permissions as $permission) {
            $actions[$permission->action->name] = $permission->action->subActions->pluck('action');
            $subSystems[$permission->sub_system->name] = $actions;
            $newPermissions[$permission->sub_system->system->name] = $subSystems;
        }
        if ($user) {
            $user['session_activity'] = $this->readFiles($id);
            $user['log_activity'] = $this->getUserLog($id);
            $user['permissions'] = $newPermissions;
            if ($user->hasPersonalStorage()) {
                $user->has_storage = $user->hasPersonalStorage();
                $personal = new Personal();
                $user->storage = $personal->settingsPersonalFiles($user->id);
            }
            return $this->successResponse($user);
        }
        return $this->errorResponse("Not Found");
    }

    // return user details
    public function getUserDetails($userIds): JsonResponse
    {
        $userIds = explode(",", $userIds);
        try {

            $teamRelations = "teams:id,name,code";
            $roleRelations = "roles:id,name,code";
            $departmentRelations = "teams.departments:id,name";
            $companiesRelations = "companies:id,name,logo";
            $countryRelations = "companies.countries:id,name,iso2";
            $companySystemsRelations = "companies.systems:id,phrase,name,logo";
            $companySystemSubSystmesRelations = "companies.systems.subSystems:id,phrase,name,system_id";
            $companySystemSubSystemsActionsRelations = "companies.systems.subSystems.actions:id,phrase,name";
            $companyDepartments = "companies.departments:id,name,code";
            $companyDepartmentTeams = "companies.departments.teams:id,name,code";

            $systemRelations = "permissions.action";
            $SubSystemRelations = "permissions.sub_system";
            $actionRelations = "permissions.action.subActions";


            $relations = [
                $teamRelations,
                $departmentRelations,
                $roleRelations,
                $companiesRelations,
                $countryRelations,
                $systemRelations,
                $SubSystemRelations,
                $actionRelations,
                $companySystemsRelations,
                $companySystemSubSystmesRelations,
                $companySystemSubSystemsActionsRelations,
                $companyDepartments,
                $companyDepartmentTeams,
                "language:id,name,native",
                "country:id,name,iso2,iso3,national",
                "state:id,name",
                "currentCountry:id,name",
            ];

            $user = User::query()->with($relations)->find($userIds);
            return $this->successResponse($user);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }


    public function changeUserStatus(Request $request)
    {
        return $this->changeStatus($request, User::class, 'users', 'reason_user', 'user_id', $this->getRelations(), 'user');
    }

    public function destroy(array $ids, $reasonId)
    {
        return $this->destroyItems(
            User::class,
            $ids,
            'reason_user',
            $reasonId,
            'user_id',
            null,
            $this->getRelations(),
            'user'
        );
    }

    public function search($request): JsonResponse
    {
        $queryBuilder = new UriQueryBuilder(new User(), $request);
        $data = $this->searchCode($queryBuilder->query, $request, $this->getRelations());
        return $data ? response()->json($data, 200) : response()->json(['result' => false], Response::HTTP_NO_CONTENT);
    }


    public function readFiles($id)
    {
        $path = storage_path() . '/logs/sessionlog-' . date('Y-m') . '.log';
        $logCollection = [];
        if (file_exists($path)) {
            $logFile = file($path);
            foreach ($logFile as $index => $line) {
                $string = substr($line, strpos($line, "{"));
                $logdata = json_decode($string);
                $logdata->date = substr($line, 1, 10);
                $logdata->time = substr($line, 11, 9);
                if ($logdata->user_id == $id) {
                    $logCollection[] = $logdata;
                }
            }
        }

        return $logCollection;
    }


    public function getUserLog($user_id)
    {
        # code...
        $start_date = date("Y-m-01");
        $end_date = date("Y-m-t");
        $i = 0;
        $logCollection = [];
        while ($end_date > $start_date) {
            $start_date = date("Y-m-d", strtotime("+" . $i . " day", strtotime($start_date)));
            $path = storage_path() . '/logs/dailylogs-' . $start_date . '.log';
            if (file_exists($path)) {
                # code...
                $logFile = file($path);
                foreach ($logFile as $index => $line) {
                    $string = substr($line, strpos($line, "{"));
                    $logdata = json_decode($string);
                    $logdata->date = substr($line, 1, 10);
                    $logdata->time = substr($line, 11, 9);
                    if ($logdata->user_id == $user_id) {
                        $logCollection[] = $logdata;
                    }
                }
            }
            $i = 1;
        }

        return $logCollection;
    }

    public function storeDefaultStorage(User $user)
    {
        $is_exists = FileLimitionForUsers::where('user_id', $user->id)->exists();
        if (!$is_exists) {
            $attributes = [
                "user_id" => $user->id,
                "limited_size" => 2147483648,
            ];
            return FileLimitionForUsers::create($attributes);
        }
        return null;
    }


    public static function getUsersByPermissions($sub_system, $sub_action)
    {
        $subSystem = SubSystem::where('scope', $sub_system)
            ->select('id', 'name')->first();
        $subAction = SubAction::where('name', $sub_action)
            ->select('id', 'name')
            ->with([
                'actions:id',
                'actions.ActionsSubSystem' => function ($q) use ($subSystem) {
                    $q->where('sub_system_id', $subSystem->id);
                    $q->with(
                        'users:id,code,firstname,lastname,image',
                        'teams:id',
                        'teams.users:id,code,firstname,lastname,image',
                        'roles:id',
                        'roles.users:id,code,firstname,lastname,image'
                    );
                }
            ])->first();
        return self::mergeUsers($subAction);
    }

    private static function mergeUsers($subAction)
    {
        $users = [];
        foreach ($subAction->actions as $action) {
            foreach ($action->ActionsSubSystem as $action_sub) {
                $users = array_merge($users, $action_sub->users->toArray());
                foreach ($action_sub->teams as $team) {
                    $users = array_merge($users, $team->users->toArray());
                }
                foreach ($action_sub->roles as $role) {
                    $users = array_merge($users, $role->users->toArray());
                }
            }
        }
        return collect($users)->unique("id");
    }
}
