<?php

namespace App\Repositories\AdvetisementUpload\AdvertisementTabs;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use App\Models\AdvetisementUpload\ConnectionMU;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use App\Models\AdvetisementUpload\HistoryCampaignMU;
use App\Repositories\Advertisement\Platforms\TikTok;
use App\Repositories\Advertisement\Platforms\Snapchat;
use App\Repositories\AdvetisementUpload\AdvertisementUtilMU;

class CampaignMU extends Repository
{
    public function fetchCampaigns(Request $request, $getCount = false)
    {
        try {
            $queryBuilder = new UriQueryBuilder(new HistoryCampaignMU(), $request);
            $queryBuilder->itemsPerPage = 20;
            $columns = ["campaign_id as id",  'code', "ad_account_id", "name", "status", "objective", "campaign_id", "delivery_status"];
            $filtered_date = [$request->start_date, $request->end_date];
            $query  = $queryBuilder->query->whereBetween("data_date", $filtered_date)->select($columns)
                ->selectRaw('sum(budget) as total_budget')->groupBy("campaign_id");
            $remove_totals =  ["total_companies", "total_products", "total_platforms", "total_organizations", "total_accounts", "total_campaigns"];
            $query = AdvertisementUtilMU::countAndAdsCalculations($query, $request, "server_campaign_id", "code", $remove_totals, ['labels', 'adAccount:id,name', 'campaignAdsets:id,server_campaign_id,bid']);
            if ($request->status) {
                $query = $query->where("status", $request->status);
            }
            $total = $query->get()->count();
            $query = $query->withCount('remarks')->withCount('campaignAdsets as adsets');
            $query = $query->withAvg('campaignAdsets', 'bid');

            if ($request->query->has("search_by_id")) {
                $data = $this->searchCode($query, $request, [], false, "code", false, false);
                return $getCount ?
                    $total :  response()->json(
                        ["result" => true, "total" => $total, "item" => $data]
                    );
            }
            $searchInColumns = ["campaign_id", "code",  "campaign_type", "name", "status", "objective", "objective_type", "budget", "budget_mode"];
            $query = $this->searchContent($query, $searchInColumns, $request->input('searchContent'));
            $queryBuilder->query = $query;
            $paginate = $queryBuilder->build()->paginate()->getData();
            $items = AdsMU::makeAdsArrayAsObjectUpload($paginate->data);
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
        $relation = ["application", "platform", "adAccount"];
        $connection = ConnectionMU::with($relation)->where("server_campaign_id", $campaign_id)->first();
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
        $start_date = Carbon::parse($request->start_date)->toDateString();
        $end_date = Carbon::parse($request->end_date)->toDateString();
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
        $campaign_query = AdvertisementUtilMU::getRawCondition($param_collections->get("budget"), $campaign_query, "sum(budget)");
        $campaign_query = AdvertisementUtilMU::getRawCondition($param_collections->get("budget_history"), $campaign_query, "count(history_campaigns.id)");
        $campaign_query = AdvertisementUtilMU::getRawCondition($param_collections->get("budget_history"), $campaign_query, "count(history_campaigns.id)");
        $campaign_query = $campaign_query->withCount("remarks");
        $campaign_query = $campaign_query->withCount("reasonables");
        $campaign_query = AdvertisementUtilMU::getCondition($request->status_history, $campaign_query, "reasonables_count");
        return $campaign_query;
    }




    public static function connectionCampaigns()
    {
        $campaigns = HistoryCampaignMU::groupBy(["campaign_id", "server_account_id"])->select(["id", "campaign_id", "name"])->get();
        return $campaigns;
    }
}
