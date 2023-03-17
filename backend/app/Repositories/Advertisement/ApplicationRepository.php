<?php

namespace App\Repositories\Advertisement;

use Exception;
use App\Models\Action;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use App\Repositories\Repository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\Advertisement\Platform;
use App\Models\Advertisement\Application;
use App\Models\SubAction;
use App\Models\SubSystem;
use Illuminate\Support\Facades\Validator;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use App\Repositories\Advertisement\Platforms\TikTok;
use App\Repositories\Advertisement\Platforms\Facebook;
use App\Repositories\Advertisement\Platforms\GoogleAd;
use App\Repositories\Advertisement\Platforms\Snapchat;
use App\Repositories\Advertisement\Platforms\GoogleAnalytics;

/**
 * Class CategoryRepository.
 */
class ApplicationRepository extends Repository
{

    public function paginate(Request $request): JsonResponse
    {
        $key = $request->query->get("key");
        $queryBuilder = new UriQueryBuilder(new Application(), $request);
        $queryBuilder->setRelations($this->getRelations());
        $queryBuilder->setWithTrashed();

        $searchInColumns        = [
            "name", "code", "client_id", "system_status", "platform_id",  "updated_by", "created_by",
        ];
        $queryBuilder->query    = $this->filterRecords($queryBuilder->query, $request, $searchInColumns);
        $statusQuery            = clone  $queryBuilder->query;
        $queryBuilder->query    = $this->fetchDataAccordingToStatus(
            $queryBuilder->query,
            $key,
            [],
            "system_status",
        );

        $queryBuilder->query->orderBy("created_at", 'desc');
        $application         = $queryBuilder->build()->paginate()->getData();
        $extraData = [
            'allTotal',
            'removedTotal',
            "activeTotal, system_status, ACTIVE",
            "tokenExpiredTotal, system_status, TOKEN_EXPIRED",
        ];
        $application = $this->getStatusCount($statusQuery, $application, $extraData, true, "system_status");
        return response()->json($application);
    }

    public function getApplicationToken(Request $request)
    {
        $application_data = $request->input("application_data");
        $platform_id = $application_data["platform"];
        $platform = Platform::find($platform_id);
        $application_data["code"] = $request->input("code");
        $application_data["platform_id"] = $platform_id;
        switch ($platform->platform_name) {
            case 'google ads':
                $request->validate([
                    'application_data' => [
                        "developer_token" => ["required", "min:5"]
                    ],
                ]);
                return GoogleAd::getTokenAndStore($application_data);
            case 'google analytics':
                return GoogleAnalytics::getTokenAndStore($application_data);
            case 'tiktok':
                $request->validate([
                    'application_data' => [
                        "rid" => ["required", "min:5"]
                    ],
                ]);
                return TikTok::getTokenAndStore($application_data);
            case 'snapchat':
                $request->validate([
                    'application_data' => [
                        "organization_id" => ["required", "min:5"]
                    ],
                ]);
                return Snapchat::getTokenAndStore($application_data);
            case 'facebook':
                return Facebook::getTokenAndStore($application_data);
            default:
                return false;
        }
    }


    public function update(Request $request)
    {
        $applicationModel = Application::findOrFail($request->id);
        $attributes = [];
        try {
            $attributes['name'] = $request->name;
            $attributes['client_id'] = $request->client_id;
            $attributes['client_secret'] = $request->client_secret;
            $attributes['expires_in'] = $request->expires_in;
            $attributes['platform_id'] = $request->platform_id;
            $applicationModel->update($attributes);
            return response()->json(['result' => true, 'application' => $applicationModel], Response::HTTP_CREATED);
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function destroy($ids)
    {

        try {
            //code...
            DB::beginTransaction();
            $applications = Application::withTrashed()->whereIn('id', $ids)->get();
            // ADD REASON
            foreach ($applications as $application) {
                $application->trashed() ? $application->forceDelete() : $application->delete();
            }
            DB::commit();
            return response()->json(['result' => true], Response::HTTP_OK);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json($th->getMessage(), 404);
        }
    }


    public function storeRules(): array
    {
        return [
            'name'            => ['required', 'string', 'min:2', 'max:255'],
            'platform_id' => ['required', 'uuid', 'exists:platforms,id'],
            'client_id' => ['required', 'min:3', 'max:255'],
            'client_secret' => ['required', 'min:3', 'max:255'],
            'users' => ['required', 'uuid']
        ];
    }

    public function updateRules(): array
    {
        return [
            'name'            => ['required', 'string', 'min:2', 'max:255'],
            'platform_id' => ['required', 'uuid', 'exists:platforms,id'],
            'client_id' => ['required', 'min:3', 'max:255'],
            'client_secret' => ['required', 'min:3', 'max:255'],
        ];
    }


    private function getRelations(): array
    {
        return [
            'platform:id,platform_name',
            'users:id,code,firstname,lastname,image'
        ];
    }

    public function search(Request $request)
    {
        $queryBuilder = new UriQueryBuilder(new Application(), $request);
        $data = $this->searchCode($queryBuilder->query, $request, [], false);
        return $data ? response()->json($data, 200) : response()->json(['result' => false], Response::HTTP_NO_CONTENT);
    }

    /**
     * Return application redirect link
     * */
    public function redirectApplication(string $application_id)
    {
        $application = Application::where("system_status", "TOKEN_EXPIRED")->where("id", $application_id)->first();
        if ($application) {
            return response()->json(["result" => true, "redirect" => $application->redirect_url]);
        }
        return response()->json(["message" => "Application not found!"], 404);
    }

    /**
     * Reauthenticate the prev applications
     *
     */
    public function reAuthenticateApplication(string $application_id, string $code)
    {
        $application = Application::with("platform")->where("system_status", "TOKEN_EXPIRED")->where("id", $application_id)->first();
        if ($application) {
            $application_data = [
                "client_id" => $application->client_id,
                "client_secret" => $application->client_secret,
                "code" => $code,
            ];
            $platform = $application->platform;
            $result = false;
            switch ($platform->platform_name) {
                case 'google ads':
                    $result = GoogleAd::getTokenAndStore($application_data, $application_id);
                    break;
                case 'google analytics':
                    $result =  GoogleAnalytics::getTokenAndStore($application_data, $application_id);
                    break;
                case 'tiktok':
                    $result = TikTok::getTokenAndStore($application_data, $application_id);
                    break;
                case 'snapchat':
                    $result = Snapchat::getTokenAndStore($application_data, $application_id);
                    break;
                case 'facebook':
                    $result =  Facebook::getTokenAndStore($application_data, $application_id);
                    break;
                default:
                    break;
            }
            return response()->json(["result" => $result]);
        }
        return response()->json(["message" => "Application not found!"], 404);
    }


    /**
     * Delete Applications
     */
    public function deleteApplications(array $application_ids)
    {
        try {
            DB::beginTransaction();
            $applications = Application::withTrashed()->whereIn("id", $application_ids)->get();
            foreach ($applications as $application) {
                if ($application->trashed()) {
                    return response()->json(["message" => "cant_delete_application_for_now"]);
                } else {
                    $application->deleted_at = Carbon::now();
                    $application->system_status = "REMOVED";
                    $application->save();
                }
            }
            DB::commit();
            return response()->json(["result" => true, "message" => "applications_deleted"]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message" => $th->getMessage()], 403);
        }
    }

    public function restoreApplications($ids)
    {
        try {
            Application::onlyTrashed()->whereIn('id', $ids)
                ->update(["deleted_at" => null, "system_status" => "ACTIVE"]);
            return response()->json(['result' => true], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 404);
        }
    }

    public function getUsers($request)
    {
        try {
            $subSystem = SubSystem::where('scope', 'advapps')
                ->select('id', 'name')->first();
            $subAction = SubAction::where('name', 'c')
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
            return response()->json([
                'users' => $this->mergeUsers($subAction),
            ]);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 500);
        }
    }


    public function assignUsers($request)
    {
        try {
            $application = Application::where('id', $request->application)
                ->withTrashed()
                ->first();
            $application->users()->sync($request->users);
            return response()->json(
                [
                    'result' => true,
                    'application' => $application->load($this->getRelations())
                ]
            );
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 500);
        }
    }

    private function mergeUsers($subAction)
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
        return $this->removeDuplicates($users);
    }

    private function removeDuplicates($arr)
    {
        $arr2 = [];
        foreach ($arr as $user) {
            $arr2[$user['id']] = $user;
        }
        return array_values($arr2);
    }
}
