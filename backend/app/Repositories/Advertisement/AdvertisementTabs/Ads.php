<?php

namespace App\Repositories\Advertisement\AdvertisementTabs;

use App\Jobs\SwitchByPlatformName;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use App\Models\Advertisement\HistoryAd;
use App\Models\Advertisement\Connection;
use App\Models\Advertisement\DisabledAd;
use Illuminate\Database\Eloquent\Builder;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use App\Repositories\Advertisement\Platforms\TikTok;
use App\Repositories\Advertisement\AdvertisementUtil;
use App\Repositories\Advertisement\Platforms\Snapchat;
use App\Repositories\Advertisement\AdvertisementTabs\Adset;
use App\Repositories\Advertisement\AdvertisementTabs\Campaign;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Stmt\Return_;

class Ads extends Repository
{

    /**
     * Fetch Ads Data for ads Tab
     */
    public function fetchAdItems(Request $request, $getCount = false)
    {
        try {
            $relations = [
                "connection:id,code,server_ad_id,pcode,generated_link,landing_page_link,media_link", 'productProfileInfo:item_code,prod_max_adver_cost,prod_image',
                "labels" => function ($subquery) {
                    $subquery->whereCurrentStatus('active')->where('sub_system', 'advertisement');
                }, "platform:platforms.id,platforms.platform_name"
            ];
            $queryBuilder               = new UriQueryBuilder(new HistoryAd(), $request);
            $queryBuilder->itemsPerPage = 20;
            $queryBuilder->setRelations($relations);
            $query = $queryBuilder->query
                ->whereHas("adset", fn ($adset_query) => Adset::filterAdsetQuery($adset_query, $request))
                ->whereHas("campaignThroughConnection", fn ($cam_query) => Campaign::filterCampaignQuery($cam_query, $request))
                ->groupBy("ad_id");
            $query = $this->filterMaxAdvCost($query, $request);
            $query = self::adsCalculationSubQueries($query, $request->start_date, $request->end_date);
            if ($request->status) {
                $query = $query->where("status", $request->status);
            }
            $searchInColumns = [
                'ad_name', 'ad_id', "code", 'server_adset_id', 'server_account_id', 'status',
                'delivery_status', 'result', "impressions", "viewable_impressions", "spend", "clicks",
                "currency", 'whereHasOne,connection,pcode', "objective", "optimization_goal",
                "reach", 'frequency',
            ];
            $query = $this->searchContent($queryBuilder->query, $searchInColumns, $request->input('searchContent'));
            $query = self::filterQuery($query, $request);

            if ($getCount) {
                $total = $query->get();
                return [
                    'total' => $total->count(),
                    "active" => $total->where('status', "ACTIVE")->count(),
                    "inactive" => $total->where('status', "!=", "ACTIVE")->count()
                ];
            }
            $total = $query->get()->count();
            $query = $query->selectRaw('COUNT(CASE WHEN status="INACTIVE" THEN 1 ELSE NULL END) AS inactive_ads, COUNT(CASE WHEN status="ACTIVE" THEN 1 ELSE NULL END) AS active_ads');

            $query = $query->withCount('remarks')->withCount('adset as adsets')->with('connection_date', function ($query) {
                return $query->select(['server_ad_id', 'created_at'])->oldest();
            });
            if ($request->query->has("search_by_id")) {
                $data = $this->searchCode($query, $request, $relations, false, "code", false, false);
                return $getCount ?
                    $total : response()->json(
                        ["result" => true, "total" => $total, "item" => $data]
                    );
            }

            $queryBuilder->query = $query;
            $total               = $query->get()->count();
            $paginate            = $queryBuilder->build()->paginate()->getData();
            $items               = $paginate->data;
            // if (!$request->sortBy)
            //     $items = Collect($items)->sortBy('connection_date.created_at')->values()->all();
            return $getCount ?
                $total : response()->json(
                    ["result" => true, "total" => $total, "items" => $items]
                );
        } catch (\Throwable $th) {
            return response()->json(["result" => false, "code" => $th->getMessage()]);
        }
    }

    public function fetchSalesTypeITems(Request $request, $getCount = false)
    {
        try {
            $relations = [
                "labels" => function ($subquery) {
                    $subquery->whereCurrentStatus('active')->where('sub_system', 'advertisement');
                }
            ];
            $queryBuilder               = new UriQueryBuilder(new HistoryAd(), $request);
            $queryBuilder->itemsPerPage = 20;
            $queryBuilder->setRelations($relations);
            $query = $queryBuilder->query
                // ->whereHas("adset", fn ($adset_query) => Adset::filterAdsetQuery($adset_query, $request))
                ->whereHas("campaignThroughConnection", fn ($cam_query) => Campaign::filterCampaignQuery($cam_query, $request))
                ->groupBy("sales_type");

            $query = $this->filterMaxAdvCost($query, $request);
            $query = self::adsCalculationSubQueries($query, $request->start_date, $request->end_date);
            if ($request->status) {
                $query = $query->where("status", $request->status);
            }
            $searchInColumns = [
                'ad_name', 'ad_id', "code", 'server_adset_id', 'server_account_id', 'status',
                'delivery_status', 'result', "impressions", "viewable_impressions", "spend", "clicks",
                "currency", 'whereHasOne,connection,pcode', "objective", "optimization_goal",
                "reach", 'frequency',
            ];
            if ($request->input('searchContent'))
                $query = $this->searchContent($queryBuilder->query, $searchInColumns, $request->input('searchContent'));
            $query = self::filterQuery($query, $request, 'sales_type');

            if ($getCount) {
                $total = $query->get();
                return [
                    'total' => $total->count(),
                    "active" => $total->where('status', "ACTIVE")->count(),
                    "inactive" => $total->where('status', "!=", "ACTIVE")->count()
                ];
            }
            $total = $query->get()->count();
            $query = $query->selectRaw(
                'COUNT(DISTINCT CASE WHEN status="INACTIVE" THEN ad_id ELSE 0 END) AS inactive_ads,
                COUNT(DISTINCT  CASE WHEN status="ACTIVE" THEN ad_id ELSE 0 END) AS active_ads,

                COUNT(DISTINCT server_account_id) AS total_accounts,
                COUNT(DISTINCT ad_id) AS total_ads,
                COUNT(DISTINCT server_adset_id) AS total_adsets
                '
            );

            $query = $query->withCount([
                'adset as active_adset'
            ]);
            // $query = $query->withCount([
            //     'adset as inactive_adset'
            //     => (fn ($q) => $q->where('status', '!=', 'ACTIVE'))
            // ]);
            $query = $query->withCount([
                'campaigns as total_campaigns'
            ]);

            HistoryAd::setcustomPrimaryKey('sales_type');
            $query = $query->withCount('remarks')->with('connection_date', function ($query) {
                return $query->select(['server_ad_id', 'created_at'])->oldest();
            });
            if ($request->query->has("search_by_id")) {
                $data = $this->searchCode($query, $request, $relations, false, "code", false, false);
                return $getCount ?
                    $total : response()->json(
                        ["result" => true, "total" => $total, "item" => $data]
                    );
            }

            $queryBuilder->query = $query;

            $total               = $query->get()->count();
            $paginate            = $queryBuilder->build()->paginate()->getData();
            $items               = $paginate->data;
            // if (!$request->sortBy)
            //     $items = Collect($items)->sortBy('connection_date.created_at')->values()->all();

            return $getCount ?
                $total : response()->json(
                    ["result" => true, "total" => $total, "items" => $items]
                );
        } catch (\Throwable $th) {
            return response()->json(["result" => false, "code" => $th->getMessage()]);
        }
    }
    public function filterMaxAdvCost($query, $request)
    {
        return $request->prod_max_adver_cost ?
            $query->whereHas("connection", function ($query) use ($request) {
                return  $query->whereHas("productProfileInfo", function ($query2) use ($request) {
                    // return $query2->where('prod_max_adver_cost5', 100);
                    return AdvertisementUtil::getConditionWhere($request->prod_max_adver_cost, $query2, 'prod_max_adver_cost');
                });
            }) : $query;
    }
    public static function filterQuery(Builder $query, Request $request, $id = 'code')
    {
        $query = AdvertisementUtil::filterByQueryIds($query, $request, $id);
        $query = AdvertisementUtil::filterConnectionParams($query, $request, "connection");
        $query = AdvertisementUtil::filterQueryParams($query, $request);
        return $query;
    }

    public static function adsCalculationSubQueries($query, $start_date = null, $end_date = null, $hide_columns = false)
    {
        $start_date             = Carbon::parse($start_date)->toDateString();
        $end_date               = Carbon::parse($end_date)->toDateString();
        $none_countable_columns = $hide_columns ? ["server_adset_id", "currency", "ad_pcode"] : HistoryAd::nonCountableColumns();
        $query                  = $query->select($none_countable_columns)->whereBetween("history_ads.data_date", [$start_date, $end_date]);
        $columns                = HistoryAd::getCountableRawColumns();
        foreach ($columns as $column) {
            $query = $query->selectRaw("SUM($column) as $column");
            if ($column == 'spend') {
                $round = AdvertisementUtil::$ROUND;
                $query = $query->selectRaw("round( (sum(spend) / sum(crm_total_orders)), $round)  AS cpo");
                // $query = $query->selectRaw("round( (crm_total_orders / crm_confirm), $round)  AS avg_sales_per_confirm");
                // $query = $query->selectRaw("round( (crm_total_orders * exact_price), $round)  AS total_register_sales");
            }
        }

        $query = self::extraCalculation($query);
        return $query;
    }



    public static function getAdsCalculations($query, $request, $group_by, $relations = [])
    {
        $relation = ["adThroughConnection" => function ($ads_query) use ($request, $group_by) {
            $ads_query = self::adsCalculationSubQueries($ads_query, $request->start_date, $request->end_date, true)
                ->groupBy($group_by);
            $ads_query = AdvertisementUtil::filterQueryParams($ads_query, $request)
                ->whereHas("adset", fn ($adset_query) => Adset::filterAdsetQuery($adset_query, $request))
                ->whereHas("campaignThroughConnection", fn ($cam_query) => Campaign::filterCampaignQuery($cam_query, $request));
            return AdvertisementUtil::filterByConColumns($ads_query, $request);
        }];
        $relation   = array_merge($relation, $relations);
        $start_date = Carbon::parse($request->start_date)->toDateString();
        $end_date   = Carbon::parse($request->end_date)->toDateString();
        return $query->with($relation)->whereHas("adThroughConnection", fn ($ads_query) => $ads_query->whereBetween("history_ads.data_date", [$start_date, $end_date]));
    }

    public static function getObjectTotals($query, $removed_columns = [], $request)
    {
        $total_columns = [
            "total_companies"     => "connections.company_id",
            "total_products"      => "connections.pcode",
            "total_platforms"     => "connections.platform_id",
            "total_organizations" => "connections.application_id",
            "total_accounts"      => "connections.ad_account_id",
            "total_campaigns"     => "connections.server_campaign_id",
            "total_adsets"        => "connections.server_ad_adset_id",
            "total_ads"           => "connections.server_ad_id",
        ];
        $total_columns = array_diff_key($total_columns, array_flip($removed_columns));
        foreach ($total_columns as $key => $value) {
            $query = $query->withCount(["adThroughConnection as $key" => function ($count_query) use ($value, $request) {
                $count_query = $count_query->select([DB::raw("count(distinct($value))")]);
                $count_query = AdvertisementUtil::filterByConColumns($count_query, $request);
                return $count_query;
            }]);
        }
        return $query;
    }

    public static function makeAdsArrayAsObject($items)
    {
        $result_items = [];
        foreach ($items as $item) {
            if (count($item->ad_through_connection) > 0) {
                $ad_item = $item->ad_through_connection[0];
                unset($ad_item->ad_id);
                unset($ad_item->server_adset_id);
                unset($ad_item->laravel_through_key);
                $item->ad_through_connection = $ad_item;
                $result_items[]              = $item;
            }
        }
        return $result_items;
    }

    public static function extraCalculation($query)
    {
        $round = AdvertisementUtil::$ROUND;
        // $query = $query->selectRaw("round( (sum(spend) / sum(crm_total_orders)), $round)  AS cpo");
        $query = $query->selectRaw("round( (sum(spend) / sum(impressions)) * 1000, $round)  AS cpm");
        $query = $query->selectRaw("round( (sum(clicks) / sum(impressions)) * 100, $round)  AS ctr");
        $query = $query->selectRaw("round( (sum(spend) / sum(clicks)) , $round)  AS cpc");
        $query = $query->selectRaw("round( (sum(crm_total_orders) / sum(clicks)) * 100, $round)  AS bp");
        $query = $query->selectRaw("round( (sum(clicks) / sum(six_second_video_views)) * 100, $round)  AS aap");
        $query = $query->selectRaw("round( (sum(six_second_video_views) / sum(two_second_video_views)) * 100, $round)  AS vqp");
        $query = $query->selectRaw("round( (sum(spend) / sum(result)) * 100, $round)  AS cpp");
        $query = $query->selectRaw("round( (sum(story_opens) / sum(impressions)) * 100, $round)  AS story_open_rate");
        $query = $query->selectRaw("round( (sum(spend) / sum(story_opens)) * 100, $round)  AS cost_per_story_open");
        $query = $query->selectRaw("round( (sum(crm_confirm) / sum(crm_total_orders)) * 100, $round)  AS crm_confirmed_percentage");
        $query = $query->selectRaw("round( (sum(crm_total_sale) - (sum(spend) + sum(crm_product_cost) + sum(crm_delivery_cost))), $round)  AS crm_profit_lose");
        $query = $query->selectRaw("round( (sum(crm_cancelled) / sum(crm_total_orders)) * 100, $round)  AS crm_cancelled_percentage");
        $query = $query->selectRaw("round( (sum(crm_repeated) / sum(crm_total_orders)) * 100, $round)  AS crm_send_back_percentage");
        $query = $query->selectRaw("round( (sum(crm_confirm) / sum(crm_total_pickedup)) * 100, $round)  AS crm_difference");
        $query = $query->selectRaw("round( (sum(crm_logis_deliverd) / sum(crm_total_pickedup)) * 100, $round)  AS crm_delivered_percentage");
        $query = $query->selectRaw("round( (sum(crm_logis_cancelled) / sum(crm_total_pickedup)) * 100, $round)  AS crm_logis_cancelled_percentage");
        $query = $query->selectRaw("round( (sum(crm_logis_deliverd) / sum(crm_total_orders)) * 100, $round)  AS crm_final_percentage");
        $query = $query->selectRaw("round( (sum(spend) + sum(crm_product_cost) + sum(crm_delivery_cost)), $round)  AS crm_total_expense");
        $query = $query->selectRaw("round( ((sum(crm_total_sale) + ( sum(spend) + sum(crm_product_cost) + sum(crm_delivery_cost))) / sum(crm_total_sale)) * 100, $round)  AS crm_profit_percentage");
        $query = $query->selectRaw("round( (sum(crm_product_cost) / sum(crm_total_sale)) * 100, $round)  AS crm_product_cost_percentage");
        $query = $query->selectRaw("round( (sum(crm_delivery_cost) / sum(crm_total_sale)) * 100, $round)  AS crm_delivery_cost_percentage");
        $query = $query->selectRaw("round( (sum(spend) / sum(crm_total_sale)) * 100, $round)  AS crm_ad_cost_percentage");
        $query = $query->selectRaw("round( ((sum(crm_confirm)+ sum(crm_cancelled)) / sum(crm_total_orders)) * 100, $round)  AS crm_percentage");
        $query = $query->selectRaw("round( (sum(ga_engaged_sessions) / sum(ga_sessions)) * 100, $round)  AS ga_engagement_rate_percentage");
        $query = $query->selectRaw("round( ((sum(crm_logis_deliverd) + sum(crm_logis_cancelled)) / sum(crm_total_pickedup)) * 100, $round)  AS crm_logistics_percentage");
        $query = $query->selectRaw("round( (sum(crm_total_orders)/ sum(crm_confirm)) , $round)  AS avg_sales_per_confirm");
        $query = $query->selectRaw("round( (sum(exact_price) * sum(crm_total_orders)) , $round)  AS total_register_sales");
        $query = $query->selectRaw("round( (sum(crm_total_sale)/ sum(crm_total_orders)) , $round)  AS avg_sales_order");
        return $query;
    }


    public static function updateAdStatus($ad_id)
    {

        try {
            $relation = ["application", "platform", "adAccount", "historyAd", "company", "country"];


            $connection = Connection::with($relation)->where("server_ad_id", $ad_id)->first();
            if ($connection) {
                $result = '';
                $platform_name = $connection->platform->platform_name;
                switch ($platform_name) {
                    case 'snapchat':
                        $result = Snapchat::updateAdStatus($connection);
                        break;
                    case 'tiktok':
                        $result = TikTok::updateAdStatus($connection);
                        break;
                    default:
                        return "change_status_not_available";
                }
                if ($result == 'INACTIVE') {

                    $extra_data  = [
                        "total" => 1,
                        "item_index" => 0,
                        'sleep' => 120,
                    ];

                    DisabledAd::create(['ad_id' => $ad_id, 'user_id' => Auth::user()->id]);
                    // SwitchByPlatformName::dispatch($connection, $extra_data);
                    SwitchByPlatformName::dispatch($connection, $extra_data)->delay(now()->addMinutes(10));

                    // $item = Snapchat::createAdWithAdsetAndCampaign($connection, $ad_id);
                }
                if ($result == 'ACTIVE') {

                    $relation = ["application", "platform", "adAccount", "historyAd", "company", "country"];
                    $connections = Connection::with($relation)
                        ->whereHas("application", function ($query) {
                            return $query->where("system_status", "ACTIVE")
                                ->whereNotNull("access_token")->whereNotNull("refresh_token");
                        })->whereHas("platform", function ($query) {
                            return $query->where("system_status", "ACTIVE");
                        })->where("server_ad_id", $ad_id)->get();

                    $extra_data  = [
                        "total" => $connections->count(),
                    ];

                    foreach ($connections as $index => $connection) {
                        $extra_data["item_index"] = $index;
                        SwitchByPlatformName::dispatch($connection, $extra_data);
                    }
                }

                return $result == 'INACTIVE' || $result == 'ACTIVE' ? "success" : $result;
            }
            return "record_not_found";
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
}
