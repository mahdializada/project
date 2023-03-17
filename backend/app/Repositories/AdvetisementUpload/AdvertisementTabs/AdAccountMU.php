<?php

namespace App\Repositories\AdvetisementUpload\AdvertisementTabs;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Repositories\Repository;
use App\Models\Advertisement\Application;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use App\Repositories\Advertisement\Platforms\TikTok;
use App\Repositories\Advertisement\Platforms\Facebook;
use App\Repositories\Advertisement\Platforms\GoogleAd;
use App\Repositories\Advertisement\Platforms\Snapchat;
use App\Models\Advertisement\AdAccount as AdAccountModel;
use App\Repositories\AdvetisementUpload\AdvertisementUtilMU;

class AdAccountMU extends Repository
{

    public function fetchAdAccounts(Request $request, $getCount = false)
    {
        try {
            $queryBuilder = new UriQueryBuilder(new AdAccountModel(), $request);
            $queryBuilder->itemsPerPage = 20;
            $query  = $queryBuilder->query->select(["id", "code", "currency", "timezone_name", "status",  "type", "balance", "name"]);
            $query = AdvertisementUtilMU::countAndAdsCalculations(
                $query,
                $request,
                "ad_account_id",
                "ad_accounts.code",
                ["total_companies", "total_products", "total_platforms", "total_organizations", "total_accounts"],
                ['labels']
            );
            if ($request->status) {
                $query = $query->where("status", $request->status);
            }
            $total = $query->get()->count();
            $query = $query->withCount('remarks')->with('adsets');
            $query = $query->withCount('campaignThroughConnection');

            if ($request->query->has("search_by_id")) {
                $data = $this->searchCode($query, $request, [], false, "code", false, false);
                return $getCount ?
                    $total :  response()->json(
                        ["result" => true, "total" => $total, "item" => $data]
                    );
            }
            $searchInColumns = ["id", "name", "code",  "status", "currency", 'timezone_name', "type"];
            $query = $this->searchContent($query, $searchInColumns, $request->input('searchContent'));
            $queryBuilder->query = $query;
            $paginate = $queryBuilder->build()->paginate()->getData();
            $items = AdsMU::makeAdsArrayAsObject($paginate->data);
            return $getCount ?
                $total :  response()->json(
                    ["result" => true, "total" => $total, "items" => $items]
                );
        } catch (\Throwable $th) {
            return response()->json(["result" => false, "code" => $th->getMessage()]);
        }
    }

    /**
     * Find Add Acount By ID
     */
    public static function findAdAccount(string $application_id, string $account_id)
    {
        $application = Application::with("platform:id,platform_name")->find($application_id);
        $platform_name = $application->platform->platform_name;
        $condition = [
            ["application_id", "=", $application->id],
            ["account_id", "=", $account_id],
        ];
        $exists_account = AdAccountModel::where($condition)->first();
        if ($exists_account) {
            return $exists_account;
        }
        switch ($platform_name) {
            case "facebook":
                return Facebook::findAndstoreAccount($application, $account_id);
            case "snapchat":
                return Snapchat::findAndstoreAccount($application, $account_id);
            case "tiktok":
                return TikTok::findAndstoreAccount($application, $account_id);
            case "google ads":
                return GoogleAd::findAndstoreAccount($application, $account_id);
            default:
                return null;
        }
    }


    public static function connectionAdAccounts()
    {
        $ad_accounts = AdAccountModel::whereHas("connections")->select(["id", "name"])->get();
        return $ad_accounts;
    }


    public static function adAccountsCalculationSubQueries($start_date = null, $end_date = null)
    {
        $start_date = Carbon::parse($start_date)->toDateString();
        $end_date = Carbon::parse($end_date)->toDateString();
        $round = AdvertisementUtilMU::$ROUND;
        $ads_calculation_query = AdsMU::adsCalculationSubQueries($start_date, $end_date);
        $ads_calculation_query = $ads_calculation_query->join(
            "history_adsets",
            "history_adsets.adset_id",
            "=",
            "history_ads.server_adset_id"
        )
            ->join("history_campaigns", "history_campaigns.campaign_id", "=", "history_adsets.server_campaign_id")
            ->join("ad_accounts", "ad_accounts.account_id", "=", "history_campaigns.server_account_id");
        $ads_calculation_query = $ads_calculation_query->whereBetween("history_campaigns.data_date", [$start_date, $end_date])
            ->whereBetween("history_adsets.data_date", [$start_date, $end_date])
            ->selectRaw("ROUND(SUM(history_adsets.daily_budget), $round) as daily_budget")
            ->selectRaw("ROUND(SUM(history_campaigns.budget), $round) as budget")
            ->selectRaw("ad_accounts.account_id as id, ad_accounts.name, ad_accounts.status, history_ads.currency as currency")
            ->selectRaw("ad_accounts.timezone_name, ad_accounts.type, ad_accounts.balance")
            ->selectRaw("ad_accounts.created_at, ad_accounts.updated_at")
            ->selectRaw("COUNT(DISTINCT(history_ads.ad_id)) AS total_ads")
            ->selectRaw("COUNT(DISTINCT(history_adsets.adset_id)) AS total_adsets")
            ->selectRaw("COUNT(DISTINCT(history_campaigns.campaign_id)) AS total_campaigns");
        $ads_calculation_query = AdsMU::extraCalculation($ads_calculation_query);
        return $ads_calculation_query->groupBy("ad_accounts.account_id");
    }
}
