<?php

namespace App\Repositories\Advertisement\AdvertisementTabs;

use App\Models\Advertisement\Connection;
use App\Models\Advertisement\HistoryCampaign;
use App\Repositories\Advertisement\AdvertisementRepository;
use App\Repositories\Advertisement\AdvertisementUtil;
use App\Repositories\Advertisement\Platforms\Snapchat;
use App\Repositories\Advertisement\Platforms\TikTok;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use App\Repositories\Repository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Campaign extends Repository
{
    public function fetchCampaigns(Request $request, $getCount = false)
    {

        try {
            $queryBuilder               = new UriQueryBuilder(new HistoryCampaign(), $request);
            $queryBuilder->itemsPerPage = 20;
            $columns                    = ["campaign_id as id", 'code', "ad_account_id", "name", "status", "objective", "campaign_id", "delivery_status"];
            $today                      = Carbon::today()->toDateTimeString();
            // where("data_date", $today)
            $query                      = $queryBuilder->query->where("data_date", $today)->select($columns)
                ->selectRaw('sum(budget) as total_budget')->groupBy("campaign_id");
            $remove_totals = ["total_companies", "total_products", "total_platforms", "total_organizations", "total_accounts", "total_campaigns"];
            $relations     = [
                "labels" => function ($subquery) {
                    $subquery->whereCurrentStatus('active')->where('sub_system', 'advertisement');
                }, 'adAccount:id,name', 'campaignAdsets:id,server_campaign_id,bid',
                "platform:platforms.id,platforms.platform_name",
            ];
            $query = AdvertisementUtil::countAndAdsCalculations($query, $request, "server_campaign_id", "code", $remove_totals, $relations);
            if ($request->status) {
                $query = $query->where("status", $request->status);
            }
            $searchInColumns     = ["campaign_id", "code", "campaign_type", "name", "status", "objective", "objective_type", "budget", "budget_mode"];
            $query               = $this->searchContent($query, $searchInColumns, $request->input('searchContent'));

            if ($getCount) {
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
                "campaignAdsets,history_adsets,adset_id"
            ];
            $query = AdvertisementRepository::getTotals($query, $request, $relationCounts);
            $query = $query->withCount('remarks')->withCount('campaignAdsets as adsets')
                ->with(['campaignAdsets' => function ($query) use ($request) {
                    $query->when($request, function ($query) use ($request) {
                        if ($request->start_date && $request->end_date)
                            $query->whereBetween(DB::raw('DATE(history_adsets.created_at)'), [$request->start_date, $request->end_date]);
                        else
                            $query->where(DB::raw('DATE(history_adsets.created_at)'), date("Y-m-d"));
                    });
                }])->with('productProfileInfo:item_code,prod_max_adver_cost');
            $query = $query->withAvg('campaignAdsets', 'bid')->with('connection_date', function ($query) {
                return $query->select(['server_campaign_id', 'created_at'])->oldest();
            });

            if ($request->query->has("search_by_id")) {
                $data = $this->searchCode($query, $request, [], false, "code", false, false);
                return $getCount ?
                    $total : response()->json(
                        ["result" => true, "total" => $total, "item" => $data]
                    );
            }

            $queryBuilder->query = $query;
            $paginate            = $queryBuilder->build()->paginate()->getData();
            $items               = Ads::makeAdsArrayAsObject($paginate->data);
            $items = Collect($items)->sortBy('connection_date.created_at')->values()->all();
            return $getCount ?
                $total : response()->json(
                    ["result" => true, "total" => $total, "items" => $items]
                );
        } catch (\Throwable $th) {
            return response()->json(["result" => false, "code" => $th->getMessage()]);
        }
    }

    public static function updateCampaignStatus($campaign_id)
    {
        $relation   = ["application", "platform", "adAccount"];
        $connection = Connection::with($relation)->where("server_campaign_id", $campaign_id)->first();
        if ($connection) {
            $platform_name = $connection->platform->platform_name;
            switch ($platform_name) {
                case 'snapchat':
                    return Snapchat::updateCampaignStatus($connection);
                case 'tiktok':
                    return TikTok::updateCampaignStatus($connection);
                default:
                    return "change_status_not_available";
            }
        }
        return "record_not_found";
    }

    public static function campaignCalculations($campaign_query, Request $request, $group_by = null)
    {
        $start_date     = Carbon::parse($request->start_date)->toDateString();
        $end_date       = Carbon::parse($request->end_date)->toDateString();
        $campaign_query = $campaign_query->whereBetween("data_date", [$start_date, $end_date]);
        if ($group_by) {
            $campaign_query = $campaign_query->groupBy($group_by);
        }
        return $campaign_query
            ->select(["campaign_id"])
            ->selectRaw("SUM(budget) as budget");
    }

    public static function filterCampaignQuery($campaign_query, Request $request)
    {
        $param_collections = collect($request->all());
        $campaign_query    = AdvertisementUtil::getRawCondition($param_collections->get("budget"), $campaign_query, "sum(budget)");
        $campaign_query    = AdvertisementUtil::getRawCondition($param_collections->get("budget_history"), $campaign_query, "count(history_campaigns.id)");
        $campaign_query    = AdvertisementUtil::getRawCondition($param_collections->get("budget_history"), $campaign_query, "count(history_campaigns.id)");
        $campaign_query    = $campaign_query->withCount("remarks");
        $campaign_query    = $campaign_query->withCount("reasonables");
        $campaign_query    = AdvertisementUtil::getCondition($request->status_history, $campaign_query, "reasonables_count");
        return $campaign_query;
    }

    public static function connectionCampaigns()
    {
        $campaigns = HistoryCampaign::groupBy(["campaign_id", "server_account_id"])->select(["id", "campaign_id", "name"])->get();
        return $campaigns;
    }
}
