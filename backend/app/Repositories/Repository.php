<?php


namespace App\Repositories;


use Exception;
use Carbon\Carbon;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use App\Models\Phrase;
use App\Events\Updated;
use App\Traits\FileTrait;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\DesignRequest;
use Illuminate\Http\Response;
use App\Models\LanguagePhrase;
use Illuminate\Http\JsonResponse;
use App\Models\DesignRequestOrder;
use App\Models\StatusTimeConsumed;
use Illuminate\Support\Facades\DB;
use App\Models\DesignRequestReject;
use Illuminate\Support\Facades\Auth;
use App\Models\DesignRequestOrderUser;
use Illuminate\Database\Eloquent\Model;
use App\Models\DesignRequestRejectReason;

abstract class Repository
{

    use FileTrait;

    // delete or soft delete items
    public function destroyItems($class, array $ids, $reasonTable, $reasons = null, $tableId, $imageColumn = null, $relations = [],  $channelName, $request = null): JsonResponse
    {

        return $this->transaction(function () use ($class, $ids, $reasons, $reasonTable, $tableId, $imageColumn, $channelName, $relations, $request) {
            $items = $class::withTrashed()->find($ids);
            $items->each(function (Model $query)  use ($reasons, $reasonTable, $tableId, $imageColumn, $channelName, $relations, $request) {
                if ($query->trashed()) {
                    if ($imageColumn) {
                        $this->deleteFile($query->getRawOriginal($imageColumn));
                    }
                    if ($channelName == 'company') {
                        $this->deleteCompanyStaff($query->id, $request);
                    } else if ($channelName == 'department') {
                        $this->deleteDepartmentStaff($query->id, $request);
                    } else if ($channelName == 'team') {
                        $this->deleteTeamStaff($query->id, $request);
                    } else if ($channelName == 'role') {
                        $this->deleteRoleStaff($query->id, $request);
                    }
                    $id = $query->id;
                    $query->forceDelete();
                    broadcast(new Updated($channelName, $id, 'deleted'));
                } else {
                    if (gettype($reasons) === 'array') {
                        if ($reasonTable == 'design_request_reason') {
                            $designRequestOrder = DesignRequestOrder::where('design_request_id', $query->id)->first();
                            DB::table('design_request_order_user')->where('design_request_order_id', $designRequestOrder->id)->delete();

                            $reject = DesignRequestReject::create([
                                'status'                => 'removed',
                                'design_request_id'     => $query->id,
                                'changed_by'            => Auth::id(),
                                'created_at'            => Carbon::now(),
                                'updated_at'            => Carbon::now(),
                            ]);
                            foreach ($reasons as $reason) {
                                DesignRequestRejectReason::create([
                                    'reason_id' => $reason,
                                    'design_request_reject_id'  => $reject->id
                                ]);
                            }
                        } else {
                            foreach ($reasons as $reason) {
                                DB::table($reasonTable)->insert([
                                    'status'        => 'removed',
                                    'reason_id'     => $reason,
                                    $tableId        => $query->id,
                                    'changed_by'    => Auth::id(),
                                    'created_at'    => Carbon::now(),
                                    'updated_at'    => Carbon::now(),
                                ]);
                            }
                        }
                    }
                    $currentStatus = $query->status;
                    if ($reasonTable == 'design_request_reason')
                        $query->prev_status  = $currentStatus;

                    $query->status  = 'removed';
                    $query->save();
                    $query->delete();
                    broadcast(new Updated($channelName, [
                        "current_status" => $currentStatus,
                        "item" => $query->id,
                        "new_status" => 'removed',
                    ], 'status-changed'));
                }
            });
        }, Response::HTTP_OK);
    }

    // change status of items
    public function changeStatus(Request $request, $class, $tableName, $reasonTable, $tableId, $relations = [], $channelName = '', $hasTrashed = true)
    {
        $this->statusValidation($request, $tableName);
        try {
            DB::beginTransaction();

            $type = $request->input('type');
            $item_id = $request->input('item_id');
            if ($type == 'status' || $type == 'pending') {     // Check whether change type is boolean or not...
                $item           = $hasTrashed ? $class::withTrashed()->findOrFail($item_id) : $class::findOrFail($item_id);
                $currentStatus  = $item->status;
                $newStatus      = $request->status;
                if ($currentStatus == 'pending' && $tableName == 'users') {       // User status can not be changed without permission
                    $user   = User::withCount('teams')->withCount('roles')->withCount('permissions')->find($item_id);
                    if ($user->teams_count == 0 && $user->roles_count == 0 && $user->permissions_count == 0) {
                        DB::rollBack();
                        return $this->errorResponse(["result" => false], Response::HTTP_TEMPORARY_REDIRECT);
                    }
                }


                $isPossible     = $this->checkPossibility($currentStatus, $newStatus);

                if ($tableName === 'translated_languages') {     // For langauge activation
                    if ($currentStatus === 'active') {
                        $active_languages = $class::where('status', 'active')->count();
                        $isPossible             = $active_languages > 1 ? true : false;
                    }
                    // if ($newStatus === 'active') {
                    //     $totalWords       = Phrase::count();
                    //     $totalTranslated  = LanguagePhrase::where('translated_language_id', $item->id)->with('phrase')->count();
                    //     $isPossible             = $totalWords === $totalTranslated ? true : false;
                    // }
                }



                if ($isPossible) {
                    $item->status = $newStatus;
                    $item->save();
                    broadcast(new Updated($channelName, [
                        "item" => $item->id,
                        "current_status" => $currentStatus,
                        "new_status" => $newStatus,
                    ], 'status-changed'));


                    if ($currentStatus != 'pending') {    // Reason is not needed for pending status
                        DB::table($reasonTable)->insert([
                            'status'        => $newStatus,
                            'reason_id'     => $request->reason_id,
                            $tableId        => $item->id,
                            'changed_by'    => Auth::id(),
                            'created_at'    => Carbon::now(),
                            'updated_at'    => Carbon::now(),
                        ]);
                    }
                    if ($currentStatus == 'removed') {    // Restore if current status is removed
                        $item->restore();
                    }

                    DB::commit();
                    return $this->successResponse($item, Response::HTTP_OK);
                } else {
                    DB::rollBack();
                    return $this->errorResponse(["Invalid"], Response::HTTP_NOT_ACCEPTABLE);
                }
            } else if ($type == 'toggle') {      // Toggle boolean statuses like (is_pinned, tracing_status)
                $item =  $class::where('id', $item_id)->update(array($request->input('column') => $request->input('toggle_status')));
                DB::commit();
                return $this->successResponse($item, Response::HTTP_OK);
            }
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->getMessage());
        }
    }

    private function statusValidation($request, $tableName)
    {
        $request->validate([
            'type'              => ['required', 'string'],
            'status'            => ['required_if:type,status', 'string'],
            'item_id'           => ['required', 'exists:' . $tableName . ',id', 'uuid'],
            'reason_id'         => ['required_if:type,status', 'exists:reasons,id', 'uuid', 'nullable'],
            'reasons'           => ['required_if:type,multiple', 'array'],
            'toggle_status'     => ['required_if:type,toggle', 'boolean'],
            'column'            => ['required_if:type,toggle', 'string'],
        ]);
    }

    // Check whether status is changeable or not
    private function checkPossibility($currentStatus, $newStatus)
    {
        $flag           = false;

        switch ($currentStatus) {
            case 'inactive':
                if (in_array($newStatus, ['active', 'blocked', 'removed']))                 $flag = true;
                break;
            case 'active':
                if (in_array($newStatus, ['inactive', 'blocked', 'removed']))               $flag = true;
                break;
            case 'pending':
                if (in_array($newStatus, ['active', 'inactive', 'blocked', 'removed', 'approved', 'rejected']))     $flag = true;
                break;
            case 'warning':
            case 'blocked':
                if (in_array($newStatus, ['active', 'inactive']))                           $flag = true;
                break;
            case 'removed':
                if (in_array($newStatus, ['active', 'inactive', 'live', 'archive', 'cancelled', 'onhold']))        $flag = true;
                break;
            case 'live':
                if (in_array($newStatus, ['archive', 'removed']))                           $flag = true;
                break;
            case 'archive':
            case 'cancelled':
                if (in_array($newStatus, ['live', 'removed']))                              $flag = true;
                break;
            case 'completed':
            case 'published':
            case 'expired':
                if (in_array($newStatus, ['removed', 'cancelled']))  $flag = true;
                break;
            case 'onhold':
                if (in_array($newStatus, ['published', 'removed', 'cancelled']))  $flag = true;
                break;
            case 'rejected':
                if (in_array($newStatus, ['approved']))  $flag = true;
                break;
            case 'approved':
                if (in_array($newStatus, ['rejected']))  $flag = true;
                break;
            default:
                $flag = false;
        }
        return $flag;
    }

    // restore all soft deleted items
    public function restoreItems($class, array $ids): JsonResponse
    {
        return $this->transaction(function () use ($class, $ids) {
            $items = $class::onlyTrashed()->find($ids);
            $items->each(function (Model $query) {
                $query->restore();
            });
        }, Response::HTTP_OK);
    }

    /**
     * Do database transaction with a custom closure
     *
     * @param \Closure $closure
     * @param int $statusCode
     * @return JsonResponse
     */
    public function transaction(\Closure $closure, int $statusCode = Response::HTTP_CREATED): JsonResponse
    {
        try {
            return DB::transaction(function () use ($closure, $statusCode) {
                $result = call_user_func($closure);
                return $this->successResponse($result, $statusCode);
            });
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }


    /**
     * Return error response message
     * @param $message
     * @param int $statusCode
     * @return JsonResponse
     */
    public function errorResponse($message, int $statusCode = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return response()->json(["result" => false, "message" => $message], $statusCode);
    }

    /**
     * Return success response
     * @param $data
     * @param int $statusCode
     * @return JsonResponse
     */
    public function successResponse($data, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return response()->json(["result" => true, "data" => $data], $statusCode);
    }

    public function filterRecords($query, $request, $columns = [], $idType = 'code')
    {
        $data   = $request->all();
        $ids    = $request->input('ids');


        foreach ($data as $key => $value) {
            if (!empty($value)) {
                if (gettype($value) === 'string') {
                    if (str_contains($value, 'like@@')) {
                        $likeValue = substr($value, 6);
                        $query = $query->where($key, 'like', '%' . $likeValue . '%');
                    } else if (str_contains($value, 'exact@@')) {
                        $exactValue = substr($value, 7);
                        $query = $query->where($key, $exactValue);
                    }
                }


                if (gettype($value) === 'array' && \in_array('symbol', $value) && $value[2] !== 'undefined') {  // For smaller than and greater than operations
                    if ($value[2] !== null && $value[2] !== 'undefined' && in_array($value[1], ['<=', '>=', '==', '>', '<']))
                        $query = $query->where($key, $value[1], $value[2]);
                    continue;
                }

                if (gettype($value) === 'array' && in_array('range', $value)) {    // For all betweens (date, range)
                    $isDate = in_array('date', $value) ? true : false;
                    $isDate ? array_splice($value, 0, 2) : array_splice($value, 0, 1);

                    if (count($value) === 1)
                        $value[] = $isDate ? '1800-11-11' : PHP_INT_MIN;

                    $value[0]   = is_null($value[0]) && !$isDate ? PHP_INT_MIN : $value[0];
                    $value[1]   = is_null($value[1]) && !$isDate ? PHP_INT_MAX : $value[1];

                    $value[0]   = is_null($value[0]) && $isDate ? '1800-11-11' : $value[0];
                    $value[1]   = is_null($value[1]) && $isDate ? '4000-12-12' : $value[1];

                    $from       = $isDate ? date(min($value)) : min($value);
                    $to         = $isDate ? date(max($value)) : max($value);
                    $query      = $isDate ? $query->whereBetween(DB::raw("DATE($key)"), array($from, $to)) : $query->whereBetween($key, array($from, $to));
                    continue;
                }

                if ($key === 'searchContent') {
                    $query = $this->searchContent($query, $columns, $value);
                    continue;
                }

                if (gettype($value) === 'array' && in_array('time_range', $value)) {
                    $days       = $value[1];
                    $hours      = $value[2];
                    $mintues    = $value[3];
                    $opration    = $value[4];

                    $totalTime = $mintues + 60 * $hours + 24 * 60 * $days;
                    $query = $query->where(DB::raw('TIMESTAMPDIFF(MINUTE,`created_at`,CURRENT_TIMESTAMP())'), $opration, $totalTime);
                    continue;
                }


                if ($key === 'task_prograss') {
                    $data = DesignRequestOrderUser::select('design_request_order_id', DB::raw('ROUND(avg(task_prograss))'))->groupBy('design_request_order_id')->get()->where(DB::raw('ROUND(avg(task_prograss))'), $value);
                    $newIds = [];
                    foreach ($data as  $item) {
                        $newIds[] = $item->design_request_order_id;
                    }

                    $query = $query->whereHas('designRequestOrder', function ($q) use ($newIds, $query) {
                        if ($newIds != null) {
                            $query = $q->whereIn('id', $newIds);
                        } else {
                            $query = $q->where('id', 123);
                        }
                    });
                    continue;
                }


                if (gettype($value) === 'array' && in_array('whereHasTwo', $value) && count($value) === 4) {
                    $query = $query->whereHas($value[1], function ($query) use ($value, $key) {
                        $query->whereHas($value[2], function ($q1) use ($value, $key) {
                            $q1->where(function ($q2) use ($value, $key) {
                                $q2->where($key, 'like', '%' . $value[3] . '%');
                            });
                        });
                    });
                    continue;
                }

                if (gettype($value) === 'array' && in_array('whereHasOne', $value) && count($value) === 3) {
                    $query = $query->whereHas($value[1], function ($q1) use ($value, $key) {
                        $q1->where(function ($q2) use ($value, $key) {
                            $q2->where($key, 'like', '%' . $value[2] . '%');
                        });
                    });
                    continue;
                }

                if (gettype($value) === 'array' && (in_array('whereHas', $value) || in_array('whereHasIn', $value)) && count($value) === 3) {
                    $query = $query->whereHas($value[1], function ($query) use ($value, $key) {
                        in_array('whereHasIn', $value) ? $query->whereIn($key, $value[2]) : $query->where($key, $value[2]);
                    });
                    continue;
                }



                if ($key === 'include' && $ids !== null) {
                    if ($idType == 'code') {
                        $newFormatedIds = [];
                        foreach ($ids as $id) {
                            array_push($newFormatedIds, reverseUniqueCode($id));
                        }
                    } else {
                        $newFormatedIds = $ids;
                    }
                    $query = $value == 1 ? $query->whereIn($idType, $newFormatedIds) : $query->whereNotIn($idType, $newFormatedIds);    // For ID and code
                }
            }
        }
        return $query;
    }


    public function searchContent($query, $columns, $value)
    {
        // Columns should be like this format:      ====> ['firstname', 'lastname', 'whereHasOne, company, name, email, password', 'whereHasTwo, countryLanguage, language, name, native']

        if ($value) {
            $query = $query->where(function ($q1) use ($columns, $value) {
                for ($i = 0; $i < count($columns); $i++) {
                    $item = array_map('trim', explode(',', $columns[$i]));
                    $len = count($item);
                    if ($len > 1) {
                        if ($item[0] === 'whereHasTwo') {
                            $q1 = $q1->orWhereHas($item[1], function ($q2) use ($value, $item) {
                                $q2->whereHas($item[2], function ($q3) use ($value, $item) {
                                    $q3->where(function ($q4) use ($value, $item) {
                                        for ($j = 0; $j < count($item); $j++) {
                                            if (in_array($j, [0, 1, 2])) continue;
                                            $j === 3 ? $q4->where($item[$j], 'like', '%' . $value . '%') : $q4->orWhere($item[$j], 'like', '%' . $value . '%');
                                        }
                                    });
                                });
                            });
                            continue;
                        }
                        if ($item[0] === 'whereHasOne') {
                            $q1 = $q1->orWhereHas($item[1], function ($q2) use ($value, $item) {
                                $q2->where(function ($q3) use ($value, $item) {
                                    for ($j = 0; $j < count($item); $j++) {
                                        if (in_array($j, [0, 1])) continue;
                                        $j === 3 ? $q3->where($item[$j], 'like', '%' . $value . '%') : $q3->orWhere($item[$j], 'like', '%' . $value . '%');
                                    }
                                });
                            });
                            continue;
                        }
                    }

                    $q1 = $i === 0 ? $q1->where($columns[$i], 'like', '%' . $value . '%') : $q1->orWhere($columns[$i], 'like', '%' . $value . '%');
                }
                return $q1;
            });
        }

        return $query;
    }


    public function fetchDataAccordingToStatus($query, $key, $customStatus = [], $custom_status_column_name = "status")
    {
        if ($key !== 'all') {
            if ($key === 'removed' || $key === 'deleted')
                return $query->onlyTrashed();

            if (count($customStatus) > 0) {
                $len = count($customStatus);
                for ($i = 0; $i < $len; $i++) {
                    $item = array_map('trim', explode(',', $customStatus[$i]));
                    if ($key === $item[0]) {
                        if (in_array('whereHas', $item)) {
                            return $query->whereHas($item[2]);
                        } else if (in_array('whereHasThree', $item)) {
                            return $query->whereHas($item[2], function ($query) use ($item) {
                                $query->whereHas($item[3], function ($q) use ($item) {
                                    $q->whereHas($item[4]);
                                });
                            });
                        } else if (in_array('whereDoesntHave', $item)) {
                            if ($item[2] == "designRequestOrder.designRequestOrderUser") {
                                $query->where($custom_status_column_name, '!=', 'completed');
                            }
                            return $query->whereDoesntHave($item[2]);
                        }
                        return $query->where($item[1], $item[2]);
                    }
                }
            }
            if ($key == "current") {
                return $query->whereDeletedAt(null);
            }
            return $query->where($custom_status_column_name, $key);
        }
        return $query;
    }

    // Return Tab Count Badge
    public function getStatusCount($query, $data, $extraData, $hasTrashed = true, $custom_status_column_name = "status")
    {
        for ($i = 0; $i < count($extraData); $i++) {
            $clonedQuery = clone $query;

            $item = array_map('trim', explode(',', $extraData[$i]));
            if (in_array('allTotal', $item))
                $data->allTotal = $hasTrashed ? $clonedQuery->withTrashed()->count() : $clonedQuery->count();
            else if (in_array('removedTotal', $item))
                $data->removedTotal = $clonedQuery->onlyTrashed()->count();
            else if (in_array('currentTotal', $item))
                $data->currentTotal = $clonedQuery->whereDeletedAt(null)->count();
            else {
                if (in_array('whereHas', $item)) {
                    $data->{$item[0]} = $clonedQuery->whereHas($item[2])->count();
                } else if (in_array('whereHasThree', $item)) {
                    $data->{$item[0]} = $clonedQuery->whereHas($item[2], function ($clonedQuery) use ($item) {
                        $clonedQuery->whereHas($item[3], function ($q) use ($item) {
                            $q->whereHas($item[4]);
                        });
                    })->count();
                } else if (in_array('whereDoesntHave', $item)) {
                    if ($item[2] == "designRequestOrder.designRequestOrderUser") {
                        $data->{$item[0]} = $clonedQuery->where($custom_status_column_name, '!=', 'completed')->whereDoesntHave($item[2])->count();
                    } else {
                        $data->{$item[0]} = $clonedQuery->whereDoesntHave($item[2])->count();
                    }
                } else
                    $data->{$item[0]} = $clonedQuery->where($item[1], $item[2])->count();
            }
        }
        return $data;
    }


    public function searchCode(
        $query,
        $request,
        $relations = [],
        $hasTrashed = true,
        $codeType = 'code',
        $whereRelationName = false,
        $applyReverseUniqueCode = true
    ) {
        $code = $request->input('code');
        if ($code) {
            if ($hasTrashed) $query->withTrashed();
            if (count($relations) > 0)
                $query = $query->with($relations);
            $query = $this->filterRecords($query, $request, [], $codeType);
            if (!$whereRelationName) {
                $code = $applyReverseUniqueCode ? reverseUniqueCode($code) : $code;
                $item = $query->where($codeType, $code)->first();
            } else {
                $item  = $query->whereRelation($whereRelationName, $codeType, $code)->first();
            }
            if ($item)
                return $item;
        }
        return false;
    }


    private function deleteCompanyStaff($company_id, $request)
    {
        $categories = explode(',', $request->query->get('categories_to_delete'));

        // department
        if (in_array('departments', $categories)) {
            $departments_to_delete = $this->deleteWithSingleId('company', 'department', 'company_department', $company_id);
        }
        // team
        if (in_array('teams', $categories)) {
            $teams_to_delete = $this->teamsToDelete($departments_to_delete);
        }
        // role
        if (in_array('roles', $categories)) {
            $roles_to_delete = $this->rolesToDelete($departments_to_delete);
        }
        // user
        if (in_array('users', $categories)) {
            $users_to_delete = $this->deleteWithSingleId('company', 'user', 'company_user', $company_id);
        }
        // design_request
        if (in_array('design_request', $categories)) {
            DesignRequest::withTrashed()->where('company_id', $company_id)->forceDelete();
        }
        if (in_array('users', $categories)) {
            User::withTrashed()->whereIn('id', $users_to_delete)->forceDelete();
        }
        if (in_array('roles', $categories)) {
            Role::withTrashed()->whereIn('id', $roles_to_delete)->forceDelete();
        }
        if (in_array('teams', $categories)) {
            Team::withTrashed()->whereIn('id', $teams_to_delete)->forceDelete();
        }
        if (in_array('departments', $categories)) {
            Department::withTrashed()->whereIn('id', $departments_to_delete)->forceDelete();
        }
    }

    private function deleteDepartmentStaff($department_id, $request)
    {
        $categories = explode(',', $request->query->get('categories_to_delete'));

        if (in_array('teams', $categories)) {
            $teams_to_delete = $this->teamsToDelete([$department_id]);
        }
        if (in_array('roles', $categories)) {
            $roles_to_delete = $this->rolesToDelete([$department_id]);
        }
        if (in_array('roles', $categories)) {
            Role::withTrashed()->whereIn('id', $roles_to_delete)->forceDelete();
        }
        if (in_array('teams', $categories)) {
            Team::withTrashed()->whereIn('id', $teams_to_delete)->forceDelete();
        }
    }

    private function deleteTeamStaff($team_id, $request)
    {
        $categories = explode(',', $request->query->get('categories_to_delete'));

        if (in_array('users', $categories)) {
            $users_with_teams = $this->deleteWithSingleId('team', 'user', 'team_user', $team_id);

            $users_with_roles = DB::table('role_user')->whereIn('user_id', $users_with_teams)->pluck('user_id');

            $users_with_no_roles = [];

            foreach ($users_with_teams as $team_user_id) {
                if (!in_array($team_user_id, $users_with_roles->toArray())) {
                    array_push($users_with_no_roles, $team_user_id);
                }
            }

            User::withTrashed()->whereIn('id', $users_with_no_roles)->forceDelete();
        }
    }

    private function deleteRoleStaff($role_id, $request)
    {
        $categories = explode(',', $request->query->get('categories_to_delete'));

        if (in_array('users', $categories)) {

            $users_with_roles = $this->deleteWithSingleId('role', 'user', 'role_user', $role_id);

            $users_with_teams = DB::table('team_user')->whereIn('user_id', $users_with_roles)->pluck('user_id');

            $users_with_no_teams = [];

            foreach ($users_with_roles as $user_with_role) {
                if (!in_array($user_with_role, $users_with_teams->toArray())) {
                    array_push($users_with_no_teams, $user_with_role);
                }
            }

            User::withTrashed()->whereIn('id', $users_with_no_teams)->forceDelete();
        }
    }


    private function deleteWithSingleId($table_name1, $table_name2, $relation_name, $id)
    {
        $items = DB::table($relation_name)
            ->where("{$table_name1}_id", $id)
            ->select("{$table_name2}_id")->pluck("{$table_name2}_id");
        $items_to_delete = DB::table($relation_name)
            ->whereIn("{$table_name2}_id", $items)
            ->groupBy("{$table_name2}_id")
            ->select("{$table_name2}_id", DB::raw('count(*) as total'))
            ->havingRaw('COUNT(*) = 1')
            ->pluck("{$table_name2}_id");
        return $items_to_delete;
    }


    private function teamsToDelete($dep_ids)
    {
        $teams_of_deps = DB::table('department_team')
            ->whereIn('department_id', $dep_ids)
            ->groupBy('team_id')
            ->pluck('team_id');

        $teams_with_rels = DB::table('department_team')
            ->whereIn('team_id', $teams_of_deps)
            ->get();

        $teams_to_delete = [];
        $blacklist = [];

        foreach ($teams_with_rels as $teams_with_rel) {
            if (
                in_array(
                    $teams_with_rel->department_id,
                    gettype($dep_ids) == 'array' ?  $dep_ids : $dep_ids->toArray()
                )
                && !in_array($teams_with_rel->team_id, $blacklist)
            ) {
                if (!in_array($teams_with_rel->team_id, $teams_to_delete)) {
                    $teams_to_delete[$teams_with_rel->team_id] = $teams_with_rel->team_id;
                }
            } else {
                if (in_array($teams_with_rel->team_id, $teams_to_delete)) {
                    unset($teams_to_delete[$teams_with_rel->team_id]);
                    $blacklist[$teams_with_rel->team_id] = $teams_with_rel->team_id;
                }
            }
        }
        return $teams_to_delete;
    }

    private function rolesToDelete($dep_ids)
    {
        $role_of_deps = DB::table('department_role')
            ->whereIn('department_id', $dep_ids)
            ->groupBy('role_id')
            ->pluck('role_id');

        $roles_with_rels = DB::table('department_role')
            ->whereIn('role_id', $role_of_deps)
            ->get();

        $roles_to_delete = [];
        $blacklist = [];

        foreach ($roles_with_rels as $role_with_rel) {
            if (
                in_array(
                    $role_with_rel->department_id,
                    gettype($dep_ids) == 'array' ?  $dep_ids : $dep_ids->toArray()
                )
                && !in_array($role_with_rel->role_id, $blacklist)
            ) {
                if (!in_array($role_with_rel->role_id, $roles_to_delete)) {
                    $roles_to_delete[$role_with_rel->role_id] = $role_with_rel->role_id;
                }
            } else {
                if (in_array($role_with_rel->role_id, $roles_to_delete)) {
                    unset($roles_to_delete[$role_with_rel->role_id]);
                    $blacklist[$role_with_rel->role_id] = $role_with_rel->role_id;
                }
            }
        }
        return $roles_to_delete;
    }
}
