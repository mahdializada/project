<?php

namespace App\Repositories\Advertisement\Platforms;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use App\Models\Advertisement\AdAccount;
use App\Models\Advertisement\Application;
use App\Repositories\Advertisement\PlatformUrl;
use App\Repositories\Advertisement\AdvertisementUtil;
use App\Repositories\Advertisement\HistoryAdRepository;
use App\Repositories\Advertisement\Queries\GoogleQuery;
use App\Repositories\Advertisement\HistoryAdsetRepository;
use App\Repositories\Advertisement\HistoryCampaignRepository;

class GoogleAd
{

    private static string $SCOPE = "https://www.googleapis.com/auth/adwords";
    private static string $start_date;
    private  static string $end_date;

    /**
     * AD ACCOUNT SECTION START
     */

    public static function findAndstoreAccount(Application $application, string $account_id)
    {
        $options = ["method" => "POST", "body" => ["query" => GoogleQuery::$ACCOUNT_QUERY]];
        $options["url"] = "customers/$account_id/googleAds:search";
        $response = self::authenticateAndFetch($application, $options);
        $collection = collect($response)->get("results");
        $first_item = collect($collection)->first();
        if ($first_item) {
            $account = collect($first_item)->get("customer");
            $account = collect($account);
            $account_attributes = self::filterAccountColumns($account, $application->id);
            return AdAccount::create($account_attributes);
        }
        return null;
    }


    public static function filterAccountColumns(Collection $account, string $application_id)
    {
        return
            [
                "code" => uniqueCode(AdAccount::class, "ACC"),
                "name" => $account->get("descriptiveName"),
                "account_id" => $account->get("id"),
                "status" => AdvertisementUtil::getStatus($account->get("status")),
                "currency" => $account->get("currencyCode"),
                "timezone_name" => $account->get("timeZone"),
                "application_id" => $application_id,
            ];
    }

    /**
     * AD ACCOUNT SECTION END
     */
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
        }
    }
    /**
     * AD SECTION START
     */

    public static function createAdWithAdsetAndCampaign($connection, $ad_id, $extra_data = null)
    {
        try {
            $application = $connection->application;
            $ad_account = $connection->adAccount;
            self::setDate($extra_data);
            $extra_data['start_date'] = self::$start_date;
            $ad_item_raw_data = self::fetchSingleAd($application, $ad_account->account_id,  $ad_id);
            $adset_id = $ad_item_raw_data->get("server_adset_id");
            $account_id = $ad_item_raw_data->get("server_account_id");
            if ($account_id != $ad_account->account_id) {
                throw new Exception("google_ads_invalid_id", 404);
            }
            $adset_attributes = self::fetchSingleAdset($application, $ad_account, $adset_id);
            $campaign_id = $adset_attributes->get("server_campaign_id");
            $campaign_attributes = self::fetchSingleCampaign($application, $ad_account->account_id, $campaign_id);
            $crm_item_raw_data = Teebalhoor::serverProducts($connection, $extra_data);
            $ga_raw_data = GoogleAnalytics::fetchGoogleAnalytics("282328672", $connection->hashed_code, $extra_data);
            $carbon_now = Carbon::parse(self::$start_date);
            $campaign_repo = new HistoryCampaignRepository();
            $campaign = $campaign_repo->createOrUpdateCampaign($campaign_attributes->toArray(), $ad_account, $carbon_now);
            $adset_repo = new HistoryAdsetRepository();
            $adset = $adset_repo->createOrUpdateAdset($adset_attributes->toArray(), $campaign->id, $carbon_now);
            $ad_parsed_data = self::parseAdRawData($ad_item_raw_data, $crm_item_raw_data);
            $ad_parsed_data["currency"] = $ad_account->currency;
            $ad_parsed_data = array_merge($ad_parsed_data, $ga_raw_data);
            $ad_repo = new HistoryAdRepository();
            $ad_instance = $ad_repo->createOrUpdateAd($ad_parsed_data, $adset->id, $carbon_now,  $connection->pcode, $extra_data, $connection->sales_type, 'google_ads_refresh_date');
            return $ad_instance;
        } catch (\Throwable $th) {
            if ($th->getCode() == 6000) {
                throw new Exception("google_ads_fetching_ads_error", 6000);
            } else if ($th->getCode() == 9000) {
                throw new Exception("teebalhoor_products_error", 6000);
            } else if ($th->getCode() == 404) {
                throw new Exception("google_ads_invalid_id", 404);
            }
            throw new Exception("google_ads_creating_ads_error", 6001);
        }
    }


    public static function fetchSingleAd(Application $application, string $account_id, string $ad_id)
    {
        $query = GoogleQuery::$ADS_QUERY;
        $query = $query . "AND ad_group_ad.ad.id = $ad_id";
        $options = ["method" => "POST", "body" => ["query" => $query]];
        $options["url"] = "customers/$account_id/googleAds:search";
        $response = self::authenticateAndFetch($application, $options);
        $collection = collect($response)->get("results");
        $first_item = collect($collection)->first();
        $ad_item = collect($first_item);
        $account = collect($ad_item->get("customer"));
        $campaign = collect($ad_item->get("campaign"));
        $adset = collect($ad_item->get("adGroup"));
        $ad_item = $ad_item->only(["metrics", "adGroupAd"]);
        $ad_item->put("server_adset_id", $adset->get("id"));
        $ad_item->put("server_campaign_id", $campaign->get("id"));
        $ad_item->put("server_account_id", $account->get("id"));
        return $ad_item;
    }


    private static function parseAdRawData(Collection $ad_item_data, Collection $crm_data)
    {
        $deliverd = (int) $crm_data->get("Deliverd");
        $logis_cancelled = (int) $crm_data->get("LogCanceld");
        $total_picked_up = (int) $crm_data->get("Pickedup");
        $picked_up = $total_picked_up - ($logis_cancelled + $deliverd);
        $ad_group_ad = collect($ad_item_data->get("adGroupAd"));
        $ad_data = collect($ad_group_ad->get("ad"));
        $metrics = collect($ad_item_data->get("metrics"));
        return
            [
                "ad_name" => $ad_data->get("name"),
                "ad_id" => $ad_data->get("id"),
                "server_adset_id" => $ad_item_data->get("server_adset_id"),
                "server_account_id" => $ad_item_data->get("server_account_id"),
                "status" => AdvertisementUtil::getStatus($ad_group_ad->get("status")),
                "impressions" => $metrics->get("impressions"),
                "spend" => self::microDollarToDollar($metrics->get("costMicros")),
                "clicks" => $metrics->get("clicks"),
                "viewable_impressions" => $metrics->get("activeViewImpressions"),
                "total_page_views" => $metrics->get(" metricsAveragePageViews"),
                "video_views" => $metrics->get("videoViews"),
                "crm_total_pickedup" => (int) $crm_data->get("Pickedup"),
                "crm_pickedup" => AdvertisementUtil::round($picked_up),
                "crm_logis_deliverd" => (int) $crm_data->get("Deliverd"),
                "crm_logis_cancelled" => $logis_cancelled,
                "crm_total_sale" => AdvertisementUtil::round($crm_data->get("TotalSale")),
                "crm_product_cost" => AdvertisementUtil::round($crm_data->get("ProductCost")),
                "crm_delivery_cost" => AdvertisementUtil::round($crm_data->get("delivery_cost")),
            ];
    }
    /**
     * AD SECTION END
     */

    /**
     * ADSET SECTION START
     */


    public static function fetchSingleAdset(Application $application, AdAccount $account, string $adset_id)
    {
        $account_id = $account->account_id;
        $query = GoogleQuery::$ADSET_QUERY;
        $query = $query . "AND ad_group.id = $adset_id";
        $options = ["method" => "POST", "body" => ["query" => $query]];
        $options["url"] = "customers/$account_id/googleAds:search";
        $response = self::authenticateAndFetch($application, $options);
        $collection = collect($response)->get("results");
        $first_item = collect($collection)->first();
        $adset_item = collect($first_item);
        $campaign = collect($adset_item->get("campaign"));
        $adset_item = collect($adset_item->get("adGroup"));
        $result_item =  [
            "adset_id" => $adset_item->get("id"),
            "name" => $adset_item->get("name"),
            "status" => AdvertisementUtil::getStatus($adset_item->get("status")),
            "server_campaign_id" => $campaign->get("id"),
            "currency" => $account->currency,
        ];
        return collect($result_item);
    }

    /**
     * ADSET SECTION END
     */



    /**
     * CAMPAIGN SECTION START
     */

    public static function fetchSingleCampaign(Application $application, string $account_id, string $campaign_ad)
    {
        $query = GoogleQuery::$CAMPAIGN_QUERY;
        $query = $query . "AND campaign.id = $campaign_ad";
        $options = ["method" => "POST", "body" => ["query" => $query]];
        $options["url"] = "customers/$account_id/googleAds:search";
        $response = self::authenticateAndFetch($application, $options);
        $collection = collect($response)->get("results");
        $first_item = collect($collection)->first();
        $campaign_item = collect($first_item);
        $campaign_budget = collect($campaign_item->get("campaignBudget"));
        $customer = collect($campaign_item->get("customer"));
        $campaign_item = collect($campaign_item->get("campaign"));
        $result_item =  [
            "campaign_id" => $campaign_item->get("id"),
            "name" => $campaign_item->get("name"),
            "status" =>  AdvertisementUtil::getStatus($campaign_item->get("status")),
            "server_account_id" => $customer->get("id"),
            "budget_mode" => $campaign_budget->get("type"),
            "budget" => self::microDollarToDollar($campaign_budget->get("amountMicros")),
            "start_time" => $campaign_item->get("startDate"),
            "end_time" => $campaign_item->get("endDate"),
        ];
        return collect($result_item);
    }


    public static function fetchAdsCampaignAndAdsetIds($connection, $ad_id)
    {
        $ad_account_id = $connection->ad_account_id;
        $application = $connection->application;
        $query = GoogleQuery::$AD_CAMPAIGN_ADSET_QUERY . $ad_id;
        $options = ["method" => "POST", "body" => ["query" => $query]];
        $options["url"] = "customers/$ad_account_id/googleAds:search";
        $response = self::authenticateAndFetch($application, $options);
        $resources = AdvertisementUtil::getValue("results", $response);
        if ($resources) {
            $first = $resources[0];
            return [
                "ad_id" => (string) $ad_id,
                "adset_id" => (string) $first["adGroup"]["id"],
                "campaign_id" => (string) $first["campaign"]["id"],
                "ad_account_id" => (string) $first["customer"]["id"],
                "currency" => $first["customer"]["currencyCode"],
            ];
        }
        throw new Exception("fetching_google_ad_error", 400);
    }

    public static function fetchAdAccounts(Application $application)
    {
        $accounts = [];
        $options = ["method" => "POST", "body" => ["query" => GoogleQuery::$ACCOUNT_QUERY]];
        $customers = self::fetchCustomers($application);
        foreach ($customers as $customer) {
            $options["url"] = "$customer/googleAds:search";
            $response = self::authenticateAndFetch($application, $options);
            $results = AdvertisementUtil::getValue("results", $response);
            if ($results && count($results) > 0) {
                $first = $results[0]["customer"];
                $is_manager = AdvertisementUtil::getValue("manager", $first);
                if ($is_manager == false) {
                    $account = self::parseAdAccounts($first, $application);
                    $accounts[] = $account;
                }
            }
        }
        return $accounts;
    }


    private static function fetchCustomers(Application $application)
    {
        $options = [
            "method" => "GET",
            "url" => "customers:listAccessibleCustomers"
        ];
        $response = self::authenticateAndFetch($application, $options);
        $resources = AdvertisementUtil::getValue("resourceNames", $response);
        if ($resources) return $resources;
        return [];
    }

    private static function parseAdAccounts(array $response, Application $application)
    {
        unset($response["resourceName"]);
        unset($response["manager"]);
        $account = [
            "id" => AdvertisementUtil::getValue("id", $response),
            "name" => AdvertisementUtil::getValue("descriptiveName", $response),
            "currency" => AdvertisementUtil::getValue("currencyCode", $response),
            "timezone" => AdvertisementUtil::getValue("timeZone", $response),
            'application_id' => $application->id,
            'platform_id' => $application->platform_id
        ];
        return $account;
    }



    /**
     * Authenticate Google ads and fetch records
     */
    public static function authenticateAndFetch(Application $application, array $options, $break = false)
    {
        $token = $application->access_token;
        $url = PlatformUrl::$GOOGLE_ADS_BASE_URL . $options["url"];
        $headers = [
            "Accept" => "application/json",
            "Content-Type" => "application/json",
            "developer-token" => $application->developer_token,
        ];
        $http = Http::withHeaders($headers)->withToken($token);
        $body = [];
        if (array_key_exists("query", $options)) {
            $http =   $http->withOptions(["query" => $options["query"]]);
        }
        if (array_key_exists("body", $options)) {
            $body =   $options["body"];
        }
        switch ($options["method"]) {
            case 'POST':
                $response = $http->post($url, $body);
                break;
            default:
                $response = $http->get($url);
                break;
        }
        if ($break && $response->failed()) {
            if ($response->status() === Response::HTTP_UNAUTHORIZED) {
                AdvertisementUtil::expireApplication($application);
                throw new Exception("google_token_or_developer_token_expired", 401);
            }
            throw new Exception("google_fetching_error", 0);
        }
        if ($response->status() == Response::HTTP_UNAUTHORIZED) {
            $is_logged_in =  self::refreshToken($application);
            if ($is_logged_in) return self::authenticateAndFetch($application, $options, true);
            return [];
        }
        if ($response->successful()) {
            return $response->json();
        }
        $response->throw();
    }


    /**
     * Refresh and Authenticate Google ads API Manager Account
     */
    public static function refreshToken(Application $application)
    {
        $body = [
            "client_id" => $application->client_id,
            "client_secret" => $application->client_secret,
            "refresh_token" => $application->refresh_token,
            "grant_type" => "refresh_token",
        ];
        $url = PlatformUrl::$GOOGLE_ADS_AUTH_BASE_URL . "token";
        $response = Http::post($url, $body);
        if ($response->successful()) {
            $data =  $response->json();
            $application->access_token = $data["access_token"];
            $application->expires_in = $data["expires_in"];
            $application->save();
            return true;
        }
        AdvertisementUtil::expireApplication($application);
        $response->throw();
        return false;
    }


    /**
     * Get Access Token and Refresh Token & Create Application
     */

    public static function getTokenAndStore(array $application_data, $application_id = null)
    {

        $body = [
            "client_id" => $application_data["client_id"],
            "client_secret" => $application_data["client_secret"],
            "code" => $application_data["code"],
            "grant_type" => "authorization_code",
            "redirect_uri" => PlatformUrl::$GOOGLE_REDIRECT_URL,
        ];
        $url = PlatformUrl::$GOOGLE_ADS_AUTH_BASE_URL . "token";
        $response = Http::post($url, $body);
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
                "developer_token" => $application_data["developer_token"],
                "redirect_url" =>  self::googleRedirectUrl($application_data["client_id"]),
                "scope" =>  self::$SCOPE,
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

    private static function googleRedirectUrl(string $client_id)
    {
        return "https://accounts.google.com/o/oauth2/v2/auth?redirect_uri=" . PlatformUrl::$GOOGLE_REDIRECT_URL . "&prompt=consent&response_type=code&client_id=$client_id&scope=" . self::$SCOPE;
    }

    private static function microDollarToDollar($micro)
    {
        $micro = (float) $micro;
        $value = $micro / 1000000;
        return $value;
    }
}
