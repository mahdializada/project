<?php

namespace App\Http\Controllers\Advertisement;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\DuplicateInActiveAds;
use App\Jobs\GenerateLink;
use App\Jobs\RefreshCrmJob;
use App\Jobs\RefreshInactiveAds;
use App\Jobs\SwitchByPlatformName;
use App\Models\Advertisement\AdAccount;
use App\Models\Advertisement\Connection;
use App\Models\Advertisement\HistoryAd;
use App\Models\Advertisement\HistoryAdset;
use App\Models\Advertisement\HistoryCampaign;
use App\Models\Country;
use App\Repositories\Advertisement\AdvertisementTabs\Campaign;
use App\Repositories\Advertisement\AdvertisementUtil;
use App\Repositories\Advertisement\ConnectionRepository;
use App\Repositories\Files\CloudinaryFileUtils;
use Illuminate\Support\Facades\DB;

class ConnectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('scope:advv')->only(['refreshAds']);
        // $this->middleware('scope:advc')->only(['store', 'update']);
    }

    public function fetchItems($type, $id)
    {
        $repository = new ConnectionRepository();
        $result = ["type" => $type];
        switch ($type) {
            case "country":
                $result["items"] = $repository->countries();
                return  response()->json($result);
            case "companies":
                $result["items"] = $repository->countryCompany($id);
                return  response()->json($result);
            case "products":
                $result["items"] = $repository->teebalhoorProducts($id);
                return  response()->json($result);
            case "ispps":
                $result["items"] = $repository->companyIspp($id);
                return  response()->json($result);
            case "projects":
                $result["items"] = $repository->companyProject($id);
                return  response()->json($result);
            case "platforms":
                $result["items"] = $repository->companyPlatform($id);
                return  response()->json($result);
            case "applications":
                $result["items"] = $repository->platformApplication($id);
                return  response()->json($result);
            case "adAccounts":
                $result["items"] = $repository->fetchAdAccounts($id);
                return  response()->json($result);
            case "allAdAccounts":
                $result["items"] = $repository->fetchAllAdAccounts();
                return  response()->json($result);
            default:
                return response()->json([], 403);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $repository = new ConnectionRepository();
        $request->validate($repository->validationRules());
        return $repository->store($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Advertisement\Connection  $connection
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $data = Connection::find($id);
        return $data;
    }

    public function item_code()
    {
        $item_code = Connection::get(['pcode', 'code']);
        return response()->json(["data" => $item_code]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Advertisement\Connection  $connection
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request) //create
    {
        $request->validate([
            // "connection_id" => ["required", "uuid", "exists:connections,id"],
            "ads" => ["required"],
        ]);
        // GenerateLink::dispatch('', $request);
        $repository = new ConnectionRepository();
        // return ['result' => true, "haji" => 'hji'];
        return $repository->update($request);
    }

    public function refreshAds() //view
    {
        return AdvertisementUtil::addPlatformRequestsIntoQueue();

        //  response()->json(["message" => "Refreshed"]);
    }
    public function refreshSpecificPlatformAds(Request $request)
    {

        return AdvertisementUtil::addPlatformIntoQueue($request);
    }
    public function refreshCrm(Request $request)
    {

        $countries =  Country::whereHas('connections')->with('connections', function ($query) {
            $query->where("history_ad_id", "!=", null)->groupBy(["server_account_id", "history_ad_id"]);
        })->get();


        foreach ($countries as $country) {
            # code...
            RefreshCrmJob::dispatch($country, $request);
        }
        return response()->json(['result' => true, "total_countries" => count($countries)]);
    }

    public  static function insertInactiveAds(Request $request = null)
    {
        try {
            $yesterday = Carbon::yesterday();
            $yesterday = $yesterday->toDateString();
            $relation = ["application", "platform", "adAccount", "historyAd"];
            $connections = Connection::with($relation)
                ->whereHas("application", function ($query) {
                    return $query->where("system_status", "ACTIVE")
                        ->whereNotNull("access_token")->whereNotNull("refresh_token");
                })->whereHas("platform", function ($query) use ($request) {
                    return $query->where("system_status", "ACTIVE");
                })
                ->whereHas("historyAdds", function ($query)  use ($yesterday) {
                    return $query->where("status", "!=", "ACTIVE")->where('data_date', $yesterday);
                });

            $connections = $connections->groupBy(["server_account_id", "history_ad_id"])->get();

            // return $connections;
            $data = [];
            foreach ($connections as  $connection) {
                DuplicateInActiveAds::dispatch($connection);
            }
            return response()->json(['result' => true, 'data' => count($data)]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json($th->getMessage(), 500);
        }
    }

    public static function updateInActiveCampaign($connection = null)
    {

        try {
            $today_timestamp = Carbon::now();
            $today_date = $today_timestamp->toDateString();
            $ad_account = $connection->adAccount;
            $condition = [
                ["server_account_id", "=", $ad_account->account_id],
                ["ad_account_id", "=", $ad_account->id],
                ["campaign_id", "=", $connection->server_campaign_id],
                ["data_date", "=", $today_date],
            ];
            $campaign = HistoryCampaign::where($condition)->first();
            if (!$campaign) {
                $condition = [
                    ["server_account_id", "=", $ad_account->account_id],
                    ["ad_account_id", "=", $ad_account->id],
                    ["campaign_id", "=", $connection->server_campaign_id],
                ];
                $campaign = HistoryCampaign::where($condition)->latest()->first();
                $campaign_attributes["server_account_id"] = $ad_account->account_id;
                $campaign_attributes["ad_account_id"]     = $ad_account->id;
                $campaign_attributes["data_date"]         = $today_date;
                $campaign_attributes["data_timestamp"]    =  $today_timestamp;
                $campaign_attributes["code"]              = uniqueCode(HistoryCampaign::class, "CMP");
                $campaign_attributes["campaign_id"]       = $campaign->campaign_id;
                $campaign_attributes["name"]              = $campaign->name;
                $campaign_attributes["status"]            = $campaign->getRawOriginal('status');
                $campaign_attributes["objective_type"]    = $campaign->getRawOriginal('objective_type');
                $campaign_attributes["budget_mode"]       = $campaign->getRawOriginal('budget_mode');
                $campaign_attributes["budget"]            = $campaign->getRawOriginal('budget');
                $campaign_attributes["campaign_type"]     = $campaign->getRawOriginal('campaign_type');
                $campaign_attributes["delivery_status"]   = $campaign->getRawOriginal('delivery_status');
                $campaign_attributes["objective"]         = $campaign->getRawOriginal('objective');
                $campaign_attributes["system_status"]     = $campaign->getRawOriginal('system_status');
                $campaign_attributes["start_time"]        = $campaign->getRawOriginal('start_time');
                $campaign_attributes["end_time"]          = $campaign->getRawOriginal('end_time');
                $campaign_attributes["server_created_at"] = $campaign->getRawOriginal('server_created_at');
                $campaign_attributes["server_updated_at"] = $campaign->getRawOriginal('server_updated_at');
                $campaign_attributes["ad_account_id"]     = $campaign->getRawOriginal('ad_account_id');
                $campaign =    HistoryCampaign::create($campaign_attributes);
            }
            return   self::updateInActiveAdset($campaign, $connection);
        } catch (\Throwable $th) {
            //throw $th;
            return $th->getMessage();
        }
    }


    public static function updateInActiveAdset($campaign, $connection)
    {
        try {
            $today_timestamp = Carbon::now();
            $today_date = $today_timestamp->toDateString();
            $condition = [
                ["adset_id", "=", $connection->server_ad_adset_id],
                ["server_campaign_id", "=", $campaign->campaign_id],
                ["data_date", "=", $today_date],
            ];
            // ["campaign_id", "=", $campaign->id],

            $adset = HistoryAdset::where($condition)->first();
            if (!$adset) {
                $condition = [
                    ["adset_id", "=", $connection->server_ad_adset_id],
                    ["server_campaign_id", "=", $campaign->campaign_id],
                ];
                $adset = HistoryAdset::where($condition)->latest()->first();
                $adset_attributes["campaign_id"]           = $campaign->id;
                $adset_attributes["server_campaign_id"]    = $campaign->campaign_id;
                $adset_attributes["data_date"]             = $today_date;
                $adset_attributes["data_timestamp"]        = $today_timestamp;
                $adset_attributes["code"]                   = uniqueCode(HistoryAdset::class, "ADS");
                $adset_attributes["adset_id"]                = $adset->adset_id;
                $adset_attributes["name"]                = $adset->name;
                $adset_attributes["status"]              = $adset->getRawOriginal("status");
                $adset_attributes["bid"]                 = $adset->getRawOriginal("bid");
                $adset_attributes["bid_strategy"]        = $adset->getRawOriginal("bid_strategy");
                $adset_attributes["optimization_goal"]    = $adset->getRawOriginal('optimization_goal');
                $adset_attributes["pixel_id"]             = $adset->getRawOriginal("pixel_id");
                $adset_attributes["end_time"]             = $adset->getRawOriginal("end_time");
                $adset_attributes["server_created_at"]    = $adset->getRawOriginal("server_created_at");
                $adset_attributes["server_updated_at"]    = $adset->getRawOriginal("server_created_at");
                $adset_attributes["start_time"]           = $adset->getRawOriginal("start_time");
                $adset_attributes["system_status"]      = $adset->getRawOriginal("system_status");
                $adset_attributes["currency"]           = $adset->getRawOriginal("currency");
                $adset_attributes["daily_budget"]       = $adset->getRawOriginal("daily_budget");
                $adset_attributes["delivery_status"]    = $adset->getRawOriginal("delivery_status");
                $adset_attributes["age_groups"]    = $adset->getRawOriginal("age_groups");
                $adset_attributes["gender"]    = $adset->getRawOriginal("gender");
                $adset_attributes["locations"]    = $adset->getRawOriginal("locations");
                $adset_attributes["operating_systems"]    = $adset->getRawOriginal("operating_systems");
                $adset_attributes["placements"]    = $adset->getRawOriginal("placements");
                $adset_attributes["languages"]    = $adset->getRawOriginal("languages");
                $adset_attributes["network_types"]    = $adset->getRawOriginal("network_types");
                $adset_attributes["device_models"]    = $adset->getRawOriginal("device_models");
                $adset_attributes["placement_type"]    = $adset->getRawOriginal("placement_type");
                $adset_attributes["promotion_type"]    = $adset->getRawOriginal("promotion_type");
                $adset_attributes["smart_targeting"]    = $adset->getRawOriginal("smart_targeting");

                $adset                                  = HistoryAdset::create($adset_attributes);
            }
            return  self::updateInActiveAds($adset, $connection);
        } catch (\Throwable $th) {
            //throw $th;
            dd($th->getMessage());
            return $th->getMessage();
        }
    }

    public  static function updateInActiveAds($adset, $connection)
    {
        try {
            $today_timestamp = Carbon::now();
            // ["adset_id", "=", $adset->id],
            $condition = [
                ["ad_id", "=", $connection->server_ad_id],
                ["server_adset_id", "=", $adset->adset_id],
                ["data_date", "=", $today_timestamp->toDateString()],
            ];
            $ad = HistoryAd::where($condition)->first();

            if (!$ad) {
                $condition = [
                    ["ad_id", "=", $connection->server_ad_id],
                    ["server_adset_id", "=", $adset->adset_id],
                ];

                $ad = HistoryAd::where($condition)->latest()->first();
                $ad_attributes["adset_id"] = $adset->id;
                $ad_attributes["ad_id"] = $connection->server_ad_id;
                $ad_attributes["server_adset_id"] = $adset->adset_id;
                $ad_attributes["server_account_id"] = $ad->server_account_id;
                $ad_attributes["data_date"] = $today_timestamp->toDateString();
                $ad_attributes["data_timestamp"] = $today_timestamp;
                $ad_attributes["ad_pcode"] = $ad->ad_pcode;
                $ad_attributes["ad_name"] = $ad->ad_name;
                $ad_attributes["code"] = uniqueCode(HistoryAd::class, "AD");
                $ad_attributes["sales_type"] = $ad->sales_type;
                $ad_attributes["status"] = $ad->status;
                return  HistoryAd::create($ad_attributes);
            }
            return 'found';
        } catch (\Throwable $th) {
            //throw $th;
            dd($th->getMessage());
            return $th->getMessage();
        }
    }




    public function checkForConnectionCode(string $connection_code)
    {
        $repository = new ConnectionRepository();
        return $repository->checkForConnectionCode($connection_code);
    }

    public function checkAccountPo(string $serverAccountId)
    {
        $isExists = AdAccount::where("account_id", $serverAccountId)->whereNotNull("account_po")->exists();
        if ($isExists) {
            return response()->json(["result" => true]);
        }
        return response()->json(["result" => false]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Advertisement\Connection  $connection
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        return Connection::whereIn('id', $request->ids)->delete();
    }



    public function productHistory(Request $request)
    {
        return ConnectionRepository::productStatusHistory($request);
    }
    public function getFirstDate()
    {
        $DATE = Connection::orderBy('created_at', 'asc')->select(DB::raw('DATE(created_at) as date'))->first();
        return $DATE ? $DATE : Carbon::now()->toDateString();
    }

    public static function getMissedAdsByDate(Request $request)
    {


        $relation = ["application", "platform", "adAccount", "historyAd"];
        $connections = Connection::with($relation)
            ->whereHas("application", function ($query) {
                return $query->where("system_status", "ACTIVE")
                    ->whereNotNull("access_token")->whereNotNull("refresh_token");
            })
            ->whereHas("platform", function ($query) use ($request) {

                return $query->where("system_status", "ACTIVE");
            })->where("history_ad_id", "!=", null);
        $connections = $connections->whereDate(DB::raw('DATE(created_at)'), "<=", $request->dates)->groupBy(["server_account_id", "history_ad_id"]);
        $missedConnections = clone  $connections;
        $connections = $connections->whereHas("historyAdds", function ($query)  use ($request) {
            return $query->where('data_date', $request->dates);
        });
        $connections = $connections->pluck('id');
        $missedConnections = $missedConnections->whereNotIn("id", $connections)->get();



        $extra_data  = [
            "total" => $missedConnections->count(),

        ];

        if ($request->dates)
            $extra_data['dates'] = $request->dates;

        foreach ($missedConnections as $index => $connection) {
            $extra_data["item_index"] = $index;
            SwitchByPlatformName::dispatch($connection, $extra_data);
        }
        return $missedConnections;
    }

    public function testUpload()
    {
        # code...
        // return  CloudinaryFileUtils::uploadFile();
    }
    public function deleteFile()
    {
        # code...
        // return  CloudinaryFileUtils::deleteFile();
    }
}
