<?php

namespace App\Repositories\Advertisement\Platforms;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\Advertisement\AdAccount;
use App\Models\Advertisement\Connection;
use App\Models\Advertisement\Application;
use App\Repositories\Advertisement\PlatformUrl;
use App\Repositories\Advertisement\AdvertisementUtil;
use App\Repositories\Advertisement\HistoryAdRepository;
use App\Repositories\Advertisement\Queries\FacebookQuery;
use App\Repositories\Advertisement\HistoryAdsetRepository;
use App\Repositories\Advertisement\HistoryCampaignRepository;

class Facebook
{
    private static string $SCOPE = "ads_management";
    private static string $start_date;
    private  static string $end_date;

    /**
     * Fetch Facebook Ads Accounts
     */

    public static function fetchAddAccounts($application_id)
    {
        $application = Application::withTrashed()->find($application_id);
        if ($application) {
            $token = $application->access_token;
            $fields = "id%2Cname%2Cadaccounts%7Bname%7D";
            $url = PlatformUrl::$FB_AD_BASE_URL . "/me?fields=$fields&access_token=$token";
            $response = Http::get($url);
            if ($response->successful()) {
                $ad_accounts = $response->json()["adaccounts"];
                return  collect($ad_accounts["data"])->map(function ($item) use ($application) {

                    return array_merge($item, [
                        'application_id' => $application->id,
                        'platform_id' => $application->platform_id
                    ]);
                });
                // a
            }
            $response->throw();
        }
        return [];
    }

    /**
     * CAMPAIGN SECTION
     */
    public static function fetchSingleCampaign(Application $application, string $campaign_id)
    {
        $token = $application->access_token;
        $fields = FacebookQuery::$CAMPAIGN_QUERY;
        $url = PlatformUrl::$FB_AD_BASE_URL . "/$campaign_id/?fields=$fields&access_token=$token";
        $response = Http::get($url);
        if ($response->successful()) {
            $campaign = $response->json();
            $collection = collect($campaign);
            return [
                "campaign_id" => $collection->get("id"),
                "name" => $collection->get("name"),
                "status" => AdvertisementUtil::getStatus($collection->get("status")),
                "delivery_status" => AdvertisementUtil::getStatus($collection->get("effective_status")),
                "objective" => $collection->get("objective"),
                "start_time" => $collection->get("start_time"),
                "end_time" => $collection->get("stop_time"),
                "server_created_at" => $collection->get("created_time"),
                "server_updated_at" => $collection->get("updated_time"),
            ];
        }
        $collection = collect($response->json("error"));
        if ($collection->get("type") == "OAuthException" || $response->status() == Response::HTTP_UNAUTHORIZED) {
            AdvertisementUtil::expireApplication($application);
        }
        return null;
    }

    /**
     * ADSET SECTION
     */
    public static function fetchSingleAdset(Application $application, string $currency, string $adset_id)
    {
        $token = $application->access_token;
        $fields = FacebookQuery::$ADSET_QUERY;
        $url = PlatformUrl::$FB_AD_BASE_URL . "/$adset_id/?fields=$fields&access_token=$token";
        $response = Http::get($url);
        $collection = collect($response->json("error"));
        if ($collection->get("type") == "OAuthException" || $response->status() == Response::HTTP_UNAUTHORIZED) {
            AdvertisementUtil::expireApplication($application);
        }
        $adset_item = $response->json();
        $collection = collect($adset_item);

        $daily_budget = self::getDollar($collection->get("daily_budget"));

        return [
            "adset_id" => $collection->get("id"),
            "name" => $collection->get("name"),
            "status" => AdvertisementUtil::getStatus($collection->get("status")),
            "delivery_status" => AdvertisementUtil::getStatus($collection->get("effective_status")),
            "daily_budget" => $daily_budget,
            "optimization_goal" => $collection->get("optimization_goal"),
            "server_campaign_id" => $collection->get("campaign_id"),
            "currency" => $currency,
            "start_time" => $collection->get("start_time"),
            "server_created_at" => $collection->get("created_time"),
            "server_updated_at" => $collection->get("updated_time"),
        ];
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
            throw new Exception($th->getMessage(), 500);
        }
    }
    /**
     * AD SECTION START
     */

    public static function createAdWithAdsetAndCampaign(Connection $connection, $ad_id, $extra_data = null)
    {
        try {
            $application = $connection->application;
            $ad_account = $connection->adAccount;
            self::setDate($extra_data);
            $extra_data['start_date'] = self::$start_date;

            $ad_item_raw_data = self::fetchSingleAd($application, $ad_id);
            $account_id = Str::after($ad_account->account_id, 'act_');
            if ($ad_item_raw_data->get("account_id") != $account_id) {
                throw new Exception("facebook_invalid_id", 404);
            }
            $ad_item_raw_data->put("server_account_id", $ad_account->account_id);
            $campaign_id = $ad_item_raw_data->get("campaign_id");
            $adset_id = $ad_item_raw_data->get("adset_id");
            $crm_item_raw_data = Teebalhoor::serverProducts($connection, $extra_data);
            $ga_raw_data = GoogleAnalytics::fetchGoogleAnalytics("282328672", $connection->hashed_code, $extra_data);
            $campaign_attributes = self::fetchSingleCampaign($application, $campaign_id);
            $adset_attributes = self::fetchSingleAdset($application, $ad_account->currency, $adset_id);
            $carbon_now = Carbon::parse(self::$start_date);
            $campaign_repo = new HistoryCampaignRepository();
            $campaign = $campaign_repo->createOrUpdateCampaign($campaign_attributes, $ad_account, $carbon_now);
            $adset_repo = new HistoryAdsetRepository();
            $adset = $adset_repo->createOrUpdateAdset($adset_attributes, $campaign->id, $carbon_now);
            $ad_parsed_data = self::parseAdRawData($ad_item_raw_data, $crm_item_raw_data, $ad_account->currency);
            $ad_parsed_data = array_merge($ad_parsed_data, $ga_raw_data);
            $ad_repo = new HistoryAdRepository();
            $ad_instance = $ad_repo->createOrUpdateAd($ad_parsed_data, $adset->id, $carbon_now, $connection->pcode, $extra_data, $connection->sales_type, 'facebook_refresh_date');
            return $ad_instance;
        } catch (\Throwable $th) {
            if ($th->getCode() == 6000) {
                throw new Exception("facebook_fetching_ads_error", 6000);
            } else if ($th->getCode() == 9000) {
                throw new Exception("teebalhoor_products_error", 6000);
            } else if ($th->getCode() == 404) {
                throw new Exception("facebook_invalid_id", 404);
            }
            throw new Exception("facebook_creating_ads_error", 6001);
        }
    }

    private static function parseAdRawData(Collection $ad_item_data, Collection $crm_data, $currency)
    {
        $video_p25_watched_actions = $ad_item_data->get("video_p25_watched_actions");
        if ($video_p25_watched_actions) {
            $video_p25_watched_actions = $video_p25_watched_actions[0]["value"];
        }

        $video_p50_watched_actions = $ad_item_data->get("video_p50_watched_actions");
        if ($video_p50_watched_actions) {
            $video_p50_watched_actions = $video_p50_watched_actions[0]["value"];
        }
        $cost_per_fifteen_sec_video_view = $ad_item_data->get("cost_per_fifteen_sec_video_view");
        if ($cost_per_fifteen_sec_video_view) {
            $cost_per_fifteen_sec_video_view = $cost_per_fifteen_sec_video_view[0]["value"];
        }

        $deliverd = (int) $crm_data->get("Deliverd");
        $logis_cancelled = (int) $crm_data->get("LogCanceld");
        $total_picked_up = (int) $crm_data->get("Pickedup");
        $picked_up = $total_picked_up - ($logis_cancelled + $deliverd);

        return
            [
                "ad_name" => $ad_item_data->get("name"),
                "ad_id" => $ad_item_data->get("id"),
                "server_adset_id" => $ad_item_data->get("adset_id"),
                "server_account_id" => $ad_item_data->get("server_account_id"),
                "status" => AdvertisementUtil::getStatus($ad_item_data->get("status")),
                "impressions" => $ad_item_data->get("impressions"),
                "spend" => $ad_item_data->get("spend"),
                "clicks" => $ad_item_data->get("clicks"),
                "currency" => $currency,
                "viewable_impressions" => $ad_item_data->get("viewable_impressions"),
                "two_second_video_views" => $video_p25_watched_actions,
                "six_second_video_views" => $video_p50_watched_actions,
                "cost_per_fifteen_sec_video_view" => $cost_per_fifteen_sec_video_view,
                "frequency" => AdvertisementUtil::round($ad_item_data->get("frequency")),
                "objective" => $ad_item_data->get("objective"),
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
                "start_time" => $ad_item_data->get("date_start"),
                "end_time" => $ad_item_data->get("date_stop"),
                "server_created_at" => $ad_item_data->get("created_time"),
                "server_updated_at" => $ad_item_data->get("updated_time"),
            ];
    }


    private static function fetchSingleAd(Application $application, $ad_id)
    {
        $ad_url = PlatformUrl::$FB_AD_BASE_URL . "/$ad_id";
        $ad_insight_url = PlatformUrl::$FB_AD_BASE_URL .
            "/$ad_id/insights";
        $ad_response = Http::get($ad_url);
        $ad_insight_response = Http::get($ad_insight_url);
        if ($ad_response->successful() && $ad_insight_response->successful()) {
            $ad_data = $ad_response->json() ?? [];
            $ad_insight_data = $ad_insight_response->json("data") ?? [];
            if (count($ad_insight_data) > 0) {
                $ad_insight_data = $ad_insight_data[0];
            }
            $collection = collect(array_merge($ad_data, $ad_insight_data));
            return $collection;
        }
    }

    /**
     * AD SECTION END
     */


    /**
     * AD ACCOUNT SECTION START
     */

    public static function findAdAccount(Application $application, string $account_id)
    {
        $token = $application->access_token;
        $fields = FacebookQuery::$AD_ACCOUNT_QUERY;
        $url = PlatformUrl::$FB_AD_BASE_URL . "/$account_id?fields=$fields&access_token=$token";
        $response = Http::get($url);
        if ($response->successful()) {
            $ad_account = $response->json();
            return $ad_account;
        }
        $collection = collect($response->json("error"));
        if ($collection->get("type") == "OAuthException" || $response->status() == Response::HTTP_UNAUTHORIZED) {
            AdvertisementUtil::expireApplication($application);
        }
        return null;
    }

    public static function findAndstoreAccount(Application $application, string $account_id)
    {
        $account = self::findAdAccount($application, $account_id);
        if ($account) {
            $account = self::filterAccountColumns($account, $application->id);
            return AdAccount::create($account);
        }
        return null;
    }

    public static function filterAccountColumns(array $account, string $application_id)
    {
        return
            [
                "code" => uniqueCode(AdAccount::class, "ACC"),
                "name" => AdvertisementUtil::getValue("name", $account),
                "account_id" => AdvertisementUtil::getValue("id", $account),
                "status" => AdvertisementUtil::getStatus(AdvertisementUtil::getValue("account_status", $account)),
                "balance" => AdvertisementUtil::getValue("balance", $account),
                "currency" => AdvertisementUtil::getValue("currency", $account),
                "timezone_name" => AdvertisementUtil::getValue("timezone_name", $account),
                "server_created_at" => AdvertisementUtil::getValue("created_time", $account),
                "application_id" => $application_id,
            ];
    }

    /**
     * AD ACCOUNT SECTION END
     */


    /**
     * Get Access Token and Refresh Token & Create Application
     */

    public static function getTokenAndStore(array $application_data, $application_id = null)
    {

        $body = [
            "client_id" => $application_data["client_id"],
            "client_secret" => $application_data["client_secret"],
            "code" => $application_data["code"],
            "redirect_uri" => PlatformUrl::$FB_REDIRECT_URL,
        ];
        $url = PlatformUrl::$FB_AUTH_BASE_URL . "access_token";
        $response = Http::post($url, $body);
        if ($response->successful()) {
            $token_data =  $response->json();
            if ($application_id) {
                $update = [
                    "access_token" => $token_data["access_token"],
                    "refresh_token" => $token_data["refresh_token"],
                    "expires_in" => 0,
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
                "redirect_url" =>  self::redirectUrl($application_data['client_id']),
                "scope" => self::$SCOPE,
                "client_id" => $application_data["client_id"],
                "client_secret" => $application_data["client_secret"],
                "access_token" => $token_data["access_token"],
                "refresh_token" => $token_data["access_token"],
                "expires_in" => 0,
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
        return "https://www.facebook.com/v8.0/dialog/oauth?client_id=$client_id&redirect_uri="
            . PlatformUrl::$FB_REDIRECT_URL;
    }
    // convert micro dollar to dollar
    public static function getDollar(int|float|string|null $num, int $precision = 2)
    {
        $num  = (float) $num;
        $num = $num / 100;
        return round($num, $precision);
    }
}
