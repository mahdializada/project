<?php

namespace App\Repositories\Advertisement;

use App\Http\Controllers\WhatsappMessageController;
use App\Jobs\SwitchByPlatformName;
use App\Models\Advertisement\Application;
use App\Models\Advertisement\Connection;
use App\Models\Advertisement\DisabledAd;
use App\Models\Advertisement\HistoryAd;
use App\Repositories\Advertisement\AdvertisementTabs\Ads;
use App\Repositories\Advertisement\AdvertisementTabs\Campaign;
use App\Repositories\Advertisement\Platforms\Facebook;
use App\Repositories\Advertisement\Platforms\GoogleAd;
use App\Repositories\Advertisement\Platforms\Snapchat;
use App\Repositories\Advertisement\Platforms\TikTok;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class AdvertisementUtil
{

    public static $ROUND = 2;

    public static function getValue(string $key, $array)
    {
        if (!is_array($array)) {
            return null;
        }

        if (array_key_exists($key, $array)) {
            return $array[$key];
        }
        return null;
    }

    public static function round($num, int $precision = 2)
    {
        if ($num) {
            return round($num, $precision);
        }
        return 0.00;
    }

    public static function roundByKey($key, $array, int $precision = 2)
    {
        $num = (float)self::getValue($key, $array);
        return round($num, $precision);
    }

    public static function percentage(int | float $value, int $precision = 2)
    {
        return round($value * 100, $precision);
    }
    public static function getStatusTest()
    {
        return self::getStatus("ENABLE");
    }
    public static function getStatus($status)
    {
        if ($status) {
            $statuses = [
                "ACTIVE"                          => "ACTIVE",
                "ENABLED"                         => "ACTIVE",
                "STATUS_ENABLE"                   => "ACTIVE",
                "STATUS_ENABLE"                   => "ACTIVE",
                "AD_STATUS_DELIVERY_OK"           => "ACTIVE",
                "AD_STATUS_CAMPAIGN_DISABLE"      => "INACTIVE",
                "ADGROUP_STATUS_DELIVERY_OK"      => "ACTIVE",
                "ADGROUP_STATUS_CAMPAIGN_DISABLE" => "INACTIVE",
                "CAMPAIGN_STATUS_DELIVERY_OK"     => "ACTIVE",
                "CAMPAIGN_STATUS_ENABLE"          => "ACTIVE",
                "CAMPAIGN_STATUS_DISABLE"         => "INACTIVE",
                "CAMPAIGN_STATUS_DISABLED"        => "INACTIVE",
                "AD_STATUS_ENABLE"                => "ACTIVE",
                "AD_STATUS_DISABLE"               => "INACTIVE",
                "AD_STATUS_DISABLED"              => "INACTIVE",
                "1"                               => "ACTIVE",
                "0"                               => "INACTIVE",
                "DISABLED"                        => "INACTIVE",
                "PAUSED"                          => "INACTIVE",
                "DELETED"                         => "DELETED",
                "ARCHIVED"                        => "ARCHIVED",
                "IN_PROCESS"                      => "IN_PROCESS",
                "WITH_ISSUES"                     => "WITH_ISSUES",
                "ENABLE"                          => "ACTIVE",
                "DISABLE"                         => "INACTIVE",
                "AD_STATUS_AUDIT"                 => "ACTIVE",
                "AD_STATUS_AUDIT_DENY"            => "INACTIVE",
                "AD_STATUS_ADGROUP_AUDIT_DENY"    => "INACTIVE",

            ];
            // return $status . "test";
            return array_key_exists($status, $statuses) ?
                $statuses[$status] : "ACTIVE";
        }
        return $status;
    }

    public static function getSchedulePath()
    {
        return storage_path("schedule/fetch_records.txt");
    }

    public static function randomCode($prefix)
    {
        return $prefix . rand(100000, 9999999999);
    }

    public static function fetchAndStoreToDayData()
    {
        try {
            $connections = ConnectionRepository::connectionAccount();
            $items       = [];
            foreach ($connections as $connection) {
                $platform_name = $connection->platform->platform_name;
                $server_ad_id  = $connection->historyAd->ad_id;
                switch ($platform_name) {
                    case 'facebook':
                        DB::beginTransaction();
                        $item    = Facebook::createAdWithAdsetAndCampaign($connection, $server_ad_id);
                        $items[] = $item;
                        DB::commit();
                        break;
                    case 'snapchat':
                        DB::beginTransaction();
                        $item    = Snapchat::createAdWithAdsetAndCampaign($connection, $server_ad_id);
                        $items[] = $item;
                        DB::commit();
                        break;
                    case 'tiktok':
                        DB::beginTransaction();
                        $item    = TikTok::createAdWithAdsetAndCampaign($connection, $server_ad_id);
                        $items[] = $item;
                        DB::commit();
                        break;
                    case 'google ads':
                        DB::beginTransaction();
                        $item    = GoogleAd::createAdWithAdsetAndCampaign($connection, $server_ad_id);
                        $items[] = $item;
                        DB::commit();
                        break;
                    default:
                        break;
                }
            }
            return $items;
        } catch (\Throwable $th) {
            DB::rollback();
            return null;
        }
    }

    public static function addPlatformRequestsIntoQueue()
    {
        try {

            // Artisan::call('queue:clear', [
            //     '--force' => true
            // ]);
            $connections = ConnectionRepository::connectionAccount();

            $items       = [];
            $extra_data  = [
                "total" => $connections->count(),
            ];

            foreach ($connections as $index => $connection) {
                $extra_data["item_index"] = $index;
                SwitchByPlatformName::dispatch($connection, $extra_data);
            }
            return $items;
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage(), 500);
        }
    }


    public static function addPlatformIntoQueue($request)
    {
        try {


            $connections = ConnectionRepository::connectionAccountByPlatform($request);
            if ($connections->count() == 0) {
                $today = Carbon::parse($request->dates);
                $request->dates = $today->addDay(-1)->toDateString();
                $connections = ConnectionRepository::connectionAccountByPlatform($request);
                $request->dates = $today->addDay(1)->toDateString();
            }



            $extra_data  = [
                "total" => $connections->count(),

            ];
            if ($request->platform)
                $extra_data['platform'] = $request->platform;
            if ($request->dates && !$request->frereshAll)
                $extra_data['dates'] = $request->dates;

            foreach ($connections as $index => $connection) {
                $extra_data["item_index"] = $index;
                SwitchByPlatformName::dispatch($connection, $extra_data);
            }
            return $connections->count();
        } catch (\Throwable $th) {

            throw new Exception($th->getMessage(), 500);
            return $th->getMessage();
        }
    }

    public static function microDollarToDollar($micro)
    {
        $micro = (float)$micro;
        return self::round($micro / 1000000);
    }
    public static function dollarToMicroDollar($dollar)
    {
        $dollar = (float)$dollar;
        return $dollar * 1000000;
    }
    public static function millisToSeconds($millis)
    {
        return $millis / 1000;
    }

    public static function countAndAdsCalculations(
        $query,
        Request
        $request,
        $group_by,
        $filter_id,
        $removed_columns = [],
        $relations = []
    ) {
        $query = Ads::getAdsCalculations($query, $request, $group_by, $relations);
        $query = Ads::getObjectTotals($query, $removed_columns, $request);
        $query = AdvertisementUtil::filterByQueryIds($query, $request, $filter_id);
        $query = AdvertisementUtil::filterConnectionParams($query, $request);
        return $query;
    }

    /**
     * Filter && Search Section
     */
    public static function filterConnectionParams($query, Request $request, $relation_name = "connections")
    {
        return $query->whereHas("$relation_name", function ($con) use ($request) {
            $type = $request->route("section");
            if ($request->country && $type != "country") {
                $con = $con->whereIn("country_id", $request->country);
            }

            if ($request->company && $type != "company") {
                $con = $con->whereIn("company_id", $request->company);
            }

            if ($request->item_code && $type != "item_code") {
                $item_codes = Connection::whereIn("id", $request->item_code)->select("pcode")->get()->toArray();
                $con        = $con->whereIn("pcode", $item_codes);
            }
            if ($request->sales_type && $type != "sales_type") {
                $sales_type = HistoryAd::whereIn("ad_id", $request->sales_type)->groupBy('sales_type')->select("sales_type")->get()->toArray();
                $con        = $con->whereIn("sales_type", $sales_type);
            }
            if ($request->platform && $type != "platform") {
                $con = $con->whereIn("platform_id", $request->platform);
            }

            if ($request->organization && $type != "organization") {
                $con = $con->whereIn("application_id", $request->organization);
            }

            if ($request->ad_account && $type != "ad_account") {
                $con = $con->whereIn("ad_account_id", $request->ad_account);
            }

            if ($request->campaign && $type != "campaign") {
                $con = $con->whereIn("server_campaign_id", $request->campaign);
            }

            if ($request->ad_set && $type != "ad_set") {
                $con = $con->whereIn("server_ad_adset_id", $request->ad_set);
            }

            if ($request->ad && $type != "ad") {
                $con = $con->whereIn("server_ad_id", $request->ad);
            }

            return $con;
        });
    }

    /**
     * Filter && Search Section
     */
    public static function filterByConColumns($query, Request $request)
    {
        $type = $request->route("section");

        if ($request->country && $type != "country") {
            $query = $query->whereIn("country_id", $request->country);
        }

        if ($request->company && $type != "company") {
            $query = $query->whereIn("company_id", $request->company);
        }

        if ($request->item_code && $type != "item_code") {
            $item_codes = Connection::whereIn("id", $request->item_code)->select("pcode")->get()->toArray();
            $query      = $query->whereIn("pcode", $item_codes);
        }
        if ($request->platform && $type != "platform") {
            $query = $query->whereIn("platform_id", $request->platform);
        }

        if ($request->organization && $type != "organization") {
            $query = $query->whereIn("application_id", $request->organization);
        }

        if ($request->ad_account && $type != "ad_account") {
            $query = $query->whereIn("ad_account_id", $request->ad_account);
        }

        if ($request->campaign && $type != "campaign") {
            $query = $query->whereIn("server_campaign_id", $request->campaign);
        }

        if ($request->ad_set && $type != "ad_set") {
            $query = $query->whereIn("server_ad_adset_id", $request->ad_set);
        }

        if ($request->ad && $type != "ad") {
            $query = $query->whereIn("server_ad_id", $request->ad);
        }

        return $query;
    }

    public static function filterQueryParams($query, Request $request)
    {
        $param_collections = collect($request->all());
        $query             = self::getCondition($param_collections->get("spend"), $query, "spend");
        $query             = self::getCondition($param_collections->get("impressions"), $query, "impressions");
        $query             = self::getCondition($param_collections->get("clicks"), $query, "clicks");
        $query             = self::getCondition($param_collections->get("video_views"), $query, "video_views");
        $query             = self::getCondition($param_collections->get("page_views"), $query, "total_page_views");
        $query             = self::getCondition($param_collections->get("orders"), $query, "crm_total_orders");
        $query             = self::getCondition($param_collections->get("crm_confirm"), $query, "crm_confirm");
        $query             = self::getCondition($param_collections->get("crm_cancelled"), $query, "crm_cancelled");
        $query             = self::getCondition($param_collections->get("crm_sendback"), $query, "crm_repeated");
        $query             = self::getCondition($param_collections->get("total_pickedup"), $query, "crm_total_pickedup");
        $query             = self::getCondition($param_collections->get("pickedup"), $query, "crm_pickedup");
        $query             = self::getCondition($param_collections->get("logis_delivered"), $query, "crm_logis_deliverd");
        $query             = self::getCondition($param_collections->get("logis_cancelled"), $query, "crm_logis_cancelled");
        $query             = self::getCondition($param_collections->get("product_cost"), $query, "crm_product_cost");
        $query             = self::getCondition($param_collections->get("delivery_cost"), $query, "crm_delivery_cost");
        $query             = self::getCondition($param_collections->get("total_sales"), $query, "crm_total_sale");
        $query             = self::getCondition($param_collections->get("users"), $query, "ga_total_users");
        $query             = self::getCondition($param_collections->get("new_users"), $query, "ga_new_users");
        $query             = self::getCondition($param_collections->get("ga_page_views"), $query, "ga_page_views");
        $query             = self::getCondition($param_collections->get("result"), $query, "result");
        $query             = self::getCondition($param_collections->get("story_opens"), $query, "story_opens");
        $query             = self::getCondition($param_collections->get("cpm"), $query, "cpm");
        $query             = self::getCondition($param_collections->get("cpp"), $query, "cpp");
        $query             = self::getCondition($param_collections->get("cpo"), $query, "cpo");
        $query             = self::getCondition($param_collections->get("ctr"), $query, "ctr");
        $query             = self::getCondition($param_collections->get("cpc"), $query, "cpc");
        $query             = self::getCondition($param_collections->get("bp"), $query, "bp");
        $query             = self::getCondition($param_collections->get("aap"), $query, "aap");
        $query             = self::getCondition($param_collections->get("vqp"), $query, "vqp");
        $query             = self::getCondition($param_collections->get("crm_confirmed_percentage"), $query, "crm_confirmed_percentage");
        $query             = self::getCondition($param_collections->get("crm_cancelled_percentage"), $query, "crm_cancelled_percentage");
        $query             = self::getCondition($param_collections->get("send_back_percentage"), $query, "send_back_percentage");
        $query             = self::getCondition($param_collections->get("crm_difference"), $query, "crm_difference");
        $query             = self::getCondition($param_collections->get("logis_delivered_percentage"), $query, "crm_delivered_percentage");
        $query             = self::getCondition($param_collections->get("logis_cancelled_percentage"), $query, "crm_logis_cancelled_percentage");
        $query             = self::getCondition($param_collections->get("final_percentage"), $query, "crm_final_percentage");
        $query             = self::getCondition($param_collections->get("total_expense"), $query, "crm_total_expense");
        $query             = self::getCondition($param_collections->get("profit_and_loss"), $query, "crm_profit_lose");
        $query             = self::getCondition($param_collections->get("profit_percentage"), $query, "crm_profit_percentage");
        $query             = self::getCondition($param_collections->get("product_cost_percentage"), $query, "crm_product_cost_percentage");
        $query             = self::getCondition($param_collections->get("delivery_cost_percentage"), $query, "crm_delivery_cost_percentage");
        $query             = self::getCondition($param_collections->get("ad_cost_percentage"), $query, "crm_ad_cost_percentage");
        $query             = self::getCondition($param_collections->get("crm_percentage"), $query, "crm_percentage");
        $query             = self::getCondition($param_collections->get("logistics_percentage"), $query, "crm_logistics_percentage");
        $query             = self::getCondition($param_collections->get("story_open_rate"), $query, "story_open_rate");
        $query             = self::getCondition($param_collections->get("cost_per_story_open"), $query, "cost_per_story_open");

        return $query;
    }

    public static function filterByQueryIds($query, Request $request, $column)
    {
        if ($request->ids && is_array($request->ids)) {
            if ($request->include == 0) {
                $query = $query->whereNotIn($column, $request->ids);
            }
            if ($request->include == 1) {
                $query = $query->whereIn($column, $request->ids);
            }
        }
        return $query;
    }

    public static function getCondition($param, $query, $column)
    {
        $param = collect($param);
        if ($param->isNotEmpty()) {
            $min = $param->get("min");
            $max = $param->get("max");
            switch ($param->get("condition")) {
                case "IS_GREATER_THAN":
                    if (is_null($min)) {
                        return $query;
                    }

                    return $query->having($column, ">=", (float)$min);
                case "IS_LESS_THAN":
                    if (is_null($min)) {
                        return $query;
                    }

                    return $query->having($column, "<=", (float)$min);
                case "IS_BETWEEN":
                    if (is_null($min) && is_null($max)) {
                        return $query;
                    }

                    return $query->havingBetween($column, [(float)$min, (float)$max]);
                default:
                    if (is_null($min)) {
                        return $query;
                    }

                    return $query->having($column, (float)$min);
            }
        }
        return $query;
    }
    public static function getConditionWhere($param, $query, $column)
    {
        $param = collect($param);
        if ($param->isNotEmpty()) {
            $min = $param->get("min");
            $max = $param->get("max");
            switch ($param->get("condition")) {
                case "IS_GREATER_THAN":
                    if (is_null($min)) {
                        return $query;
                    }

                    return $query->where($column, ">=", (float)$min);
                case "IS_LESS_THAN":
                    if (is_null($min)) {
                        return $query;
                    }

                    return $query->where($column, "<=", (float)$min);
                case "IS_BETWEEN":
                    if (is_null($min) && is_null($max)) {
                        return $query;
                    }

                    return $query->whereBetween($column, [(float)$min, (float)$max]);
                default:
                    if (is_null($min)) {
                        return $query;
                    }

                    return $query->where($column, (float)$min);
            }
        }
        return $query;
    }
    public static function getRawCondition($param, $query, $column)
    {

        $param = collect($param);
        if ($param->isNotEmpty()) {
            $min = $param->get("min");
            $max = $param->get("max");
            switch ($param->get("condition")) {
                case "IS_GREATER_THAN":
                    if (is_null($min)) {
                        return $query;
                    }

                    $min = (float)$min;
                    return $query->havingRaw("$column >= $min");
                case "IS_LESS_THAN":
                    if (is_null($min)) {
                        return $query;
                    }

                    $min = (float)$min;
                    return $query->havingRaw("$column <= $min");
                case "IS_BETWEEN":
                    if (is_null($min) && is_null($max)) {
                        return $query;
                    }

                    $min = (float)$min;
                    $max = (float)$max;
                    return $query->havingRaw("$column between $min and $max");
                default:
                    if (is_null($min)) {
                        return $query;
                    }

                    $min = (float)$min;
                    return $query->havingRaw("$column = $min");
            }
        }
        return $query;
    }

    public static function expireApplication(Application $application)
    {
        $application->system_status = "TOKEN_EXPIRED";
        $application->save();
        DB::commit();
        $application = $application->load('users');
        foreach ($application->users as $user) {
            WhatsappMessageController::sendMessage(
                $user->whatsapp,
                $user->firstname . ' ' . $user->lastname,
                $application->name
            );
        }
        return true;
    }

    public static function searchableColumns($newcolumns = [])
    {
        $defaultColumns = [
            // "currency",
            'result',
            'impressions',
            'video_views',
            'spend', 'clicks', "reach",
            "crm_total_orders", "crm_confirm", "crm_cancelled", "crm_repeated", "crm_pickedup",
            "crm_total_pickedup", "crm_logis_deliverd", "crm_logis_cancelled",
            "crm_total_sale", "crm_product_cost", "crm_delivery_cost", "ga_total_users", "ga_new_users",
            "ga_user_engagement", "ga_sessions", "ga_engaged_sessions", "ga_page_views", "cpm", "ctr", "cpc", "bp", "aap", "vqp",
            "cpp", "story_open_rate", "cost_per_story_open", "crm_confirmed_percentage", "crm_cancelled_percentage",
            "send_back_percentage", "crm_difference", "delivered_percentage", "cancelled_percentage", "final_percentage",
            "total_expense", "profit_and_loss", "profit_percentage", "product_cost_percentage", "delivery_cost_percentage",
            "ad_cost_percentage", "crm_percentage", "engagement_rate_percentage", "logistics_percentage",
        ];
        $defaultColumns = array_merge($defaultColumns, $newcolumns);
        return $defaultColumns;
    }

    public static function getSumAndRoundColumns(array $columns)
    {
        $raw_query = [];
        foreach ($columns as $column) {
            $raw_query[] = DB::raw("(SELECT (ROUND(SUM($column), 2)) as $column");
        }

        return $raw_query;
    }

    public static function roundAndSumSubQuery(Builder $query, array $columns)
    {
        foreach ($columns as $column) {
            $query = $query->selectRaw("(SELECT(ROUND(SUM($column), 2)) as $column");
        }

        return $query;
    }

    public static function nonCountableColumns(Builder $query, array $columns)
    {
        foreach ($columns as $column) {
            $query = $query->selectRaw("$column as $column");
        }

        return $query;
    }

    public static function refreshDisabledAds()
    {
        $yesterday = Carbon::yesterday();
        $deleteDate = Carbon::now()->subDays(4);
        DisabledAd::where(DB::raw('date(created_at)'), '<=', $deleteDate->toDateString())->delete();
        $ad_ids =   DisabledAd::where(DB::raw('date(created_at)'), $yesterday->toDateString())->groupBy('ad_id')->get()->pluck('ad_id');
        if (count($ad_ids) > 0) {

            $request = new Request([
                'dates' => $yesterday->toDateString(),
                'ad_ids' => $ad_ids
            ]);

            $connections = ConnectionRepository::connectionAccountByPlatform($request);

            $extra_data  = [
                "total" => $connections->count(),
                'dates' => $yesterday->toDateString()
            ];
            foreach ($connections as $index => $connection) {
                $extra_data["item_index"] = $index;
                SwitchByPlatformName::dispatch($connection, $extra_data);
            }
            return $extra_data;
        }
        return "no_ad";
    }
}
