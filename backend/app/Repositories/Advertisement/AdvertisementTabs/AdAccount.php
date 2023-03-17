<?php

namespace App\Repositories\Advertisement\AdvertisementTabs;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Repositories\Repository;
use App\Models\Advertisement\Application;
use Illuminate\Database\Eloquent\Builder;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use App\Repositories\Advertisement\Platforms\TikTok;
use App\Repositories\Advertisement\AdvertisementUtil;
use App\Repositories\Advertisement\Platforms\Facebook;
use App\Repositories\Advertisement\Platforms\GoogleAd;
use App\Repositories\Advertisement\Platforms\Snapchat;
use App\Models\Advertisement\AdAccount as AdAccountModel;
use App\Repositories\Advertisement\AdvertisementRepository;
use Illuminate\Support\Facades\DB;

class AdAccount extends Repository
{

    public function fetchAdAccounts(Request $request, $getCount = false)
    {
        try {
            $queryBuilder = new UriQueryBuilder(new AdAccountModel(), $request);

            $queryBuilder->itemsPerPage = 20;
            $query  = $queryBuilder->query->select(["id", "code", "currency", "timezone_name", "status", 'ad_account_balance',  "type", "balance", "name", "account_id", "account_po", 'advertisement_status']);

            $query = AdvertisementUtil::countAndAdsCalculations(
                $query,
                $request,
                "ad_account_id",
                "ad_accounts.code",
                ["total_companies", "total_products", "total_platforms", "total_organizations", "total_accounts"],
                ["labels" => function ($subquery) {
                    $subquery->whereCurrentStatus('active')->where('sub_system', 'advertisement');
                }]
            );
            if ($request->status) {
                $query = $query->where("status", $request->status);
            }
            $searchInColumns = ["id", "name", "code",  "status", "currency", 'timezone_name', "type"];
            $query = $this->searchContent($query, $searchInColumns, $request->input('searchContent'));
            $total =  clone $query;
            $total = $total->select('status', 'id')->get();
            if ($getCount) {
                return [
                    'total' => $total->count(),
                    "active" => $total->where('status', "ACTIVE")->count(),
                    "inactive" => $total->where('status', "!=", "ACTIVE")->count()
                ];
            }


            $relationCounts = [
                "adThroughConnection,history_ads,ad_id",
                "adsetThroughConnection,history_adsets,adset_id",
                "campaignThroughConnection,history_campaigns,campaign_id"
            ];
            $query = AdvertisementRepository::getTotals($query, $request, $relationCounts);

            $query = $query->withCount('remarks')->with([
                'connection.platform:id,platform_name',
                'adAccountPixels:ad_account_id,pixel_id',
                'bankAccount', 'connection:id,platform_id,ad_account_id', 'connection.platform:id,platform_name', 'adsets' => function ($query) use ($request) {
                    $query->when($request, function ($query) use ($request) {
                        if ($request->start_date && $request->end_date)
                            $query->whereBetween(DB::raw('DATE(history_adsets.created_at)'), [$request->start_date, $request->end_date])
                                ->select('bid', 'currency');
                        else
                            $query->where(DB::raw('DATE(history_adsets.created_at)'), date("Y-m-d"))->select('bid', 'currency');
                    });
                }
            ]);
            $query = $query->withCount('campaignThroughConnection')->with('connection_date', function ($query) {
                return $query->select(['ad_account_id', 'created_at'])->oldest();
            });

            if ($request->query->has("search_by_id")) {
                $data = $this->searchCode($query, $request, [], false, "code", false, false);
                return  ["result" => true, "total" => $total->count(), "item" => $data];
            }

            $query = $query->orderBy('created_at', 'asc');
            $queryBuilder->query = $query;
            $paginate = $queryBuilder->build()->paginate()->getData();
            $items = Ads::makeAdsArrayAsObject($paginate->data);
            return  response()->json(
                ["result" => true, "total" => $total->count(), "items" => $items,]
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
        $round = AdvertisementUtil::$ROUND;
        $ads_calculation_query = Ads::adsCalculationSubQueries($start_date, $end_date);
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
        $ads_calculation_query = Ads::extraCalculation($ads_calculation_query);
        return $ads_calculation_query->groupBy("ad_accounts.account_id");
    }
}
