<?php

namespace App\Repositories\AdvetisementUpload\AdvertisementTabs;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use App\Models\AdvetisementUpload\ConnectionMU;
use App\Models\AdvetisementUpload\HistoryAdMU;
use Illuminate\Database\Eloquent\Builder;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use App\Repositories\Advertisement\Platforms\TikTok;
use App\Repositories\Advertisement\Platforms\Snapchat;
use App\Repositories\AdvetisementUpload\AdvertisementUtilMU;

class AdsMU extends Repository
{

    /**
     * Fetch Ads Data for ads Tab
     */
    public function fetchAdItems(Request $request, $getCount = false)
    {
        try {
            $relations = [
                "connection:id,server_ad_id,pcode,generated_link,landing_page_link", "labels",
                "platform:platforms.id,platforms.platform_name"

            ];
            $queryBuilder               = new UriQueryBuilder(new HistoryAdMU(), $request);
            $queryBuilder->itemsPerPage = 20;
            $queryBuilder->setRelations($relations);
            $query = $queryBuilder->query
                ->whereHas("adset", fn ($adset_query) => AdsetMU::filterAdsetQuery($adset_query, $request))
                ->whereHas("campaignThroughConnectionUpload", fn ($cam_query) => CampaignMU::filterCampaignQuery($cam_query, $request))
                ->groupBy("ad_id");
            $query = self::adsCalculationSubQueries($query, $request->start_date, $request->end_date);
            if ($request->status) {
                $query = $query->where("status", $request->status);
            }
            $total = $query->get()->count();
            $query = $query->withCount('remarks');
            $query = self::filterQuery($query, $request);
            if ($request->query->has("search_by_id")) {
                $data = $this->searchCode($query, $request, $relations, false, "code", false, false);
                return $getCount ?
                    $total : response()->json(
                        ["result" => true, "total" => $total, "item" => $data]
                    );
            }
            $searchInColumns = [
                'ad_name', 'ad_id', "code", 'server_adset_id', 'server_account_id', 'status',
                'delivery_status', 'result', "impressions", "viewable_impressions", "spend", "clicks",
                "currency", 'whereHasOne,connection,pcode', "objective", "optimization_goal",
                "reach", 'frequency',
            ];
            $query = $this->searchContent($queryBuilder->query, $searchInColumns, $request->input('searchContent'));

            $queryBuilder->query = $query;
            $total               = $query->get()->count();
            $paginate            = $queryBuilder->build()->paginate()->getData();
            $items               = $paginate->data;
            return $getCount ?
                $total : response()->json(
                    ["result" => true, "total" => $total, "items" => $items]
                );
        } catch (\Throwable $th) {
            return response()->json(["result" => false, "code" => $th->getMessage()]);
        }
    }

    public static function filterQuery(Builder $query, Request $request)
    {
        $query = AdvertisementUtilMU::filterByQueryIds($query, $request, "code");
        $query = AdvertisementUtilMU::filterConnectionParams($query, $request, "connection");
        $query = AdvertisementUtilMU::filterQueryParams($query, $request);
        return $query;
    }

    public static function adsCalculationSubQueries($query, $start_date = null, $end_date = null, $hide_columns = false)
    {
        $start_date             = Carbon::parse($start_date)->toDateString();
        $end_date               = Carbon::parse($end_date)->toDateString();
        $none_countable_columns = $hide_columns ? ["ad_id", "server_adset_id", "currency"] : HistoryAdMU::nonCountableColumns();
        $query                  = $query->select($none_countable_columns)->whereBetween("data_date", [$start_date, $end_date]);
        $columns                = HistoryAdMU::getCountableRawColumns();
        foreach ($columns as $column) {
            $query = $query->selectRaw("SUM($column) as $column");
        }

        $query = self::extraCalculation($query);
        return $query;
    }

    public static function getAdsCalculations($query, $request, $group_by, $relations = [])
    {
        $relation = ["adThroughConnectionUpload" => function ($ads_query) use ($request, $group_by) {
            $ads_query = self::adsCalculationSubQueries($ads_query, $request->start_date, $request->end_date, true)
                ->groupBy($group_by);
            $ads_query = AdvertisementUtilMU::filterQueryParams($ads_query, $request)
                ->whereHas("adset", fn ($adset_query) => AdsetMU::filterAdsetQuery($adset_query, $request))
                ->whereHas("campaignThroughConnectionUpload", fn ($cam_query) => CampaignMU::filterCampaignQuery($cam_query, $request));
            return AdvertisementUtilMU::filterByConColumns($ads_query, $request);
        }];
        $relation   = array_merge($relation, $relations);
        $start_date = Carbon::parse($request->start_date)->toDateString();
        $end_date   = Carbon::parse($request->end_date)->toDateString();
        return $query->with($relation)->whereHas("adThroughConnectionUpload", fn ($ads_query) => $ads_query->whereBetween("data_date", [$start_date, $end_date]));
    }

    public static function getObjectTotals($query, $removed_columns = [], $request)
    {
        $total_columns = [
            "total_companies"     => "manual_connections.company_id",
            "total_products"      => "manual_connections.pcode",
            "total_platforms"     => "manual_connections.platform_id",
            "total_organizations" => "manual_connections.application_id",
            "total_accounts"      => "manual_connections.ad_account_id",
            "total_campaigns"     => "manual_connections.server_campaign_id",
            "total_adsets"        => "manual_connections.server_ad_adset_id",
            "total_ads"           => "manual_connections.server_ad_id",
        ];
        $total_columns = array_diff_key($total_columns, array_flip($removed_columns));
        foreach ($total_columns as $key => $value) {
            $query = $query->withCount(["adThroughConnectionUpload as $key" => function ($count_query) use ($value, $request) {
                $count_query = $count_query->select([DB::raw("count(distinct($value))")]);
                $count_query = AdvertisementUtilMU::filterByConColumns($count_query, $request);
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

    public static function makeAdsArrayAsObjectUpload($items)
    {
        $result_items = [];
        foreach ($items as $item) {
            if (count($item->ad_through_connection_upload) > 0) {
                $ad_item = $item->ad_through_connection_upload[0];
                unset($ad_item->ad_id);
                unset($ad_item->server_adset_id);
                unset($ad_item->laravel_through_key);
                $item->ad_through_connection_upload = $ad_item;
                $result_items[]              = $item;
            }
        }
        return $result_items;
    }

    public static function extraCalculation($query)
    {
        $round = AdvertisementUtilMU::$ROUND;
        $query = $query->selectRaw("round( (sum(spend) / sum(crm_total_orders)), $round)  AS cpo");
        $query = $query->selectRaw("round( (sum(spend) / sum(impressions)) * 1000, $round)  AS cpm");
        $query = $query->selectRaw("round( (sum(clicks) / sum(impressions)) * 100, $round)  AS ctr");
        $query = $query->selectRaw("round( (sum(spend) / sum(clicks) , $round)  AS cpc");
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
        return $query;
    }


    public static function updateAdsetStatus($ad_id)
    {
        $relation = ["application", "platform", "adAccount"];
        $connection = ConnectionMU::with($relation)->where("server_ad_id", $ad_id)->first();
        if ($connection) {
            $platform_name = $connection->platform->platform_name;
            switch ($platform_name) {
                case 'snapchat':
                    return Snapchat::updateAdStatus($connection);
                case 'tiktok':
                    return TikTok::updateAdStatus($connection);
                default:
                    return "change_status_not_available";
            }
        }
        return "record_not_found";
    }
}
