<?php

namespace App\Repositories\AdvetisementUpload;

use App\Models\Advertisement\Application;
use App\Models\Advertisement\Platform;
use App\Models\Advertisement\Project;
use App\Models\AdvetisementUpload\ConnectionMU;
use App\Models\Company;
use App\Models\Country;
use App\Repositories\AdvetisementUpload\AdvertisementTabs\AdAccountMU;
use App\Repositories\AdvetisementUpload\AdvertisementTabs\AdsetMU;
use App\Repositories\AdvetisementUpload\AdvertisementTabs\AdsMU;
use App\Repositories\AdvetisementUpload\AdvertisementTabs\CampaignMU;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use App\Repositories\Repository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AdvertisementMURepository extends Repository
{
    /**
     * Fetch Country Data for Country Tab
     */
    public function fetchCountryItems(Request $request, $getCount = false)
    {
        try {
            $queryBuilder               = new UriQueryBuilder(new Country(), $request);
            $queryBuilder->itemsPerPage = -1;
            $query                      = $queryBuilder->query->select(["id", "code", "iso2", "name", "advertisement_status"]);
            $query                      = AdvertisementUtilMU::countAndAdsCalculations($query, $request, "country_id", "countries.code", [], ['labels']);
            $status                     = strtolower($request->status) ?? null;
            if ($status) {
                $query = $query->where("advertisement_status", $status);
            }
            $total = $query->get()->count();
            $query = $query->withCount('remarks')->with(['adsets']);
            $query = $query->withCount('reasonables');
            $query = $query->with(['campaignThroughConnection']);
            if ($request->query->has("search_by_id")) {
                $data = $this->searchCode($query, $request);
                return $getCount ?
                    $total : response()->json(["result" => true, "total" => $total, "item" => $data]);
            }
            $searchInColumns     = ["name"];
            $query               = $this->searchContent($query, $searchInColumns, $request->input('searchContent'));
            $queryBuilder->query = $query;
            $total               = $query->get()->count();
            $paginate            = $queryBuilder->build()->paginate()->getData();
            $items               = AdsMU::makeAdsArrayAsObject($paginate->data);
            return $getCount ?
                $total : response()->json(
                    [
                        "result" => true, "total" => $total, "items" => $items,
                    ]
                );
        } catch (\Throwable $th) {
            return response()->json(["result" => false, "code" => $th->getMessage()]);
        }
    }

    /**
     * Fetch Company Data for Company Tab According to countries
     */
    public function fetchCompanies(Request $request, $getCount = false)
    {
        try {
            $queryBuilder               = new UriQueryBuilder(new Company(), $request);
            $queryBuilder->itemsPerPage = 20;
            $query                      = $queryBuilder->query->select(["id", "code", "logo", "name", "advertisement_status"]);
            $query                      = AdvertisementUtilMU::countAndAdsCalculations($query, $request, "company_id", "companies.code", ["total_companies"], ['labels']);
            $status                     = strtolower($request->status) ?? null;
            if ($status) {
                $query = $query->where("advertisement_status", $status);
            }
            $total = $query->get()->count();
            $query = $query->withCount('campaignThroughConnection');

            $query = $query->withCount('remarks')->with('adsets');
            if ($request->query->has("search_by_id")) {
                $data = $this->searchCode($query, $request);
                return $getCount ?
                    $total :
                    response()->json(
                        ["result" => true, "total" => $total, "item" => $data]
                    );
            }
            $searchInColumns     = ["name", "code"];
            $query               = $this->searchContent($query, $searchInColumns, $request->input('searchContent'));
            $queryBuilder->query = $query;
            $paginate            = $queryBuilder->build()->paginate()->getData();
            $items               = AdsMU::makeAdsArrayAsObject($paginate->data);
            return $getCount ?
                $total : response()->json(
                    ["result" => true, "total" => $total, "items" => $items]
                );
        } catch (\Throwable $th) {
            return response()->json(["result" => false, "code" => $th->getMessage()]);
        }
    }
    public function fetchProjects(Request $request, $getCount = false)
    {
        try {
            $queryBuilder               = new UriQueryBuilder(new Project(), $request);
            $queryBuilder->itemsPerPage = 20;
            $query                      = $queryBuilder->query->select(["id", "code", "domain", "name", "advertisement_status"]);
            $query                      = AdvertisementUtilMU::countAndAdsCalculations($query, $request, "project_id", "projects.code", ["total_projects"], ['labels']);
            $status                     = strtolower($request->status) ?? null;
            if ($status) {
                $query = $query->where("advertisement_status", $status);
            }
            $total = $query->get()->count();
            $query = $query->withCount('remarks')->with('adsets');
            $query = $query->withCount('campaignThroughConnection');

            if ($request->query->has("search_by_id")) {
                $data = $this->searchCode($query, $request);
                return $getCount ?
                    $total :
                    response()->json(
                        ["result" => true, "total" => $total, "item" => $data]
                    );
            }
            $searchInColumns     = ["name", "code"];
            $query               = $this->searchContent($query, $searchInColumns, $request->input('searchContent'));
            $queryBuilder->query = $query;
            $paginate            = $queryBuilder->build()->paginate()->getData();
            $items               = AdsMU::makeAdsArrayAsObject($paginate->data);
            return $getCount ?
                $total : response()->json(
                    ["result" => true, "total" => $total, "items" => $items]
                );
        } catch (\Throwable $th) {
            return response()->json(["result" => false, "code" => $th->getMessage()]);
        }
    }

    /**
     * Fetch Products  Data for Item Code Tab
     */
    public function fetchItemCode(Request $request, $getCount = false)
    {
        try {
            $queryBuilder               = new UriQueryBuilder(new ConnectionMU(), $request);
            $queryBuilder->itemsPerPage = 20;
            $query                      = $queryBuilder->query->select(["id", "pcode", "pname", "code", "server_ad_id"])->groupBy("pcode");
            $query                      = self::ItemCodeAdsCalculations($query, $request);
            $total                      = $query->get()->count();
            $query                      = $query->withCount('remarks')->with('adsets');
            $query = $query->withCount('campaigns');
            if ($request->query->has("search_by_id")) {
                $data = $this->searchCode($query, $request, [], false, "pcode", false, false);
                return $getCount ?
                    $total : response()->json(
                        ["result" => true, "total" => $total, "item" => $data]
                    );
            }
            $searchInColumns     = ["pcode"];
            $query               = $this->searchContent($query, $searchInColumns, $request->input('searchContent'));
            $queryBuilder->query = $query;
            $paginate            = $queryBuilder->build()->paginate()->getData();
            $items               = collect($paginate->data)->whereNotNull("history_ad")->values();
            return $getCount ?
                $total : response()->json(
                    ["result" => true, "total" => $total, "items" => $items]
                );
        } catch (\Throwable $th) {
            return response()->json(["result" => false, "code" => $th->getMessage()]);
        }
    }

    public static function ItemCodeAdsCalculations($query, Request $request)
    {
        $relation = ["historyAd" => function ($ads_query) use ($request) {
            $ads_query = AdsMU::adsCalculationSubQueries($ads_query, $request->start_date, $request->end_date, true)
                ->groupBy("ad_id");
            $ads_query = AdvertisementUtilMU::filterQueryParams($ads_query, $request);
            return $ads_query;
        }, "labels"];

        $start_date = Carbon::parse($request->start_date)->toDateString();
        $end_date   = Carbon::parse($request->end_date)->toDateString();
        $query      = $query->with($relation)->whereHas("historyAd", function ($ads_query) use ($start_date, $end_date) {
            return $ads_query->whereBetween("data_date", [$start_date, $end_date]);
        });
        $query = $query->selectRaw("count(distinct(platform_id)) as total_platforms");
        $query = $query->selectRaw("count(distinct(application_id)) as total_organizations");
        $query = $query->selectRaw("count(distinct(ad_account_id)) as total_accounts");
        $query = $query->selectRaw("count(distinct(server_campaign_id)) as total_campaigns");
        $query = $query->selectRaw("count(distinct(server_ad_adset_id)) as total_adsets");
        $query = $query->selectRaw("count(distinct(server_ad_id)) as total_ads");
        $query = AdvertisementUtilMU::filterByQueryIds($query, $request, "pcode");
        $query = AdvertisementUtilMU::filterByConColumns($query, $request);
        return $query;
    }

    /**
     * Fetch Issp  Data for Issp Code Tab
     */
    public function fetchIsppCode($params = [])
    {
        $ispps = [];
        return response()->json(["result" => true, "items" => $ispps]);
    }

    /**
     * Fetch Platforms  Data for Platforms Tab
     */
    public function fetchPlatforms(Request $request, $getCount = false)
    {
        try {
            $queryBuilder               = new UriQueryBuilder(new Platform(), $request);
            $queryBuilder->itemsPerPage = 20;
            $query                      = $queryBuilder->query->select(["id", "code", "platform_name", "platform_key", "advertisement_status"]);
            $query                      = AdvertisementUtilMU::countAndAdsCalculations(
                $query,
                $request,
                "platform_id",
                "platforms.code",
                ["total_companies", "total_products", "total_platforms"],
                ['labels']
            );
            $status = strtolower($request->status) ?? null;
            if ($status) {
                $query = $query->where("advertisement_status", $status);
            }
            $total = $query->get()->count();
            $query = $query->withCount('remarks')->with('adsets');
            $query = $query->withCount('campaignThroughConnection');


            if ($request->query->has("search_by_id")) {
                $data = $this->searchCode($query, $request);
                return $getCount ?
                    $total : response()->json(
                        ["result" => true, "total" => $total, "item" => $data]
                    );
            }
            $searchInColumns     = ["code", "platform_name", "platform_key"];
            $query               = $this->searchContent($query, $searchInColumns, $request->input('searchContent'));
            $queryBuilder->query = $query;
            $paginate            = $queryBuilder->build()->paginate()->getData();
            $items               = AdsMU::makeAdsArrayAsObject($paginate->data);
            return $getCount ?
                $total : response()->json(
                    ["result" => true, "total" => $total, "items" => $items]
                );
        } catch (\Throwable $th) {
            return response()->json(["result" => false, "code" => $th->getMessage()]);
        }
    }

    /**
     * Fetch Organizations  Data for Organizations Tab
     */
    public function fetchOrganizations(Request $request, $getCount = false)
    {
        try {
            $queryBuilder               = new UriQueryBuilder(new Application(), $request);
            $queryBuilder->itemsPerPage = 20;
            $query                      = $queryBuilder->query->select(["id", "code", "name", "advertisement_status"]);
            $query                      = AdvertisementUtilMU::countAndAdsCalculations(
                $query,
                $request,
                "application_id",
                "applications.code",
                ["total_companies", "total_products", "total_platforms", "total_organizations"],
                ['labels']
            );
            $status = strtolower($request->status) ?? null;
            if ($status) {
                $query = $query->where("advertisement_status", $status);
            }
            $total = $query->get()->count();
            $query = $query->withCount('remarks')->with('adsets');
            $query = $query->withCount('campaignThroughConnection');


            if ($request->query->has("search_by_id")) {
                $data = $this->searchCode($query, $request);
                return $getCount ?
                    $total : response()->json(
                        ["result" => true, "total" => $total, "item" => $data]
                    );
            }
            $searchInColumns     = ["code", "name", "code", "whereHasOne,platform,platform_name"];
            $query               = $this->searchContent($query, $searchInColumns, $request->input('searchContent'));
            $queryBuilder->query = $query;
            $paginate            = $queryBuilder->build()->paginate()->getData();
            $items               = AdsMU::makeAdsArrayAsObject($paginate->data);
            return $getCount ?
                $total : response()->json(
                    ["result" => true, "total" => $total, "items" => $items]
                );
        } catch (\Throwable $th) {
            return response()->json(["result" => false, "code" => $th->getMessage()]);
        }
    }

    public function getCounts($request)
    {
        $counts            = [];
        $counts['country'] = $this->fetchCountryItems($request, true);

        $counts['company'] = $this->fetchCompanies($request, true);

        $counts['project'] = $this->fetchCompanies($request, true);

        $counts['item_code'] = $this->fetchItemCode($request, true);

        // $counts['ispp_code'] = $repository->fetchIsppCode($request, true);

        $counts['platform'] = $this->fetchPlatforms($request, true);

        $counts['organization'] = $this->fetchOrganizations($request, true);

        $addAccount           = new AdAccountMU();
        $counts['ad_account'] = $addAccount->fetchAdAccounts($request, true);

        $compaign           = new CampaignMU();
        $counts['campaign'] = $compaign->fetchCampaigns($request, true);

        $adset            = new AdsetMU();
        $counts['ad_set'] = $adset->fetchAdsets($request, true);

        $ad           = new AdsMU();
        $counts['ad'] = $ad->fetchAdItems($request, true);

        return response()->json([
            'counts' => $counts, 'result' => true,
        ]);
    }

    public static function findParentModel(Model $model, $id)
    {
        return $model->whereId($id)->first();
    }
}
