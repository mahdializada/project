<?php

namespace App\Repositories\Advertisement\Platforms;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use App\Models\Advertisement\AdAccount;
use App\Models\Advertisement\HistoryAd;
use App\Models\Advertisement\Connection;
use App\Models\Advertisement\Application;
use App\Models\Advertisement\HistoryAdset;
use App\Models\Advertisement\AdAccountPixel;
use App\Models\Advertisement\AdsetTargets;
use App\Models\Advertisement\HistoryCampaign;
use App\Repositories\Advertisement\PlatformUrl;
use App\Repositories\Advertisement\AdvertisementUtil;
use App\Repositories\Advertisement\HistoryAdRepository;
use App\Repositories\Advertisement\Platforms\Teebalhoor;
use App\Repositories\Advertisement\Queries\SnapchatQuery;
use App\Repositories\Advertisement\HistoryAdsetRepository;
use App\Repositories\Advertisement\HistoryCampaignRepository;
use App\Repositories\Advertisement\Platforms\GoogleAnalytics;
use Illuminate\Support\Facades\Auth;

class Snapchat
{

    private static string $SCOPE = "snapchat-marketing-api";
    private static string $start_date;
    private  static string $end_date;

    /**
     * Authenticate Snapchat and fetch records
     */
    public static function authenticateAndFetch(string $url, Application $application, $break = false)
    {

        $token = $application->access_token;

        $response = Http::withToken($token)->get($url);
        if ($break && $response->failed()) {
            if ($response->status() === Response::HTTP_UNAUTHORIZED) {
                AdvertisementUtil::expireApplication($application);
                throw new Exception("snapchat_token_expired", 401);
            }
            throw new Exception("snapchat_fetching_error", $response->status());
        }
        if ($response->status() == Response::HTTP_UNAUTHORIZED) {
            $is_logged_in =  self::refreshToken($application);
            if ($is_logged_in) return self::authenticateAndFetch($url, $application, true);
            return [];
        }
        if ($response->successful()) {
            return $response->json();
        }
        $response->throw();
    }

    /**
     * Authenticate Snapchat and fetch records
     */
    public static function authenticateAndUpdate(string $url, Application $application, $body, $break = false)
    {
        $token = $application->access_token;
        $response = Http::withToken($token)->put($url, $body);
        if ($break && $response->failed()) {
            if ($response->status() === Response::HTTP_UNAUTHORIZED) {
                AdvertisementUtil::expireApplication($application);
                throw new Exception("snapchat_token_expired", 401);
            }
            throw new Exception("snapchat_fetching_error", $response->status());
        }
        if ($response->status() == Response::HTTP_UNAUTHORIZED) {
            $is_logged_in =  self::refreshToken($application);
            if ($is_logged_in) return self::authenticateAndFetch($url, $application, true);
            return [];
        }
        if ($response->successful()) {
            return $response->json();
        }
        $response->throw();
    }

    /**
     * ACCOUNT SECTION
     */

    public static function findAndstoreAccount(Application $application, string $account_id)
    {
        $url = PlatformUrl::$SNAPCHAT_AD_BASE_URL . "/adaccounts/$account_id";
        $response = self::authenticateAndFetch($url, $application);
        if ($response && array_key_exists("adaccounts", $response)) {
            $is_done =  self::fetchPixelsAndSave($application, $account_id);

            if ($is_done == 'not found') return null;
            $collection = collect($response)->get("adaccounts");

            $collection = collect($collection)->first();
            $account = collect($collection)->get("adaccount");
            $account = collect($account);
            $account_attributes = self::filterAccountColumns($account, $application->id);
            return AdAccount::create($account_attributes);
        }
        return null;
    }

    public static function fetchPixelsAndSave(Application $application, string $account_id)
    {
        $url = PlatformUrl::$SNAPCHAT_AD_BASE_URL . "/adaccounts/$account_id/pixels";
        $response = self::authenticateAndFetch($url, $application);

        if ($response && array_key_exists("pixels", $response)) {
            $pixels = collect($response)->get("pixels");
            $inserted_pixels = [];
            foreach ($pixels as $pixel) {
                $pixel = $pixel["pixel"];
                $attributes = collect($pixel)->only(["organization_id",  "status"]);
                // $attributes = collect($pixel)->only(["organization_id", "ad_account_id", "status"]);
                $attributes["pixel_id"] = $pixel["id"];
                $attributes["ad_account_id"] = $account_id;
                $attributes["pixel_name"] = $pixel["name"];
                $attributes["pixel_script"] = $pixel["pixel_javascript"];
                $is_exist = AdAccountPixel::where("pixel_id", $pixel["id"])
                    ->where("ad_account_id", $attributes["ad_account_id"])->exists();

                if (!$is_exist) {
                    $item = AdAccountPixel::create($attributes->toArray());
                    $inserted_pixels[] = $item;
                }
            }
            return $inserted_pixels;
        }
        return 'not fount';
    }


    public static function filterAccountColumns(Collection $account, string $application_id)
    {
        return
            [
                "code" => uniqueCode(AdAccount::class, "ACC"),
                "name" => $account->get("name"),
                "account_id" => $account->get("id"),
                "status" => AdvertisementUtil::getStatus($account->get("status")),
                "type" => $account->get("type"),
                "organization_id" => $account->get("organization_id"),
                "billing_center_id" => $account->get("billing_center_id"),
                "currency" => $account->get("currency"),
                "timezone_name" => $account->get("timezone"),
                "server_created_at" => $account->get("created_at"),
                "server_updated_at" => $account->get("updated_at"),
                "application_id" => $application_id,
            ];
    }


    /**
     * ACCOUNT SECTION END
     */


    /**
     * Fetch Snapchat Ads Accounts
     */

    public static function fetchAdAccount(Application $application)
    {
        $org_id = $application->organization_id;
        $url = PlatformUrl::$SNAPCHAT_AD_BASE_URL . "/organizations/$org_id/adaccounts";
        $response = self::authenticateAndFetch($url, $application);
        if ($response && array_key_exists("adaccounts", $response)) {
            $ad_accounts =  $response["adaccounts"];
            return $ad_accounts;
        }
        return [];
    }

    /**
     * return the fields columns from Ad Accounts
     */
    public static function fetchAdAccountsField($application_id, $fields = [])
    {
        $application = Application::withTrashed()->find($application_id);
        if ($application) {
            $ad_accounts = self::fetchAdAccount($application);
            $result_accounts = [];
            foreach ($ad_accounts as $ad_account) {
                $item = $ad_account["adaccount"];
                $temp_item = [];
                $temp_item['application_id'] = $application->id;
                $temp_item['platform_id'] = $application->platform_id;
                foreach ($fields as $field)
                    $temp_item[$field] = $item[$field];
                $result_accounts[] = $temp_item;
            }
        }
        return $result_accounts;
    }


    /**
     * CAMPAIGN SECTION START
     */

    public static function updateCampaignStatus(Connection $connection)
    {
        try {
            $application = $connection->application;
            $account_id = $connection->server_account_id;
            $campaign_id = $connection->server_campaign_id;
            $url = PlatformUrl::$SNAPCHAT_AD_BASE_URL . "/campaigns/$campaign_id";
            $response = self::authenticateAndFetch($url, $application);
            $campaigns = collect($response)->get("campaigns");
            $campaign = collect($campaigns)->first();
            $campaign = collect($campaign)->get("campaign");
            $current_status = collect($campaign)->get("status");
            $new_status = $current_status == 'ACTIVE' ? "PAUSED" : "ACTIVE";
            $campaign["status"] = $new_status;
            $update_url = PlatformUrl::$SNAPCHAT_AD_BASE_URL . "/adaccounts/$account_id/campaigns";
            $body = ["campaigns" => [$campaign]];
            $update_response = self::authenticateAndUpdate($update_url, $application, $body);
            if ($update_response["request_status"] == "SUCCESS") {
                $today = Carbon::today()->toDateString();
                $history_campaign = HistoryCampaign::where("campaign_id",  $campaign_id)
                    ->where("data_date", $today)->first();
                $history_campaign->update([
                    "status" => $new_status  == "ACTIVE" ? "ACTIVE" : "INACTIVE",
                ]);
                return "success";
            }
            return "failed";
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    /**
     * CAMPAIGN SECTION END
     */


    /**
     * Refresh and Authenticate snapchat API Manager Account
     */
    public static function refreshToken(Application $application)
    {
        $auth_url = PlatformUrl::$SNAPCHAT_AUTH_URL;
        $params = [
            "client_id" => $application->client_id,
            "client_secret" => $application->client_secret,
            "refresh_token" => $application->refresh_token,
            "grant_type" => "refresh_token",
        ];
        $url = $auth_url . "/access_token";
        $response = Http::withOptions(["query" => $params])->post($url);
        if ($response->successful()) {
            $data =  $response->json();
            $application->access_token = $data["access_token"];
            $application->refresh_token = $data["refresh_token"];
            $application->expires_in = $data["expires_in"];
            $application->save();
            return true;
        }
        AdvertisementUtil::expireApplication($application);
        $response->throw();
        return false;
    }


    /**
     * ADSET SECTION START
     */
    public static function updateAdsetStatus(Connection $connection)
    {
        try {
            $application = $connection->application;
            $campaign_id = $connection->server_campaign_id;
            $adset_id = $connection->server_ad_adset_id;
            $url = PlatformUrl::$SNAPCHAT_AD_BASE_URL . "/adsquads/$adset_id";
            $response = self::authenticateAndFetch($url, $application);
            $adsquads = collect($response)->get("adsquads");
            $adsquad = collect($adsquads)->first();
            $adsquad = collect($adsquad)->get("adsquad");
            $current_status = collect($adsquad)->get("status");
            $new_status = $current_status == 'ACTIVE' ? "PAUSED" : "ACTIVE";
            $adsquad["status"] = $new_status;
            $update_url = PlatformUrl::$SNAPCHAT_AD_BASE_URL . "/campaigns/$campaign_id/adsquads";
            $body = ["adsquads" => [$adsquad]];
            $update_response = self::authenticateAndUpdate($update_url, $application, $body);
            if ($update_response["request_status"] == "SUCCESS") {
                $today = Carbon::today()->toDateString();
                $history_adset = HistoryAdset::where("adset_id",  $adset_id)
                    ->where("data_date", $today)->first();
                $history_adset->update([
                    "status" => $new_status  == "ACTIVE" ? "ACTIVE" : "INACTIVE",
                ]);
                return "success";
            }
            return "failed";
        } catch (\Throwable $th) {
            return "failed";
        }
    }

    public static function fetchAdsets(Application $application, string $currency, string $adset_id)
    {
        try {
            //code...

            $url = PlatformUrl::$SNAPCHAT_AD_BASE_URL . "/adsquads/$adset_id?return_placement_v2=true";
            $response = self::authenticateAndFetch($url, $application);
            $collection = collect($response)->get("adsquads");
            $collection = collect($collection)->first();
            $collection = collect($collection)->get("adsquad");
            $adset_item = collect($collection);
            $targeting = collect($adset_item->get("targeting"));
            $delivery_status = $adset_item->get("delivery_status");
            $result_item =  [
                "adset_id" => $adset_item->get("id"),
                "name" => $adset_item->get("name"),
                "status" => AdvertisementUtil::getStatus($adset_item->get("status")),
                "daily_budget" => AdvertisementUtil::microDollarToDollar($adset_item->get("daily_budget_micro")),
                "bid" => AdvertisementUtil::microDollarToDollar($adset_item->get("bid_micro")),
                "bid_strategy" => $adset_item->get("bid_strategy"),
                "pixel_id" => $adset_item->get("pixel_id"),
                "delivery_status" => is_array($delivery_status) ? implode(",", $delivery_status) : $delivery_status,
                "optimization_goal" => $adset_item->get("optimization_goal"),
                "server_campaign_id" => $adset_item->get("campaign_id"),
                "currency" => $currency,
                "start_time" => $adset_item->get("start_time"),
                "server_created_at" => $adset_item->get("created_at"),
                "server_updated_at" => $adset_item->get("updated_at"),
                "placement_type" =>  collect($adset_item->get("placement_v2"))->get('config'),
                "placements" => json_encode(collect($adset_item->get("placement_v2"))->get('snapchat_positions')),
                "locations" => json_encode(collect($targeting->get("geos"))->pluck('country_code')),
                "gender" => @collect($targeting->get("demographics"))->toArray()[0]['gender'],
                "age_groups" => json_encode(collect($targeting->get("demographics"))),

                //     "age_groups" => json_encode($adset_item->get("age_groups")),
                // "gender" => $adset_item->get("gender"),
                // "locations" =>  json_encode($adset_item->get("location_ids")),
                // "operating_systems" =>  json_encode($adset_item->get("operating_systems")),
                // "placements" => json_encode($adset_item->get("placements")),
                // "languages" =>  json_encode($adset_item->get("languages")),
                // "network_types" =>  json_encode($adset_item->get("network_types")),
                // "device_models" =>  json_encode($adset_item->get("device_model_ids")),
                // "placement_type" =>  $adset_item->get("placement_type"),
                // "promotion_type" => $adset_item->get("promotion_type"),
                // "smart_targeting" => $adset_item->get("auto_targeting_enabled"),
            ];
            // $targetCollection = $adset_item->get("targeting");

            return collect($result_item);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
    public static function createOrUpdateAdsetTaget($data, $adset_id)
    {
        // return [];
        try {
            //code...

            $targets = [];
            $data = collect($data);
            // if ($data)
            $demographics = collect($data->get("demographics"))->first();
            $demographics = collect($demographics);
            $location = collect($data->get('geo') ? $data->get('geo')->first() : []);
            $auto_expansion_options = collect($data->get('auto_expansion_options'));
            $test = [['id' => 1, 'name' => "hakima"], ['id' => 2], ['id' => 3], ['id' => 4]];
            $location = collect($test[0]);


            $targets =  [
                "adset_id" => $adset_id,
                "age" => json_encode($demographics->get('age_groups') ?? [$demographics->get('min_age'), $demographics->get('max_age')]),
                "gender" => $demographics->get('gender'),
                'location' => json_encode($location->get('country_code')),
                'language' => json_encode($demographics->get('languages')),
                'connection_type' => json_encode($location->get('id')),
                // 'device_model' => json_encode(),
                // 'operation_system' => json_encode(),
                // 'operating_system_version' => json_encode(),
                // 'interest' => json_encode(),
                // 'targeting_expansion' => json_encode(),
                'auto_targeting' => json_encode($auto_expansion_options->get('interest_expansion_option')),

            ];

            $result = AdsetTargets::create($targets);



            // return $result;
        } catch (\Throwable $th) {
            file_put_contents('test.json', $th->getMessage());

            return $th->getMessage();
        }
    }


    /**
     * ADSET SECTION END
     */

    /**
     * CAMPAIGN SECTION START
     */
    public static function fetchSingleCampaign(Application $application, string $campaign_id)
    {
        $url = PlatformUrl::$SNAPCHAT_AD_BASE_URL . "/campaigns/$campaign_id";
        $response = self::authenticateAndFetch($url, $application);
        $collection = collect($response)->get("campaigns");
        $collection = collect($collection)->first();
        $collection = collect($collection)->get("campaign");
        $campaign_item = collect($collection);
        $delivery_status = $campaign_item->get("effective_status");
        $result_item =  [
            "campaign_id" => $campaign_item->get("id"),
            "name" => $campaign_item->get("name"),
            "status" => AdvertisementUtil::getStatus($campaign_item->get("status")),
            "delivery_status" =>  is_array($delivery_status) ? implode(",", $delivery_status) : $delivery_status,
            "objective" => $campaign_item->get("objective"),
            "server_account_id" => $campaign_item->get("ad_account_id"),
            "start_time" => $campaign_item->get("start_time"),
            "end_time" => $campaign_item->get("stop_time"),
            "server_created_at" => $campaign_item->get("created_time"),
            "server_updated_at" => $campaign_item->get("updated_time"),
        ];
        return collect($result_item);
    }

    /**
     * CAMPAIGN SECTION END
     */

    /**
     * AD SECTION START
     */

    public static function updateAdStatus(Connection $connection)
    {
        try {
            $application = $connection->application;
            $adset_id = $connection->server_ad_adset_id;
            $ad_id = $connection->server_ad_id;
            $url = PlatformUrl::$SNAPCHAT_AD_BASE_URL . "/ads/$ad_id";
            $response = self::authenticateAndFetch($url, $application);
            $ads = collect($response)->get("ads");
            $ad_item = collect($ads)->first();
            $ad_item = collect($ad_item)->get("ad");
            $current_status = collect($ad_item)->get("status");
            $new_status = $current_status == 'ACTIVE' ? "PAUSED" : "ACTIVE";
            $ad_item["status"] = $new_status;
            $update_url = PlatformUrl::$SNAPCHAT_AD_BASE_URL . "/adsquads/$adset_id/ads";
            $body = ["ads" => [$ad_item]];
            $update_response = self::authenticateAndUpdate($update_url, $application, $body);
            if ($update_response["request_status"] == "SUCCESS") {
                $today = Carbon::today()->toDateString();
                $history_ad_item = HistoryAd::where("ad_id",  $ad_id)
                    ->where("data_date", $today)->first();
                if ($history_ad_item) {
                    $history_ad_item->update([
                        "status" => $new_status  == "ACTIVE" ? "ACTIVE" : "INACTIVE",
                    ]);
                }
                return $new_status  == "ACTIVE" ? "ACTIVE" : "INACTIVE";
            }
            return $update_response;
        } catch (\Throwable $th) {
            return $th;
        }
    }
    public static function setDate($extra_data)
    {
        try {
            if ($extra_data != null) {
                if (array_key_exists('dates', $extra_data)) {
                    $today = Carbon::parse($extra_data['dates']);
                    self::$start_date = $today->toDateString();
                    self::$end_date = $today->addDay()->toDateString();

                    return;
                }
            }
            $today = Carbon::now();
            self::$start_date = $today->toDateString();
            self::$end_date = $today->addDay()->toDateString();
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }
    public static function createAdWithAdsetAndCampaign(Connection $connection, string $ad_id, $extra_data = null)
    {
        try {
            $application = $connection->application;
            $ad_account = $connection->adAccount;
            $timezone = $ad_account->timezone_name;
            self::setDate($extra_data);
            $extra_data['start_date'] = self::$start_date;
            $ad_item_raw_data = self::fetchSingleAdPerDay($application, $timezone, $ad_id);
            $adset_id = $ad_item_raw_data->get("ad_squad_id");

            $adset_attributes = self::fetchAdsets($application, $ad_account->currency, $adset_id);
            $campaign_id = $adset_attributes->get("server_campaign_id");
            $campaign_attributes = self::fetchSingleCampaign($application,  $campaign_id);
            $account_id = $campaign_attributes->get("server_account_id");
            if ($account_id != $ad_account->account_id) {
                throw new Exception("snapchat_invalid_id", 404);
            }
            $crm_item_raw_data = Teebalhoor::serverProducts($connection, $extra_data);
            // $ga_raw_data = GoogleAnalytics::fetchGoogleAnalytics("282328672", $connection->hashed_code, $extra_data);
            // $carbon_now = Carbon::now();
            $carbon_now = Carbon::parse(self::$start_date);
            $campaign_repo = new HistoryCampaignRepository();
            $campaign = $campaign_repo->createOrUpdateCampaign($campaign_attributes->toArray(), $ad_account, $carbon_now);
            $adset_repo = new HistoryAdsetRepository();
            $adset = $adset_repo->createOrUpdateAdset($adset_attributes->toArray(), $campaign->id, $carbon_now);
            $ad_parsed_data = self::parseAdRawData($ad_item_raw_data, $crm_item_raw_data);
            $ad_parsed_data["server_account_id"] = $account_id;
            $ad_parsed_data["currency"] = $ad_account->currency;
            $ad_parsed_data = $ad_parsed_data;
            // $ad_parsed_data = array_merge($ad_parsed_data, $ga_raw_data);
            $ad_repo = new HistoryAdRepository();

            $ad_instance = $ad_repo->createOrUpdateAd($ad_parsed_data, $adset->id, $carbon_now,  $connection->pcode, $extra_data, $connection->sales_type, 'snapchat_refresh_date');

            return $ad_instance;
        } catch (\Throwable $th) {

            if ($th->getCode() == 6000) {
                throw new Exception("snapchat_fetching_ads_error", 6000);
            } else if ($th->getCode() == 9000) {
                throw new Exception("teebalhoor_products_error", 6000);
            } else if ($th->getCode() == 404) {
                throw new Exception("snapchat_invalid_id", 404);
            }
            throw new Exception("snapchat_creating_ads_error" . $th->getMessage(), 6001);
        }
    }

    private static function fetchSingleAd(Application $application, $timezone, string $ad_id)
    {
        try {
            $today = Carbon::now()->setTimezone($timezone);
            // $start_time = $today->toDateString();
            // $end_time = $today->addDay()->toDateString();

            $start_time = self::$start_date;
            $end_time = self::$end_date;
            $url = PlatformUrl::$SNAPCHAT_AD_BASE_URL . "/ads/$ad_id";
            $ad_response = self::authenticateAndFetch($url, $application);
            $fields = SnapchatQuery::$AD_STATS;
            $url = $url . "/stats?fields=$fields&start_time=$start_time&end_time=$end_time";
            $ad_stats_repsonse = self::authenticateAndFetch($url, $application);
            $stats_collection = collect($ad_stats_repsonse)->get("total_stats");
            $stats_collection = collect($stats_collection)->first();
            $stats_collection = collect($stats_collection)->get("total_stat");
            $ad_stats = collect($stats_collection)->get("stats");
            $collection = collect($ad_response)->get("ads");
            $collection = collect($collection)->first();
            $ad_item = collect($collection)->get("ad");
            $result_item = collect($ad_item)->merge($ad_stats);
            return $result_item;
        } catch (\Throwable $th) {
            throw new Exception("snapchat_invalid_id", 404);
        }
    }


    private static function fetchSingleAdPerDay(Application $application, $timezone, string $ad_id)
    {
        try {
            $start_time = self::$start_date;
            $end_time = self::$end_date;
            $url = PlatformUrl::$SNAPCHAT_AD_BASE_URL . "/ads/$ad_id";
            $ad_response = self::authenticateAndFetch($url, $application);
            $fields = SnapchatQuery::$AD_STATS;
            $url = $url . "/stats?granularity=DAY&fields=$fields&start_time=$start_time&end_time=$end_time";
            $ad_stats_repsonse = self::authenticateAndFetch($url, $application);
            $stats_collection = collect($ad_stats_repsonse)->get("timeseries_stats");
            $stats_collection = collect($stats_collection)->first();
            $stats_collection = collect($stats_collection)->get("timeseries_stat");
            $stats_collection = collect($stats_collection)->get("timeseries");
            $stats_collection = collect($stats_collection)->first();
            $ad_stats = collect($stats_collection)->get("stats");


            $collection = collect($ad_response)->get("ads");
            $collection = collect($collection)->first();
            $ad_item = collect($collection)->get("ad");
            $result_item = collect($ad_item)->merge($ad_stats);
            return $result_item;
        } catch (\Throwable $th) {

            throw new Exception("snapchat_invalid_id", 404);
        }
    }


    private static function parseAdRawData(Collection $ad_item_data, Collection $crm_data)
    {
        $deliverd = (int) $crm_data->get("Deliverd");
        $logis_cancelled = (int) $crm_data->get("LogCanceld");
        $total_picked_up = (int) $crm_data->get("Pickedup");
        $picked_up = $total_picked_up - ($logis_cancelled + $deliverd);
        $delivery_status = $ad_item_data->get("delivery_status");
        return
            [
                "ad_name" => $ad_item_data->get("name"),
                "ad_id" => $ad_item_data->get("id"),
                "server_adset_id" => $ad_item_data->get("ad_squad_id"),
                "status" => AdvertisementUtil::getStatus($ad_item_data->get("status")),
                "impressions" => $ad_item_data->get("impressions"),
                "spend" => AdvertisementUtil::microDollarToDollar($ad_item_data->get("spend")),
                "clicks" => $ad_item_data->get("swipes"),
                "view_completion" => $ad_item_data->get("view_completion"),
                "viewable_impressions" => $ad_item_data->get("viewable_impressions"),
                "two_second_video_views" => $ad_item_data->get("quartile_1"),
                "six_second_video_views" => $ad_item_data->get("quartile_2"),
                "delivery_status" =>  is_array($delivery_status) ? implode(",", $delivery_status) : $delivery_status,
                "average_screen_time" => AdvertisementUtil::millisToSeconds($ad_item_data->get("screen_time_millis")),
                "video_views" => $ad_item_data->get("video_views"),
                "story_opens" => $ad_item_data->get("story_opens"),
                "optimization_goal" => $ad_item_data->get("optimization_goal"),
                "crm_total_orders" => (int) $crm_data->get("total"),
                "crm_confirm" => (int) $crm_data->get("confirm"),
                "crm_repeated" => (int) $crm_data->get("repeated"),
                "crm_cancelled" => (int) $crm_data->get("cancel"),
                "crm_total_pickedup" => (int) $crm_data->get("Pickedup"),
                "crm_pickedup" => AdvertisementUtil::round($picked_up),
                "crm_logis_deliverd" => (int) $crm_data->get("Deliverd"),
                "crm_logis_cancelled" => $logis_cancelled,
                "crm_total_sale" => AdvertisementUtil::round($crm_data->get("TotalSale")),
                "crm_product_cost" => AdvertisementUtil::round($crm_data->get("ProductCost")),
                "crm_delivery_cost" => AdvertisementUtil::round($crm_data->get("delivery_cost")),
                "exact_price" => AdvertisementUtil::round($crm_data->get("exact_price")),
                "sale_price" => AdvertisementUtil::round($crm_data->get("sale_price")),
                "server_created_at" => $ad_item_data->get("created_at"),
                "server_updated_at" => $ad_item_data->get("updated_at"),
            ];
    }

    /**
     * AD SECTION END
     */

    /**
     * Get Access Token and Refresh Token & Create Application
     */

    public static function getTokenAndStore(array $application_data, $application_id = null)
    {
        $query = [
            "client_id" => $application_data["client_id"],
            "client_secret" => $application_data["client_secret"],
            "code" => $application_data["code"],
            "grant_type" => "authorization_code",
            "scope" => "snapchat-marketing-api",
            "redirect_uri" => PlatformUrl::$SNAPCHAT_REDIRECT_URL,
        ];
        $url = PlatformUrl::$SNAPCHAT_AUTH_URL . "/access_token";
        $response = Http::withOptions(["query" => $query])->post($url);
        if ($response->successful()) {
            $token_data =  $response->json();
            if ($application_id) {
                $update = [
                    "access_token" => $token_data["access_token"],
                    "refresh_token" => $token_data["refresh_token"],
                    "expires_in" => $token_data["expires_in"],
                    "updated_by" => auth()->id(),
                    "system_status" => "ACTIVE",
                ];
                Application::where("id", $application_id)->update($update);
                return true;
            }
            $attributes = [
                "code" => rand(1000000, 100000000),
                "name" => $application_data["name"],
                "platform_id" => $application_data["platform_id"],
                "organization_id" => $application_data["organization_id"],
                "redirect_url" =>  self::redirectUrl($application_data["client_id"]),
                "scope" => self::$SCOPE,
                "client_id" => $application_data["client_id"],
                "client_secret" => $application_data["client_secret"],
                "access_token" => $token_data["access_token"],
                "refresh_token" => $token_data["refresh_token"],
                "expires_in" => $token_data["expires_in"],
                "created_by" => auth()->id(),
                "updated_by" => auth()->id(),
                "country_id" => $application_data["country_id"],
            ];
            $check_application = Application::where([
                ["client_id", "=", $attributes["client_id"]],
                ["client_secret", "=", $attributes["client_secret"]],
                ["scope", "=", $attributes["scope"]],
            ])->first();
            if ($check_application) return false;
            $app = Application::create($attributes);
            $app->users()->sync($application_data['users']);
            return true;
        }
        return false;
    }


    private static function redirectUrl(string $client_id)
    {
        return "https://accounts.snapchat.com/accounts/oauth2/auth?scope=" . self::$SCOPE . "&response_type=code&client_id=$client_id&redirect_uri=" . PlatformUrl::$SNAPCHAT_REDIRECT_URL;
    }

    public static function updateBidBudget($request)
    {
        try {


            $connection = connection::where('server_ad_adset_id', $request->adset_id)->first();
            $application = $connection->application;
            $campaign_id = $connection->server_campaign_id;

            $url = PlatformUrl::$SNAPCHAT_AD_BASE_URL . "/adsquads/$request->adset_id";
            $response = self::authenticateAndFetch($url, $connection->application);

            // 12000000
            // 10000000
            $adsquads = collect($response)->get("adsquads");
            $adsquad = collect($adsquads)->first();
            $adsquad = collect($adsquad)->get("adsquad");
            if ($request->type == 'bid') {
                if ($adsquad["auto_bid"] == true && !$request->auto)
                    return response()->json('is_auto_bid', Response::HTTP_NOT_IMPLEMENTED);
                if ($request->auto) {
                    if ($request->auto == 'auto') {
                        $adsquad["bid_strategy"] = "AUTO_BID";
                        $adsquad["auto_bid"] = true;
                        $adsquad["target_bid"] = false;
                        unset($adsquad["bid_micro"]);
                    } else if ($request->auto == 'custom') {
                        $adsquad["bid_micro"] = AdvertisementUtil::dollarToMicroDollar($request->value);
                        $adsquad["bid_strategy"] = "LOWEST_COST_WITH_MAX_BID";
                        $adsquad["auto_bid"] = false;
                    }
                } else
                    $adsquad["bid_micro"] = AdvertisementUtil::dollarToMicroDollar($request->value);
            } else
                $adsquad["daily_budget_micro"] = AdvertisementUtil::dollarToMicroDollar($request->value);

            $update_url = PlatformUrl::$SNAPCHAT_AD_BASE_URL . "/campaigns/$campaign_id/adsquads";
            $body = ["adsquads" => [$adsquad]];
            $update_response = self::authenticateAndUpdate($update_url, $application, $body);

            $adset_attributes = self::fetchAdsetsColumn($update_response);
            $adset = HistoryAdsetRepository::updateAdsetBidBudget($adset_attributes->toArray(), $request);

            // $response = Http::withToken($adset->application->access_token)->get($url);
            return $adset;
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public static function fetchAdsetsColumn($response)
    {

        $collection = collect($response)->get("adsquads");
        $collection = collect($collection)->first();
        $collection = collect($collection)->get("adsquad");
        $adset_item = collect($collection);
        $result_item =  [
            "adset_id" => $adset_item->get("id"),
            "daily_budget" => AdvertisementUtil::microDollarToDollar($adset_item->get("daily_budget_micro")),
            "bid" => AdvertisementUtil::microDollarToDollar($adset_item->get("bid_micro")),
            "server_campaign_id" => $adset_item->get("campaign_id"),


        ];
        return collect($result_item);
    }

    // disable ad status
    // rohullah function

    public static function disableAdStatus(Connection $connection)
    {
        try {
            $application = $connection->application;
            $adset_id = $connection->server_ad_adset_id;
            $ad_id = $connection->server_ad_id;
            $url = PlatformUrl::$SNAPCHAT_AD_BASE_URL . "/ads/$ad_id";
            $response = self::authenticateAndFetch($url, $application);
            $ads = collect($response)->get("ads");
            $ad_item = collect($ads)->first();
            $ad_item = collect($ad_item)->get("ad");
            $current_status = collect($ad_item)->get("status");
            $ad_item["status"] = "PAUSED";
            $update_url = PlatformUrl::$SNAPCHAT_AD_BASE_URL . "/adsquads/$adset_id/ads";
            $body = ["ads" => [$ad_item]];


            $update_response = self::updateOnly($update_url, $application, $body);
            if ($update_response["request_status"] == "SUCCESS") {
                return "success";
            }
            return "failed";
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public static function disableAdsetStatus(Connection $connection)
    {
        try {
            $application = $connection->application;
            $campaign_id = $connection->server_campaign_id;
            $adset_id = $connection->server_ad_adset_id;
            $url = PlatformUrl::$SNAPCHAT_AD_BASE_URL . "/adsquads/$adset_id";
            $response = self::authenticateAndFetch($url, $application);
            $adsquads = collect($response)->get("adsquads");
            $adsquad = collect($adsquads)->first();
            $adsquad = collect($adsquad)->get("adsquad");
            $adsquad["status"] = "PAUSED";
            $update_url = PlatformUrl::$SNAPCHAT_AD_BASE_URL . "/campaigns/$campaign_id/adsquads";
            $body = ["adsquads" => [$adsquad]];
            $update_response = self::updateOnly($update_url, $application, $body);
            if ($update_response["request_status"] == "SUCCESS") {
                return "success";
            }
            return "failed";
        } catch (\Throwable $th) {
            return "failed";
        }
    }

    public static function disableCampaignStatus(Connection $connection)
    {
        try {
            $application = $connection->application;
            $account_id = $connection->server_account_id;
            $campaign_id = $connection->server_campaign_id;
            $url = PlatformUrl::$SNAPCHAT_AD_BASE_URL . "/campaigns/$campaign_id";
            $response = self::authenticateAndFetch($url, $application);
            $campaigns = collect($response)->get("campaigns");
            $campaign = collect($campaigns)->first();
            $campaign = collect($campaign)->get("campaign");
            $current_status = collect($campaign)->get("status");
            $campaign["status"] = "PAUSED";
            $update_url = PlatformUrl::$SNAPCHAT_AD_BASE_URL . "/adaccounts/$account_id/campaigns";
            $body = ["campaigns" => [$campaign]];
            $update_response = self::updateOnly($update_url, $application, $body);
            if ($update_response["request_status"] == "SUCCESS") {
                return "success";
            }
            return "failed";
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
    // rohullah function
    public static function updateOnly($url, $application, $body, $break = false)
    {
        $token = $application->access_token;
        $response = Http::withToken($token)->put($url, $body);
        if ($break && $response->failed()) {
            if ($response->status() === Response::HTTP_UNAUTHORIZED) {
                AdvertisementUtil::expireApplication($application);
                throw new Exception("snapchat_token_expired", 401);
            }
            throw new Exception("snapchat_fetching_error", $response->status());
        }
        if ($response->status() == Response::HTTP_UNAUTHORIZED) {
            $is_logged_in =  self::refreshToken($application);
            if ($is_logged_in) return self::updateOnly($url, $application, $body, true);
            return [];
        }
        if ($response->successful()) {
            return $response->json();
        }
        $response->throw();
    }
}
