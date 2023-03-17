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
use App\Models\Advertisement\HistoryCampaign;
use App\Repositories\Advertisement\PlatformUrl;
use App\Repositories\Advertisement\AdvertisementUtil;
use App\Repositories\Advertisement\HistoryAdRepository;
use App\Repositories\Advertisement\Queries\TikTokQuery;
use App\Repositories\Advertisement\HistoryAdsetRepository;
use App\Repositories\Advertisement\HistoryCampaignRepository;
use App\Repositories\Advertisement\Platforms\GoogleAnalytics;
use Illuminate\Support\Facades\Auth;

use function GuzzleHttp\json_encode;

class TikTok
{

    private static string $start_date;
    private  static string $end_date;
    /**
     * ACCOUNT SECTION START
     */

    public static function findAndstoreAccount(Application $application, string $account_id)
    {
        $token = $application->access_token;
        $url = PlatformUrl::$TIKTOK_V3_BASE_URL . "advertiser/info";
        $query = ["advertiser_ids" => '["' . "$account_id" . '"]'];
        $header = [
            "Content-Type" => "application/json",
            "Access-Token" => $token,
        ];
        $response = Http::withHeaders($header)->get($url, $query);
        if ($response->successful()) {
            $accounts = $response->json("data");
            $collection = collect($accounts)->get("list");
            $account = collect($collection)->first();
            $account = collect($account);
            if ($account) {
                $account_attributes = self::filterAccountColumns($account, $application->id);
                return AdAccount::create($account_attributes);
            }
        }
        $code = $response->json('code');

        if ($response->status() == Response::HTTP_UNAUTHORIZED) {
            AdvertisementUtil::expireApplication($application);
        } else if ($code == 40105) {
            AdvertisementUtil::expireApplication($application);
        } else if ($code == 40001) {
            AdvertisementUtil::expireApplication($application);
        }
        return null;
    }

    public static function filterAccountColumns(Collection $account, string $application_id)
    {
        $create_time =  Carbon::createFromTimestamp($account->get("create_time"), $account->get("timezone"));
        return
            [
                "code" => uniqueCode(AdAccount::class, "ACC"),
                "name" => $account->get("name"),
                "account_id" => $account->get("advertiser_id"),
                "status" => AdvertisementUtil::getStatus($account->get("status")),
                "balance" => $account->get("balance"),
                "currency" => $account->get("currency"),
                "timezone_name" => $account->get("timezone"),
                "server_created_at" =>  $create_time,
                "application_id" => $application_id,
            ];
    }

    /**
     * ACCOUNT SECTION END
     */

    public static function fetchAdAccounts(Application $application)
    {
        $token = $application->access_token;
        $url = PlatformUrl::$TIKTOK_ACCOUNT_V3_BASE_URL . "advertiser/get";
        $query = [
            "access_token" => $token,
            "app_id" => $application->client_id,
            "secret" => $application->client_secret,
        ];
        $header = [
            "Content-Type" => "application/json",
            "Access-Token" => $token,
        ];
        $response = Http::withHeaders($header)->get($url, $query);
        if ($response->successful()) {
            $accounts = $response->json("data");
            $collection = collect($accounts)->get("advertisers");
            if ($collection == null) {
                $collection = collect($accounts)->get("list");
            }
            $accounts = collect($collection)->map(function ($item) use ($application) {
                $temp_item = [
                    "id" => (string) $item["advertiser_id"], "name" => $item["advertiser_name"],
                    'application_id' => $application->id,
                    'platform_id' => $application->platform_id
                ];
                return $temp_item;
            })->toArray();
            return $accounts ?? [];
        }
        $response->throw();
    }

    /**
     * CAMPAIGN SECTION START
     */


    public static function updateCampaignStatus(Connection $connection)
    {
        try {
            $application = $connection->application;
            $token = $application->access_token;
            $campaign_id = $connection->server_campaign_id;
            $today = Carbon::today()->toDateString();
            $history_campaign = HistoryCampaign::where("campaign_id",  $campaign_id)
                ->where("data_date", $today)->first();
            $new_status = $history_campaign->status == 'ACTIVE' ? "DISABLE" : "ENABLE";
            $url = PlatformUrl::$TIKTOK_V3_BASE_URL . "campaign/status/update/";
            $header = ["Access-Token" => $token, "Content-Type" => "application/json"];
            $body = [
                "advertiser_id" => $connection->server_account_id,
                "operation_status" => $new_status,
                "campaign_ids" => [$campaign_id]
            ];
            $response = Http::withHeaders($header)->post($url, $body);
            $code = $response->json('code');
            if ($response->status() == Response::HTTP_UNAUTHORIZED) {
                AdvertisementUtil::expireApplication($application);
                return "token_expired";
            } else if ($code == 40105) {
                AdvertisementUtil::expireApplication($application);
                return "token_expired";
            } else if ($code == 40001) {
                AdvertisementUtil::expireApplication($application);
                return "token_expired";
            }
            $data = $response->json("data");
            $status = collect($data)->get("status");
            if ($status) {
                $new_status = $status == "ENABLE" ? "ACTIVE" : "INACTIVE";
                $history_campaign->update([
                    "status" => $new_status
                ]);
                return "success";
            }
            return "failed";
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public static function fetchCampaignAttributes(Application $application, string $accound_id, string $campaign_id)
    {
        $token = $application->access_token;
        $url = PlatformUrl::$TIKTOK_V3_BASE_URL . "campaign/get/?advertiser_id=$accound_id&page_size=1000";
        $header = ["Access-Token" => $token, "Content-Type" => "application/json"];
        $url = $url . '&filtering={"campaign_ids":["' . $campaign_id . '"]}';
        $response = Http::withHeaders($header)->get($url);
        $code = $response->json('code');
        if ($response->status() == Response::HTTP_UNAUTHORIZED) {
            AdvertisementUtil::expireApplication($application);
        } else if ($code == 40105) {
            AdvertisementUtil::expireApplication($application);
        } else if ($code == 40001) {
            AdvertisementUtil::expireApplication($application);
        }
        $data = $response->json("data");
        $collection = collect($data)->get("list");
        $first_item = collect($collection)->first();
        $campaign = collect($first_item);
        return self::parseCampaignItem($campaign);
    }

    private static function parseCampaignItem(Collection $campaign_item)
    {
        return [
            "campaign_id" => $campaign_item->get("campaign_id"),
            "name" => $campaign_item->get("campaign_name"),
            "status" => AdvertisementUtil::getStatus($campaign_item->get("secondary_status")),
            "objective" => $campaign_item->get("objective"),
            "objective_type" => $campaign_item->get("objective_type"),
            "budget_mode" => $campaign_item->get("budget_mode"),
            "budget" => $campaign_item->get("budget"),
            "campaign_type" => $campaign_item->get("campaign_type"),
            "server_account_id" => $campaign_item->get("advertiser_id"),
            "server_created_at" => $campaign_item->get("create_time"),
            "server_updated_at" => $campaign_item->get("modify_time"),
        ];
    }

    /**
     * CAMPAIGN SECTION END
     */


    /**
     * ADSET SECTION START
     */



    public static function updateAdsetStatus(Connection $connection)
    {
        try {
            $application = $connection->application;
            $token = $application->access_token;
            $adset_id = $connection->server_ad_adset_id;
            $today = Carbon::today()->toDateString();
            $history_adset = HistoryAdset::where("adset_id",  $adset_id)
                ->where("data_date", $today)->first();
            $new_status = $history_adset->status == 'ACTIVE' ? "DISABLE" : "ENABLE";
            $url = PlatformUrl::$TIKTOK_V3_BASE_URL . "adgroup/status/update/";
            $header = ["Access-Token" => $token, "Content-Type" => "application/json"];
            $body = [
                "advertiser_id" => $connection->server_account_id,
                "operation_status" => $new_status,
                "adgroup_ids" => [$adset_id]
            ];
            $response = Http::withHeaders($header)->post($url, $body);
            $code = $response->json('code');
            if ($response->status() == Response::HTTP_UNAUTHORIZED) {
                AdvertisementUtil::expireApplication($application);
                return "token_expired";
            } else if ($code == 40105) {
                AdvertisementUtil::expireApplication($application);
                return "token_expired";
            } else if ($code == 40001) {
                AdvertisementUtil::expireApplication($application);
                return "token_expired";
            }
            $data = $response->json("data");
            $status = collect($data)->get("status");
            if ($status) {
                $new_status = $status == "ENABLE" ? "ACTIVE" : "INACTIVE";
                $history_adset->update([
                    "status" => $new_status
                ]);
                return "success";
            }
            return "failed";
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }


    public static function fetchAdsetAttributes(Application $application, AdAccount $account, string $adset_id)
    {
        $account_id = $account->account_id;
        $token = $application->access_token;
        $header = ["Access-Token" => $token, "Content-Type" => "application/json"];
        $fields = TikTokQuery::$ADSET_FIELDS;
        $params = '?advertiser_id=' . $account_id . '&page_size=1000';
        $url = PlatformUrl::$TIKTOK_V3_BASE_URL . "adgroup/get/$params&fields=$fields";
        $url = $url . '&filtering={"adgroup_ids":["' . $adset_id . '"]}';
        $response = Http::withHeaders($header)->get($url);
        $code = $response->json('code');
        if ($response->status() == Response::HTTP_UNAUTHORIZED) {
            AdvertisementUtil::expireApplication($application);
        } else if ($code == 40105) {
            AdvertisementUtil::expireApplication($application);
        } else if ($code == 40001) {
            AdvertisementUtil::expireApplication($application);
        }
        $body = $response->json("data");
        $collection = collect($body)->get("list");
        $first_item = collect($collection)->first();
        $adset_item = collect($first_item);
        return self::parseAdsetItem($adset_item, $account->currency);
    }

    private static function parseAdsetItem(Collection $adset_item, string $currency)
    {
        // "age_groups","gender","location_ids","operating_systems","placements","languages","network_types","promotion_type","placement_type"
        return [
            "adset_id" => $adset_item->get("adgroup_id"),
            "name" => $adset_item->get("adgroup_name"),
            "status" => AdvertisementUtil::getStatus($adset_item->get("secondary_status")),
            "daily_budget" => $adset_item->get("budget"),
            "bid" => $adset_item->get("bid_price"),
            "bid_strategy" => $adset_item->get("bid_type"),
            "pixel_id" => $adset_item->get("pixel_id"),
            "delivery_status" => $adset_item->get("delivery_mode"),
            "optimization_goal" => $adset_item->get("optimization_goal"),
            "server_campaign_id" => $adset_item->get("campaign_id"),
            "currency" => $currency,
            "start_time" => $adset_item->get("schedule_start_time"),
            "end_time" => $adset_item->get("schedule_end_time"),
            "server_created_at" => $adset_item->get("create_time"),
            "server_updated_at" => $adset_item->get("modify_time"),
            "age_groups" => json_encode($adset_item->get("age_groups")),
            "gender" => $adset_item->get("gender"),
            "locations" =>  json_encode($adset_item->get("location_ids")),
            "operating_systems" =>  json_encode($adset_item->get("operating_systems")),
            "placements" => json_encode($adset_item->get("placements")),
            "languages" =>  json_encode($adset_item->get("languages")),
            "network_types" =>  json_encode($adset_item->get("network_types")),
            "device_models" =>  json_encode($adset_item->get("device_model_ids")),
            "placement_type" =>  $adset_item->get("placement_type"),
            "promotion_type" => $adset_item->get("promotion_type"),
            "smart_targeting" => $adset_item->get("auto_targeting_enabled"),
            // $table->json('age_groups')->nullable();
            // $table->json('locations')->nullable();
            // $table->json('languages')->nullable();
            // $table->json('network_types')->nullable();
            // $table->json('operating_systems')->nullable();
            // $table->json('device_models')->nullable();
            // $table->json('placements')->nullable();
        ];
    }

    /**
     * ADSET SECTION END
     */


    /**
     * AD SECTION START
     */


    public static function updateAdStatus(Connection $connection)
    {
        try {
            $application = $connection->application;
            $token = $application->access_token;
            $ad_id = $connection->server_ad_id;
            $today = Carbon::today()->toDateString();
            $history_ad_item = HistoryAd::where("ad_id",  $ad_id)
                ->where("data_date", $today)->first();
            if (!$history_ad_item) {
                $history_ad_item = HistoryAd::where("ad_id",  $ad_id)
                    ->latest()->first();
            }
            $new_status = $history_ad_item->status == 'ACTIVE' ? "DISABLE" : "ENABLE";
            // $url = PlatformUrl::$TIKTOK_V3_BASE_URL . "ad/status/update";
            $url = PlatformUrl::$TIKTOK_V3_BASE_URL . "ad/status/update/";
            $header = ["Access-Token" => $token, "Content-Type" => "application/json"];
            $body = [
                "advertiser_id" => $connection->server_account_id,
                "operation_status" => $new_status,
                "ad_ids" => [$ad_id]
            ];

            $response = Http::withHeaders($header)->post($url, $body);
            $code = $response->json('code');
            if ($response->status() == Response::HTTP_UNAUTHORIZED) {
                AdvertisementUtil::expireApplication($application);
                return "token_expired";
            } else if ($code == 40105) {
                AdvertisementUtil::expireApplication($application);
                return "token_expired";
            } else if ($code == 40001) {
                AdvertisementUtil::expireApplication($application);
                return "token_expired";
            } else if ($code == 40002) {
                return $response->json('message');
            }
            $data = $response->json("data");
            $status = collect($data)->get("status");
            if ($status) {
                $new_status = $status == "ENABLE" ? "ACTIVE" : "INACTIVE";
                $history_ad_item->update([
                    "status" => $new_status
                ]);

                return $new_status  == "ENABLE" ? "ACTIVE" : "INACTIVE";
            }
            return "failed";
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
    public static function disableTiktokStatus(Connection $connection, $type)
    {
        try {
            $application = $connection->application;
            $token = $application->access_token;
            $target_id = '';
            switch ($type) {
                case 'ad':
                    $target_id =  $connection->server_ad_id;
                    break;
                case 'adgroup':
                    $target_id =  $connection->server_ad_adset_id;
                    break;
                case 'campaign':
                    $target_id =  $connection->server_campaign_id;
                    break;
            }
            $url = PlatformUrl::$TIKTOK_V3_BASE_URL . $type . "/status/update/";
            $header = ["Access-Token" => $token, "Content-Type" => "application/json"];
            $body = [
                "advertiser_id" => $connection->server_account_id,
                "operation_status" => "DISABLE",
                $type . "_ids" => [$target_id]
            ];

            $response = Http::withHeaders($header)->post($url, $body);
            $code = $response->json('code');
            if ($response->status() == Response::HTTP_UNAUTHORIZED) {
                AdvertisementUtil::expireApplication($application);
                return "token_expired";
            } else if ($code == 40105) {
                AdvertisementUtil::expireApplication($application);
                return "token_expired";
            } else if ($code == 40001) {
                AdvertisementUtil::expireApplication($application);
                return "token_expired";
            }
            $data = $response->json("data");
            $status = collect($data)->get("status");
            if ($status) {
                return "success";
            }
            return "failed";
        } catch (\Throwable $th) {
            return $th->getMessage();
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
        }
    }

    public static function createAdWithAdsetAndCampaign(Connection $connection, $ad_id, $extra_data = null)
    {

        try {

            $account = $connection->adAccount;
            $application = $connection->application;
            self::setDate($extra_data);
            $extra_data['start_date'] = self::$start_date;
            $ad_raw_data = self::fetchAdsWithMetrics($account, $application, $ad_id);
            $account_id = $ad_raw_data->get("advertiser_id");
            if ($account_id != $account->account_id) {
                throw new Exception("tiktok_invalid_id", 404);
            }
            $campaign_id = $ad_raw_data->get("campaign_id");
            $adset_id = $ad_raw_data->get("adgroup_id");

            $adset_attributes = self::fetchAdsetAttributes($application, $account,  $adset_id);
            $campaign_attributes = self::fetchCampaignAttributes($application, $account->account_id,  $campaign_id);
            $crm_item_raw_data = Teebalhoor::serverProducts($connection, $extra_data);

            // $ga_raw_data = GoogleAnalytics::fetchGoogleAnalytics("282328672", $connection->hashed_code, $extra_data);

            $carbon_now = Carbon::parse(self::$start_date);
            // $carbon_now = Carbon::now();
            $campaign_repo = new HistoryCampaignRepository();
            $campaign = $campaign_repo->createOrUpdateCampaign($campaign_attributes, $account, $carbon_now);
            $adset_repo = new HistoryAdsetRepository();
            $adset = $adset_repo->createOrUpdateAdset($adset_attributes, $campaign->id, $carbon_now);
            $ad_parsed_data = self::parseAdRawData($ad_raw_data, $crm_item_raw_data, $account->currency);
            $ad_parsed_data = $ad_parsed_data;

            $ad_repo = new HistoryAdRepository();
            $ad_instance = $ad_repo->createOrUpdateAd($ad_parsed_data, $adset->id, $carbon_now,  $connection->pcode, $extra_data, $connection->sales_type, 'tikrok_refresh_date');
            return $ad_instance;
        } catch (\Throwable $th) {

            if ($th->getCode() == 6000) {
                throw new Exception("tiktok_fetching_ads_error", 6000);
            } else if ($th->getCode() == 9000) {
                throw new Exception("teebalhoor_products_error", 6000);
            } else if ($th->getCode() == 404) {
                throw new Exception('tiktok_invalid_id', 404);
            }
            throw new Exception($th->getMessage(), 6001);
        }
    }

    private static function fetchAdsWithMetrics(AdAccount $account, Application $application, string $ad_id)
    {
        try {


            $ad_account_id = $account->account_id;
            $timezone = $account->timezone_name;
            $token = $application->access_token;
            $header = ["Access-Token" => $token, "Content-Type" => "application/json"];
            $fields = TikTokQuery::$AD_FIELDS;
            $params = "?advertiser_id=$ad_account_id&fields=$fields";
            $url = PlatformUrl::$TIKTOK_V3_BASE_URL . "ad/get/$params";
            $url = $url . '&filtering={"ad_ids": ["' . $ad_id . '"]}';
            $response = Http::withHeaders($header)->get($url);
            $code = $response->json('code');
            if ($response->status() == Response::HTTP_UNAUTHORIZED) {
                AdvertisementUtil::expireApplication($application);
            } else if ($code == 40105) {
                AdvertisementUtil::expireApplication($application);
            } else if ($code == 40001) {
                AdvertisementUtil::expireApplication($application);
            }
            $data = $response->json("data");
            $collection = collect($data)->get("list");
            $ad_item_collection = collect($collection)->first();
            if ($ad_item_collection) {
                $filter = json_encode(self::adRequestFilterBody($timezone, $ad_account_id, $ad_id));
                $url = PlatformUrl::$TIKTOK_V2_BASE_URL . "reports/integrated/get/";
                $response = Http::withHeaders($header)->withBody($filter, "application/json")->get($url);
                $code = $response->json('code');
                if ($response->status() == Response::HTTP_UNAUTHORIZED) {
                    AdvertisementUtil::expireApplication($application);
                } else if ($code == 40105) {
                    AdvertisementUtil::expireApplication($application);
                } else if ($code == 40001) {
                    AdvertisementUtil::expireApplication($application);
                }
                $data = $response->json("data");
                $metric_collection = collect($data)->get("list");
                $metric_collection = collect($metric_collection)->first();
                if ($metric_collection) {
                    $result = array_merge($ad_item_collection, $metric_collection["metrics"]);
                    return collect($result);
                }
                return collect($ad_item_collection);
            }
        } catch (\Throwable $th) {
            //throw $th;
            throw new Exception("item_not_found", 404);
        }
    }


    private static function adRequestFilterBody(string $timezone,  string $account_id, string $ad_id)
    {
        $today = Carbon::today()->setTimezone($timezone)->toDateString();

        return  [
            "advertiser_id" => $account_id,
            "service_type" => "AUCTION",
            "report_type" => "BASIC",
            "data_level" => "AUCTION_AD",
            "dimensions" => [
                "ad_id",
                "stat_time_day"
            ],
            "metrics" => TikTokQuery::$AD_METRICS,
            "start_date" => self::$start_date,
            "end_date" => self::$start_date,
            // "start_date" => $today,
            // "end_date" => $today,
            "filters" => [
                [
                    "field_name" => "ad_ids",
                    "filter_type" => "IN",
                    "filter_value" => "[$ad_id]"
                ]
            ],
            "lifetime" => false,
            "page" => 1,
            "page_size" => 200
        ];
    }


    private static function parseAdRawData(Collection $ad_item_data, Collection $crm_data, $currency)
    {
        $deliverd = (int) $crm_data->get("Deliverd");
        $logis_cancelled = (int) $crm_data->get("LogCanceld");
        $total_picked_up = (int) $crm_data->get("Pickedup");
        $picked_up = $total_picked_up - ($logis_cancelled + $deliverd);
        $new =
            [
                "ad_name" => $ad_item_data->get("ad_name"),
                "ad_id" => $ad_item_data->get("ad_id"),
                "server_adset_id" => $ad_item_data->get("adgroup_id"),
                "server_account_id" => $ad_item_data->get("advertiser_id"),
                "currency" => $currency,
                "status" => AdvertisementUtil::getStatus($ad_item_data->get("secondary_status")),
                "impressions" => $ad_item_data->get("impressions"),
                "spend" => $ad_item_data->get("spend"),
                "frequency" => $ad_item_data->get("frequency"),
                "reach" => $ad_item_data->get("reach"),
                "result" => $ad_item_data->get("result"),
                "clicks" => $ad_item_data->get("clicks"),
                "two_second_video_views" => $ad_item_data->get("video_watched_2s"),
                "six_second_video_views" => $ad_item_data->get("video_watched_6s"),
                "video_views" => $ad_item_data->get("video_play_actions"),
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
                "server_created_at" => $ad_item_data->get("create_time"),
                "server_updated_at" => $ad_item_data->get("modify_time"),
            ];

        return $new;
    }


    /**
     * Get Access Token and Refresh Token & Create Application
     */

    public static function getTokenAndStore(array $application_data, $application_id = null)
    {
        $body = [
            "app_id" => $application_data["client_id"],
            "secret" => $application_data["client_secret"],
            "auth_code" => $application_data["code"],
        ];
        $url = PlatformUrl::$TIKTOK_ACCOUNT_V3_BASE_URL . "access_token/";
        $response = Http::post($url, $body);
        if ($response->successful()) {
            $data =  $response->json("data");
            if ($data) {
                $access_token =  $data["access_token"];
                if ($application_id) {
                    $update = [
                        "access_token" => $access_token,
                        "refresh_token" => $access_token,
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
                    "redirect_url" => self::redirectUrl($application_data["client_id"], $application_data["rid"]),
                    "client_id" => $application_data["client_id"],
                    "client_secret" => $application_data["client_secret"],
                    "access_token" => $access_token,
                    "refresh_token" => $access_token,
                    "expires_in" => 0,
                    "created_by" => auth()->id(),
                    "updated_by" => auth()->id(),
                    "country_id" => $application_data["country_id"],
                ];
                $check_application = Application::where([
                    ["client_id", "=", $attributes["client_id"]],
                    ["client_secret", "=", $attributes["client_secret"]],
                ])->first();
                if ($check_application) return false;
                $app = Application::create($attributes);
                $app->users()->sync($application_data['users']);
                return true;
            }
        }
        return false;
    }

    private static function redirectUrl(string $client_id, string $rid)
    {
        return "https://ads.tiktok.com/marketing_api/auth?app_id=$client_id&state=your_custom_params&redirect_uri="
            . PlatformUrl::$TIKTOK_REDIRECT_URL . "&rid=$rid";
    }

    public static function updateBidBudget($request)
    {
        // 10.64
        // 21   1747018961694721
        try {
            $connection = connection::where('server_ad_adset_id', $request->adset_id)->first();
            $application = $connection->application;
            $token = $application->access_token;
            $adset_id = $connection->server_ad_adset_id;
            $today = Carbon::today()->toDateString();
            $history_adset = HistoryAdset::where("adset_id",  $adset_id)
                ->where("data_date", $today)->first();
            $header = ["Access-Token" => $token, "Content-Type" => "application/json"];
            if ($request->type == 'bid') {
                $url = PlatformUrl::$TIKTOK_V3_BASE_URL . "adgroup/update";
                $body = [
                    "adgroup_id" => $adset_id,
                    'advertiser_id' => $connection->server_account_id,
                    'bid_type' => 'BID_TYPE_CUSTOM',
                    // 'bid_type' => 'BID_TYPE_NO_BID',
                    'bid_price' => $request->value,
                ];
            } else {

                $url = PlatformUrl::$TIKTOK_V3_BASE_URL . "adgroup/budget/update";
                $body = [
                    "advertiser_id" => $connection->server_account_id,
                    "budget" => [["budget" => $request->value, "adgroup_id" => $adset_id]],
                    "scheduled_budget" => [['scheduled_budget' => 23, "adgroup_id" => $adset_id]]
                ];
            }

            $response = Http::withHeaders($header)->post($url, $body);
            $code = $response->json('code');
            if ($response->status() == Response::HTTP_UNAUTHORIZED) {
                AdvertisementUtil::expireApplication($application);
                return "token_expired";
            } else if ($code == 40105) {
                AdvertisementUtil::expireApplication($application);
                return response()->json('token_expired', 500);
            } else if ($code == 40001) {
                AdvertisementUtil::expireApplication($application);
                return response()->json('token_expired', 500);
                return "token_expired";
            } else if ($code == 40002) {
                return response()->json($response->json('message'), 500);
            }
            if ($request->type == 'bid') {
                $data = $response->json("data");
                $bid = collect($data)->get("bid_price");
                if ($bid) {

                    $history_adset->update([
                        "bid" => $bid
                    ]);
                }
                $history_adset->changesHistory()->create(["value" => $request->value, "column_name" => $request->type, "user_id" => Auth::user()->id]);
                return response()->json($history_adset);
            } else {
                if ($response->json("message") == 'OK')
                    $history_adset->update([
                        "daily_budget" => $request->value
                    ]);
                // adset_id
                // :
                // "54be19bd-bf53-4dd4-86e2-1b432fe2bd55"
                // platform
                // :
                // "snapchat"
                // type
                // :
                // "budget"
                // value
                // :
                // "50"

                $history_adset->changesHistory()->create(["value" => $request->value, "column_name" => $request->type, "user_id" => Auth::user()->id]);
                return response()->json($history_adset);
            }
            return response()->json('something went wrong', 404);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }



    public static function getLocationInfo($connection, $adset_id, $adset)
    {
        # code... $account_id = $account->account_id;
        $account = $connection->adAccount;
        $objective_type = $adset->campaign->objective_type;
        $application = $connection->application;
        $token = $application->access_token;
        $header = ["Access-Token" => $token, "Content-Type" => "application/json"];
        $params = '?advertiser_id=' . $account->account_id . '&placements=' . json_encode($adset->placements) . '&objective_type=' . $objective_type;
        $url = PlatformUrl::$TIKTOK_V3_BASE_URL . "tool/region/$params";
        $url = $url . '&filtering={"adgroup_ids":["' . $adset_id . '"]}';
        $response = Http::withHeaders($header)->get($url);
        $code = $response->json('code');
        if ($response->status() == Response::HTTP_UNAUTHORIZED) {
            AdvertisementUtil::expireApplication($application);
        } else if ($code == 40105) {
            AdvertisementUtil::expireApplication($application);
        } else if ($code == 40001) {
            AdvertisementUtil::expireApplication($application);
        }
        $body = $response->json("data");
        $region_info = collect($body['region_info']);
        $region_info = $region_info;
        return $region_info->whereIn('location_id', $adset->locations);
    }
}
