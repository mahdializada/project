<?php

namespace App\Http\Controllers\Advertisement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Advertisement\AdAccount;
use App\Models\Advertisement\AdAccountSubscribtion;
use App\Models\Advertisement\Application;
use App\Repositories\Advertisement\ApplicationRepository;
use Illuminate\Support\Facades\Http;


class ApplicationController extends Controller
{
    public function __construct()
    {
        $this->middleware('scope:advappsv')->only(['index', 'show']);
        $this->middleware('scope:advappsc')->only(['store', 'reAuthentication']);
        $this->middleware('scope:advappsu')->only(['store', 'update']);
        $this->middleware('scope:advappsd')->only(['store', 'destroy']);
    }

    public function index(Request $request)
    {
        $repository = new ApplicationRepository();
        return  $repository->paginate($request);
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => ['required', 'min:10'],
            "application_data.platform" => ["required", "uuid", "exists:platforms,id"],
            "application_data.platform_name" => ["required", "string", "min:2", "exists:platforms,platform_name"],
            "application_data.name" => ["required", "min:2", "max:150"],
            "application_data.client_id" => ["required", "min:10"],
            "application_data.client_secret" => ["required", "min:10"],
        ]);
        $repository = new ApplicationRepository();
        $result = $repository->getApplicationToken($request);
        return response()->json(["result" => $result]);
    }

    public function show(Request $request)
    {
        $repository = new ApplicationRepository();
        return $repository->search($request);
    }

    public function reAuthentication(string $application_id, string $type, Request $request)
    {
        $repository = new ApplicationRepository();
        if ($type == "redirect") {
            return $repository->redirectApplication($application_id);
        } else if ($type == "authenticate") {
            $request->validate(["code" => "required|min:5"]);
            return $repository->reAuthenticateApplication($application_id, $request->code);
        }
        return response()->json(["message" => "Invalid params"], 404);
    }

    public function update(Request $request)
    {
        $repository = new ApplicationRepository();
        if ($request->restore) {
            $request->validate([
                "ids" => ["required", "array",],
                "ids.*" => ["uuid", "exists:applications,id"],
            ]);
            return $repository->restoreApplications($request->ids);
        }
        // $request->validate($repository->updateRules());
        // return $repository->update($request);
    }

    public function destroy($application_ids)
    {
        $application_ids = explode(",", $application_ids);
        if ($application_ids) {
            $repository = new ApplicationRepository();
            return $repository->deleteApplications($application_ids);
        }
        return response()->json(["message" => "application_id_is_not_provided"]);
    }

    public function getUsers(Request $request)
    {
        $repository = new ApplicationRepository();
        return  $repository->getUsers($request);
    }

    public function assignUsers(Request $request)
    {
        $request->validate([
            'users' => ['required', 'array'],
            'users.*' => ['uuid', 'exists:users,id'],
            'application' => ['required', 'uuid', 'exists:applications,id'],
        ]);
        $repository = new ApplicationRepository();
        return  $repository->assignUsers($request);
    }



    public function getRelatedAdAccounts($id)
    {
        # code...
        try {
            $ad_accounts = AdAccount::with('subcribtion')->whereApplicationId($id)->get();
            return response()->json($ad_accounts, 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json($th->getMessage(), 400);
        }
    }
    public  function toggleSubcribtion(Request $request, $id)
    {
        try {
            $subscription =  AdAccountSubscribtion::where('ad_account_id', $id)->first();
            $ad_account = AdAccount::find($id);
            $ad_account = $ad_account->makeVisible(['account_id']);
            $application =   Application::find($ad_account->application_id);
            $application = $application->makeVisible(['access_token', 'client_id', 'client_secret']);

            if (!$subscription) {

                $body = [
                    "app_id" => $application->client_id,
                    'secret' => $application->client_secret,
                    "subscribe_entity" => "LEAD",
                    "callback_url" => "https://clientbackend.oredoh.org/api/instant-order/subscribe",
                    "subscription_detail" => ["advertiser_id" => $ad_account->account_id, "access_token" => $application->access_token]
                ];
                $url = "https://business-api.tiktok.com/open_api/v1.3/subscription/subscribe/";
                // return $body;
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                ])->post($url, $body);
                if ($response['message'] == "OK") {
                    $result =  AdAccountSubscribtion::create(["ad_account_id" => $id, "subscribtion_id" => $response['data']['subscription_id']]);
                    return response()->json(["action" => "subscribed", "data" => $result]);
                } else {
                    return response()->json(['error', $response], 500);
                }
            } else {
                $url = "https://business-api.tiktok.com/open_api/v1.3/subscription/unsubscribe/";
                $body = [
                    "app_id" => $application->client_id,
                    "subscription_id" => $subscription["subscribtion_id"],
                    'secret' => $application->client_secret
                ];

                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                ])->post($url, $body);
                if ($response['message'] == "OK") {
                    AdAccountSubscribtion::where('ad_account_id', $id)->delete();
                } else {
                    return response()->json(['error', $response], 500);
                }
            }
            return response()->json(["action" => "unsubscribe"]);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 500);
        }
    }
}
