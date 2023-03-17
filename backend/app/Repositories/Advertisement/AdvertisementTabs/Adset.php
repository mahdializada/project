<?php

namespace App\Repositories\Advertisement\AdvertisementTabs;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Repositories\Repository;
use App\Models\Advertisement\Connection;
use App\Models\Advertisement\HistoryAdset;
use App\Repositories\Advertisement\AdvertisementRepository;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use App\Repositories\Advertisement\AdvertisementUtil;
use App\Repositories\Advertisement\Platforms\Snapchat;
use App\Repositories\Advertisement\AdvertisementTabs\Ads;
use App\Repositories\Advertisement\Platforms\TikTok;
use Illuminate\Support\Facades\DB;

class Adset extends Repository
{
    public function fetchAdsets(Request $request, $getCount = false)
    {
        try {
            $param_collections = collect($request->all());
            $queryBuilder = new UriQueryBuilder(new HistoryAdset(), $request);
            $queryBuilder->itemsPerPage = 20;
            $columns = ["adset_id as id", "code", "adset_id", "status", "name", "delivery_status", "bid", "bid_strategy", "optimization_goal", "data_date", "updated_at"];
            $today = Carbon::today()->toDateTimeString();
            // ->where("data_date", $today)
            $query  = $queryBuilder->query->where("data_date", $today)->select($columns)
                ->selectRaw("sum(daily_budget) AS daily_budget")->groupBy("adset_id");
            $remove_totals =  [
                "total_companies", "total_products", "total_platforms",
                "total_organizations", "total_accounts", "total_campaigns", "total_adsets"
            ];
            $query = AdvertisementUtil::countAndAdsCalculations($query, $request, "server_adset_id", "code", $remove_totals, ["labels" => function ($subquery) {
                $subquery->whereCurrentStatus('active')->where('sub_system', 'advertisement');
            }, "platform:platforms.id,platforms.platform_name"]);
            $query = AdvertisementUtil::getCondition($param_collections->get("bid"), $query, "bid");
            $query = AdvertisementUtil::getCondition($param_collections->get("daily_budget"), $query, "daily_budget");
            if ($request->status) {
                $query = $query->where("status", $request->status);
            }
            $searchInColumns = [
                'name', 'adset_id', "code",  'server_campaign_id', 'status',
                'delivery_status', 'whereHasOne,connections,pcode', "optimization_goal", "bid"
            ];
            $query = $this->searchContent($query, $searchInColumns, $request->input('searchContent'));

            if ($getCount) {
                // this part is for tab counts
                $total = $query->get();
                return [
                    'total' => $total->count(),
                    "active" => $total->where('status', "ACTIVE")->count(),
                    "inactive" => $total->where('status', "!=", "ACTIVE")->count()
                ];
            }
            $total = $query->get()->count();

            $relationCounts = [
                "adThroughConnection,history_ads,ad_id",
            ];
            $query = AdvertisementRepository::getTotals($query, $request, $relationCounts);
            $query = $query->withCount(['remarks', 'campaignThroughConnection']);
            $query = $query->with('connection_date', function ($query) {
                return $query->select(['server_ad_adset_id', 'created_at'])->oldest();
            })->with('productProfileInfo:item_code,prod_max_adver_cost');
            if ($request->query->has("search_by_id")) {
                $data = $this->searchCode($query, $request, [], false, "code", false, false);
                return $getCount ?
                    $total :  response()->json(
                        ["result" => true, "total" => $total, "item" => $data]
                    );
            }

            $queryBuilder->query = $query;
            $paginate = $queryBuilder->build()->paginate()->getData();
            $items = Ads::makeAdsArrayAsObject($paginate->data);
            $items = Collect($items)->sortBy('connection_date.created_at')->values()->all();
            return $getCount ?
                $total :  response()->json(
                    ["result" => true, "total" => $total, "items" => $items]
                );
        } catch (\Throwable $th) {
            return response()->json(["result" => false, "code" => $th->getMessage()]);
        }
    }

    public static function adsetCalculations($adset_query, Request $request, $group_by = null)
    {
        $start_date = Carbon::parse($request->start_date)->toDateString();
        $end_date = Carbon::parse($request->end_date)->toDateString();
        $adset_query = $adset_query->whereBetween("data_date", [$start_date, $end_date]);
        if ($group_by) {
            $adset_query = $adset_query->groupBy($group_by);
        }
        return $adset_query
            ->select(["adset_id"])
            ->selectRaw("SUM(bid) as bid")
            ->selectRaw("SUM(daily_budget) as daily_budget");
    }

    public static function filterAdsetQuery($adset_query, Request $request)
    {
        $param_collections = collect($request->all());
        $adset_query = AdvertisementUtil::getRawCondition($param_collections->get("bid"), $adset_query, "sum(bid)");
        $adset_query = AdvertisementUtil::getRawCondition($param_collections->get("daily_budget"), $adset_query, "sum(daily_budget)");
        return $adset_query;
    }




    public static function connectionAdsets()
    {
        $adsets = HistoryAdset::groupBy(["adset_id", "server_campaign_id"])->select("id", "adset_id", "name")->get();
        return $adsets;
    }




    public static function updateAdsetStatus($adset_id)
    {
        $relation = ["application", "platform", "adAccount"];
        $connection = Connection::with($relation)->where("server_ad_adset_id", $adset_id)->first();
        if ($connection) {
            $platform_name = $connection->platform->platform_name;
            switch ($platform_name) {
                case 'snapchat':
                    return Snapchat::updateAdsetStatus($connection);
                case 'tiktok':
                    return TikTok::updateAdsetStatus($connection);
                default:
                    return "change_status_not_available";
            }
        }
        return "record_not_found";
    }
}
