<?php

namespace App\Repositories\Advertisement;

use App\Exports\Advertisement\ConnectionExport;
use App\Models\Advertisement\AdAccount as AdAccountModel;
use App\Models\Advertisement\Application;
use App\Models\Advertisement\Connection;
use App\Models\Advertisement\HistoryAd;
use App\Models\Advertisement\HistoryAdset;
use App\Models\Advertisement\HistoryCampaign;
use App\Models\Advertisement\Platform;
use App\Models\Advertisement\ProductProfileInfo;
use App\Models\Advertisement\Project;
use App\Models\CloudAttachment;
use App\Models\Company;
use App\Models\Country;
use App\Models\OtpCode;
use App\Models\SingleSales\Ispp;
use App\Models\TempFile;
use App\Repositories\Advertisement\AdvertisementTabs\AdAccount;
use App\Repositories\Advertisement\AdvertisementTabs\Ads;
use App\Repositories\Advertisement\AdvertisementTabs\Adset;
use App\Repositories\Advertisement\AdvertisementTabs\Campaign;
use App\Repositories\Advertisement\AdvertisementUtil;
use App\Repositories\Advertisement\Platforms\Snapchat;
use App\Repositories\Advertisement\Platforms\TikTok;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use App\Repositories\Repository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\OnlineSalesManagement\OnlineSales;
use App\Models\Remark;
use App\Repositories\Files\CloudinaryFileUtils;

class AdvertisementRepository extends Repository
{

    /**
     * Fetch Country Data for Country Tab
     */
    public function fetchCountryItems(Request $request, $getCount = false)
    {

        try {
            $queryBuilder = new UriQueryBuilder(new Country(), $request);

            $queryBuilder->itemsPerPage = -1;
            $query = $queryBuilder->query->select(["id", "code", "iso2", "name", "advertisement_status",]);
            $query = AdvertisementUtil::countAndAdsCalculations($query, $request, "country_id", "countries.code", [], ['labels' => function ($subquery) {
                # code...
                $subquery->whereCurrentStatus('active')->where('sub_system', 'advertisement');
            }]);
            $status = strtolower($request->status) ?? null;
            if ($status) {
                $query = $query->where("advertisement_status", $status);
            }

            $searchInColumns = ["name", 'code'];
            $query = $this->searchContent($query, $searchInColumns, $request->input('searchContent'));
            $total = $query->get()->count();

            if ($getCount)
                return $total;
            $query = $query->withCount(['remarks' => (fn ($q) => $q->whereSubSystem('advertisement')), 'reasonables']);

            // ->with(['adsets' => function ($query) use ($request) {
            //     $query->when($request, function ($query) use ($request) {
            //         if ($request->start_date && $request->end_date)
            //             $query->whereBetween(DB::raw('DATE(history_adsets.created_at)'), [$request->start_date, $request->end_date])
            //                 ->select('bid', 'currency');
            //         else
            //             $query->where(DB::raw('DATE(history_adsets.created_at)'), date("Y-m-d"))->select('bid', 'currency');
            //     });
            // }])

            $relationCounts = [
                "adThroughConnection,history_ads,ad_id",
                "adsetThroughConnection,history_adsets,adset_id",
                "campaignThroughConnection,history_campaigns,campaign_id"
            ];
            $query = self::getTotals($query, $request, $relationCounts);
            $query = $query->withSum('adAccontThroughConnections as total_ad_account_balance', 'ad_account_balance');
            $query = $query->withCount(['reasonables', 'adAccontThroughConnections as total_active_ad_account' => function ($adAccount) {
                return $adAccount->select(DB::raw('count(distinct(connections.server_account_id))'))->whereStatus("ACTIVE");
            }]);
            $query = $query->withCount(['adAccontThroughConnections as total_inactive_ad_account' => function ($adAccount) {
                return $adAccount->select(DB::raw('count(distinct(connections.server_account_id))'))->whereStatus("INACTIVE");
            }]);
            $query = $query->with('connection_date', function ($query) {
                return $query->select(['country_id', 'created_at'])->oldest();
            });
            if ($request->query->has("search_by_id")) {
                $data = $this->searchCode($query, $request);
                return $getCount ?
                    $total : response()->json(["result" => true, "total" => $total, "item" => $data]);
            }
            $query = $query->orderBy('created_at', 'asc');
            $queryBuilder->query = $query;
            $total = $query->get()->count();
            $paginate = $queryBuilder->build()->paginate()->getData();
            $items = Ads::makeAdsArrayAsObject($paginate->data);
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
    public static function getTotalAds($query, Request $request, $relation_name = 'adThroughConnection')
    {

        $query = $query->withCount(["$relation_name as active_ads"  => function ($query) use ($request) {
            if ($request->start_date && $request->end_date)
                $query->whereBetween(DB::raw('DATE(history_ads.created_at)'), [$request->start_date, $request->end_date])
                    ->whereStatus("ACTIVE")->select(DB::raw('count(distinct(history_ads.ad_id))'));
            else
                $query->where(DB::raw('DATE(history_ads.created_at)'), date("Y-m-d"))->whereStatus("ACTIVE")->select(DB::raw('count(distinct(history_ads.ad_id))'));
        }, "$relation_name as inactive_ads"  => function ($query) use ($request) {
            if ($request->start_date && $request->end_date)

                $query->whereBetween(DB::raw('DATE(history_ads.created_at)'), [$request->start_date, $request->end_date])
                    ->whereStatus("INACTIVE")->select(DB::raw('count(distinct(history_ads.ad_id))'));
            else
                $query->where(DB::raw('DATE(history_ads.created_at)'), date("Y-m-d"))->whereStatus("INACTIVE")->select(DB::raw('count(distinct(history_ads.ad_id))'));
        }]);
        return $query;
    }
    public static function getTotals($query, Request $request, $countValues)
    {
        for ($i = 0; $i < count($countValues); $i++) {
            $params = explode(",", $countValues[$i]);
            $query = self::getTotalActiveInActive($query, $request, $params[0], $params[1], $params[2]);
        }
        return $query;
    }
    public static function getTotalActiveInActive($query, Request $request, $relation_name, $table_name, $distinct_column, $column = "status:ACTIVE:INACTIVE")
    {
        $columnName = explode(":", $column);
        $status1 = $columnName[1];
        $status2 = $columnName[2];
        $columnName = $columnName[0];

        $query = $query->withCount(
            [
                "$relation_name as active_$table_name"  => function ($query) use ($request, $table_name, $distinct_column, $columnName, $status1) {
                    if ($request->start_date && $request->end_date)
                        $query->whereBetween(DB::raw("DATE($table_name.created_at)"), [$request->start_date, $request->end_date])
                            ->where($columnName, $status1);
                    else
                        $query->where(DB::raw("DATE($table_name.created_at)"), date("Y-m-d"))->where($columnName, $status1);
                    // ;->select(DB::raw("count(distinct($table_name.$distinct_column))"));
                },
                "$relation_name as inactive_$table_name"  => function ($query) use ($request, $table_name, $distinct_column, $column, $columnName, $status1) {
                    if ($request->start_date && $request->end_date)
                        $query->whereBetween(DB::raw("DATE($table_name.created_at)"), [$request->start_date, $request->end_date])
                            ->where($columnName, "!=", $status1)->select(DB::raw("count(distinct($table_name.$distinct_column))"));
                    else
                        $query->where(DB::raw("DATE($table_name.created_at)"), date("Y-m-d"))->where($columnName, "!=", $status1);
                }
            ]
        );
        return $query;
    }
    public static function getTotalProductStatus($query, Request $request, $relation_name = 'connections')
    {
        $query = $query->withCount(["$relation_name as active_products"  => function ($query) use ($request) {
            $query->where('advertisement_status', "active");
        }, "$relation_name as inactive_products"  => function ($query) use ($request) {
            $query->where('advertisement_status', "inactive");
        }]);
        return $query;
    }
    /**
     * Fetch Company Data for Company Tab According to countries
     */
    public function fetchCompanies(Request $request, $getCount = false)
    {
        try {
            $queryBuilder = new UriQueryBuilder(new Company(), $request);
            $queryBuilder->itemsPerPage = 20;
            $query = $queryBuilder->query->select(["id", "code", "logo", "name", "advertisement_status"]);
            $query = AdvertisementUtil::countAndAdsCalculations($query, $request, "company_id", "companies.code", ["total_companies"], ['labels' => function ($subquery) {
                # code...
                $subquery->whereCurrentStatus('active')->where('sub_system', 'advertisement');
            }]);
            $status = strtolower($request->status) ?? null;
            if ($status) {
                $query = $query->where("advertisement_status", $status);
            }
            $searchInColumns = ["name", "code"];
            $query = $this->searchContent($query, $searchInColumns, $request->input('searchContent'));
            $total = $query->get()->count();
            if ($request->query->has("search_by_id")) {
                $data = $this->searchCode($query, $request);
                return $getCount ?
                    $total :
                    response()->json(
                        ["result" => true, "total" => $total, "item" => $data]
                    );
            }
            if ($getCount) {
                return $total;
            }

            $query = $query->withCount('campaignThroughConnection')->with('connection_date', function ($query) {
                return $query->select(['company_id', 'created_at'])->oldest();
            });
            $relationCounts = [
                "adThroughConnection,history_ads,ad_id",
                "adsetThroughConnection,history_adsets,adset_id",
                "campaignThroughConnection,history_campaigns,campaign_id"
            ];
            $query = self::getTotals($query, $request, $relationCounts);
            $query = $query->withSum('adAccontThroughConnections as total_ad_account_balance', 'ad_account_balance');

            $query = $query->withCount(['adAccontThroughConnections as total_active_ad_account' => function ($adAccount) {
                return $adAccount->select(DB::raw('count(distinct(connections.server_account_id))'))->whereStatus("ACTIVE");
            }]);
            $query = $query->withCount(['adAccontThroughConnections as total_inactive_ad_account' => function ($adAccount) {
                return $adAccount->select(DB::raw('count(distinct(connections.server_account_id))'))->whereStatus("INACTIVE");
            }]);
            $query = $query->withCount(['remarks' => (fn ($q) => $q->whereSubSystem('advertisement'))]);
            // ->with(['adsets' => function ($query) use ($request) {
            //     $query->when($request, function ($query) use ($request) {
            //         if ($request->start_date && $request->end_date)
            //             $query->whereBetween(DB::raw('DATE(history_adsets.created_at)'), [$request->start_date, $request->end_date])
            //                 ->select('bid', 'currency');
            //         else
            //             $query->where(DB::raw('DATE(history_adsets.created_at)'), date("Y-m-d"))->select('bid', 'currency');
            //     });
            // }])

            $query = $query->orderBy('created_at', 'asc');
            $queryBuilder->query = $query;
            $paginate = $queryBuilder->build()->paginate()->getData();
            $items = Ads::makeAdsArrayAsObject($paginate->data);
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
            $queryBuilder = new UriQueryBuilder(new Project(), $request);
            $queryBuilder->itemsPerPage = 20;
            $query = $queryBuilder->query->select(["id", "code", "domain", "name", "advertisement_status"]);
            $query = AdvertisementUtil::countAndAdsCalculations($query, $request, "project_id", "projects.code", ["total_projects"], ['labels' => function ($subquery) {
                # code...
                $subquery->whereCurrentStatus('active')->where('sub_system', 'advertisement');
            }, "adAccontThroughConnections"]);
            $status = strtolower($request->status) ?? null;
            if ($status) {
                $query = $query->where("advertisement_status", $status);
            }

            $searchInColumns = ["name", "code"];
            $query = $this->searchContent($query, $searchInColumns, $request->input('searchContent'));
            $total = $query->get()->count();
            if ($request->query->has("search_by_id")) {
                $data = $this->searchCode($query, $request);
                return $getCount ?
                    $total :
                    response()->json(
                        ["result" => true, "total" => $total, "item" => $data]
                    );
            }
            if ($getCount) {
                return $total;
            }
            $relationCounts = [
                "adThroughConnection,history_ads,ad_id",
                "adsetThroughConnection,history_adsets,adset_id",
                "campaignThroughConnection,history_campaigns,campaign_id"
            ];
            $query = self::getTotals($query, $request, $relationCounts);
            $query = $query->withCount(['remarks' => (fn ($q) => $q->whereSubSystem('advertisement'))]);


            // ->with(['adsets' => function ($query) use ($request) {
            //     $query->when($request, function ($query) use ($request) {
            //         if ($request->start_date && $request->end_date)
            //             $query->whereBetween(DB::raw('DATE(history_adsets.created_at)'), [$request->start_date, $request->end_date])
            //                 ->select('bid', 'currency');
            //         else
            //             $query->where(DB::raw('DATE(history_adsets.created_at)'), date("Y-m-d"))->select('bid', 'currency');
            //     });
            // }])

            $query = $query->withSum('adAccontThroughConnections as total_ad_account_balance', 'ad_account_balance');

            $query = $query->withCount(['adAccontThroughConnections as total_active_ad_account' => function ($adAccount) {
                return $adAccount->select(DB::raw('count(distinct(connections.server_account_id))'))->whereStatus("ACTIVE");
            }]);
            $query = $query->withCount(['adAccontThroughConnections as total_inactive_ad_account' => function ($adAccount) {
                return $adAccount->select(DB::raw('count(distinct(connections.server_account_id))'))->whereStatus("INACTIVE");
            }]);
            $query = $query->withCount('campaignThroughConnection')->with('connection_date', function ($query) {
                return $query->select(['project_id', 'created_at'])->oldest();
            });


            $query = $query->orderBy('created_at', 'asc');
            $queryBuilder->query = $query;
            $paginate = $queryBuilder->build()->paginate()->getData();
            $items = Ads::makeAdsArrayAsObject($paginate->data);
            return $getCount ?
                $total : response()->json(
                    ["result" => true, "total" => $total, "items" => $items]
                );
        } catch (\Throwable $th) {
            return response()->json(["result" => false, "code" => $th->getMessage()]);
        }
    }

    public function fetchSalesType(Request $request, $getCount = false)
    {

        try {
            $queryBuilder = new UriQueryBuilder(new Connection(), $request);
            $queryBuilder->itemsPerPage = 20;
            $query                      = $queryBuilder->query->select(["id", "pcode", "pname", "code", "server_ad_id", "server_campaign_id", "created_at", "updated_at", "company_id", "country_id", 'sales_type'])
                ->selectRaw('sales_type_status as advertisement_status')->latest()->groupBy(["sales_type"]);
            $query                      = self::SalesTypeAdsCalculations($query, $request);
            $searchInColumns = ["sales_type"];
            $query = $this->searchContent($query, $searchInColumns, $request->input('searchContent'));
            $total                      = $query->get()->count();
            if ($getCount) {
                return $total;
            }
            $queryBuilder->query = $query;
            $query = $query->withCount(['remarks' => (fn ($q) => $q->whereSubSystem('advertisement')->whereType('sales_type'))])->with(['company:id,name,logo', 'country:id,name,iso2', 'adsets' => function ($query) use ($request) {
                $query->when($request, function ($query) use ($request) {
                    $query->whereBetween(DB::raw('DATE(history_adsets.created_at)'), [$request->start_date, $request->end_date])
                        ->select('bid', 'currency', DB::raw('history_adsets.created_at'));
                });
            }]);
            $query = $query->withCount('campaigns');
            $relationCounts = [
                "historyAdds,history_ads,ad_id",
                "historyAddsets,history_adsets,adset_id",
                "historyCampians,history_campaigns,campaign_id"
            ];
            $query = self::getTotals($query, $request, $relationCounts);
            if ($request->query->has("search_by_id")) {
                $data = $this->searchCode($query, $request, [], false, "sales_type", false, false);
                return $getCount ?
                    $total : response()->json(
                        ["result" => true, "total" => $total, "item" => $data]
                    );
            }

            $query = $query->orderBy(DB::raw('min(created_at)'), 'asc');
            $queryBuilder->query = $query;
            $paginate = $queryBuilder->build()->paginate()->getData();
            $items = self::makeSalesTypeAdsArrayAsObject($paginate->data);
            return $getCount ?
                $total : response()->json(
                    ["result" => true, "total" => $total, "items" => $items]
                );
        } catch (\Throwable $th) {
            return response()->json(["result" => false, "sales_type" => $th->getMessage()]);
        }
    }
    /**
     * Fetch Products  Data for Item Code Tab
     */
    public function fetchItemCode(Request $request, $getCount = false)
    {
        try {
            $queryBuilder = new UriQueryBuilder(new Connection(), $request);
            $queryBuilder->itemsPerPage = 20;
            $query                      = $queryBuilder->query->select(["id", "pcode", "pname", "code", "server_ad_id", "server_campaign_id", "advertisement_status", "created_at", "updated_at", "company_id", "country_id"]);
            if ($request->company)
                // group by with pcode country and selected company
                $query = $query->groupBy(["pcode", "country_id", "company_id"]);
            else
                // group by with pcode and country
                $query = $query->groupBy(["country_id", 'pcode']);
            // $query = $query->groupBy(["pcode"]);

            $query                      = self::ItemCodeAdsCalculations($query, $request);
            $query                      = self::ItemCodeProductProfileFitering($query, $request);

            $searchInColumns = ["pcode", 'pname', 'whereHasOne,company,name', 'whereHasOne,country,name', 'advertisement_status'];
            $status = strtolower($request->status) ?? null;
            if ($status) {
                $query = $query->where("advertisement_status", $status);
            }
            $query = $this->searchContent($query, $searchInColumns, $request->input('searchContent'));
            if ($getCount) {
                $query = $query->with('itemStatus');
                $newTotal = clone $query;
                if ($request->country)
                    $newTotal = $newTotal->whereHas('itemStatus', function ($q) use ($request) {
                        $q->whereIn('country_id', $request->country);
                    });
                $total = $query->get();
                $tabs = [
                    'new_item' => 0,
                    "in_study" => 0,
                    "create_content" => 0,
                    "content_review" => 0,
                    "ready_to_sale" => 0,
                    "in_testing" => 0,
                    "remove" => 0,
                    "in_sales" => 0,
                    "in_creation" => 0,
                    "retesting" => 0,
                    "short_stop" => 0,
                    "waiting_for_md" => 0,
                    "final_stop" => 0,
                    "cancel" => 0,
                ];
                foreach ($tabs as $key => $value) {
                    $tabs[$key] =  $newTotal->get()->filter(function ($val, $k) use ($key) {
                        $val = collect($val);
                        return  @$val['item_status']['item_status'] == $key;
                    })->count();
                }
                $tabs['all'] = $total->count();
                return [
                    'total' => $total->count(),
                    "active" => $total->where('advertisement_status', "active")->count(),
                    "inactive" => $total->where('advertisement_status', "!=", "active")->count(),
                    "item_code_tabs" => $tabs,
                ];
            }
            if ($request->item_code_tab != 'all') {
                $query = $query->whereHas('itemStatus', function ($q) use ($request) {
                    if ($request->country)
                        $q->where('item_status', $request->item_code_tab)->whereIn('country_id', $request->country);
                    else
                        $q->where('item_status', $request->item_code_tab);
                });
            }
            $total = $query->get()->count();
            $query                      = $query->withCount(['remarks' => (fn ($q) => $q->whereSubSystem('advertisement')->whereType('item_code'))])->with(['company:id,name,logo', 'country:id,name,iso2', 'productProfileInfo.attachments', 'itemStatus', 'adsets' => function ($query) use ($request) {
                $query->when($request, function ($query) use ($request) {
                    $query->whereBetween(DB::raw('DATE(history_adsets.created_at)'), [$request->start_date, $request->end_date])
                        ->select('bid', 'currency', DB::raw('history_adsets.created_at'));
                });
            }]);


            $query = $query->withCount('campaigns');

            $relationCounts = [
                "historyAdds,history_ads,ad_id",
                "historyAddsets,history_adsets,adset_id",
                "historyCampians,history_campaigns,campaign_id"
            ];
            $query = self::getTotals($query, $request, $relationCounts);
            if ($request->query->has("search_by_id")) {
                $data = $this->searchCode($query, $request, [], false, "pcode", false, false);
                return $getCount ?
                    $total : response()->json(
                        ["result" => true, "total" => $total, "item" => $data]
                    );
            }

            if (!$request->sortBy)
                $query = $query->orderBy(DB::raw('min(created_at)'), 'asc');

            $queryBuilder->query = $query;

            $paginate = $queryBuilder->build()->paginate()->getData();
            $items = self::makeItemCodeAdsArrayAsObject($paginate->data);
            return $getCount ?
                $total : response()->json(
                    ["result" => true, "total" => $total, "items" => $items]
                );
        } catch (\Throwable $th) {
            return response()->json(["result" => false, "code" => $th->getMessage()]);
        }
    }
    private static function makeItemCodeAdsArrayAsObject($items)
    {
        $result_items = [];
        foreach ($items as $item) {
            if (count($item->item_code_history_ads) > 0) {
                $ad_item = $item->item_code_history_ads[0];
                unset($ad_item->ad_id);
                unset($ad_item->server_adset_id);
                unset($ad_item->laravel_through_key);
                $item->item_code_history_ads = $ad_item;
                $result_items[]              = $item;
            }
        }
        return $result_items;
    }
    private static function makeSalesTypeAdsArrayAsObject($items)
    {
        $result_items = [];
        foreach ($items as $item) {
            if (count(
                $item->sales_type_history_ads
            ) > 0) {
                $ad_item = $item->sales_type_history_ads[0];
                unset($ad_item->ad_id);
                unset($ad_item->server_adset_id);
                unset($ad_item->laravel_through_key);
                $item->sales_type_history_ads
                    = $ad_item;
                $result_items[]   = $item;
            }
        }
        return $result_items;
    }


    public static function ItemCodeAdsCalculations($query, Request $request)
    {
        $relation = ["itemCodeHistoryAds" => function ($ads_query) use ($request) {
            $ads_query = Ads::adsCalculationSubQueries($ads_query, $request->start_date, $request->end_date, true)
                ->groupBy("ad_pcode");
            $ads_query = AdvertisementUtil::filterQueryParams($ads_query, $request);
            return $ads_query;
        }, "labels" => function ($subquery) {
            $subquery->whereCurrentStatus('active')->whereType(null)->where('sub_system', 'advertisement');
        }];

        $start_date = Carbon::parse($request->start_date)->toDateString();
        $end_date = Carbon::parse($request->end_date)->toDateString();
        $query = $query->with($relation)->whereHas("itemCodeHistoryAds", function ($ads_query) use ($start_date, $end_date) {
            return $ads_query->whereBetween("data_date", [$start_date, $end_date]);
        });
        $query = $query->selectRaw("count(distinct(platform_id)) as total_platforms");
        $query = $query->selectRaw("count(distinct(application_id)) as total_organizations");
        $query = $query->selectRaw("count(distinct(ad_account_id)) as total_accounts");
        $query = $query->selectRaw("count(distinct(server_campaign_id)) as total_campaigns");
        $query = $query->selectRaw("count(distinct(server_ad_adset_id)) as total_adsets");
        $query = $query->selectRaw("count(distinct(server_ad_id)) as total_ads");
        $query = $query->selectRaw("min(created_at) as first_generated_date");
        $query = $query->selectRaw("min(created_at)");
        $query = $query->selectRaw("GROUP_CONCAT(DISTINCT landing_page_link) as pcode_landing_urls");
        $query = AdvertisementUtil::filterByQueryIds($query, $request, "pcode");
        $query = AdvertisementUtil::filterByConColumns($query, $request);
        return $query;
    }
    public static function ItemCodeProductProfileFitering($query, Request $request)
    {
        if (
            $request->prod_source || $request->prod_sale_goal || $request->prod_style || $request->prod_section
            || $request->prod_new_product_source || $request->prod_cost || $request->prod_max_adver_cost || $request->prod_available_quantity
        ) {


            $query = $query->whereHas("productProfileInfo", function ($query) use ($request) {
                if ($request->prod_source)
                    $query = $query->whereIn('prod_source', $request->prod_source);
                if ($request->prod_sale_goal)
                    $query = $query->whereIn('prod_sale_goal', $request->prod_sale_goal);
                if ($request->prod_style)
                    $query = $query->whereIn('prod_style', $request->prod_style);
                if ($request->prod_section)
                    $query = $query->whereIn('prod_section', $request->prod_section);
                if ($request->prod_new_product_source)
                    $query = $query->whereIn('prod_new_product_source', $request->prod_new_product_source);
                if ($request->prod_work_type)
                    $query = $query->whereIn('prod_work_type', $request->prod_work_type);
                if ($request->prod_cost)
                    $query = AdvertisementUtil::getConditionWhere($request->prod_cost, $query, "prod_cost");
                if ($request->prod_max_adver_cost)
                    $query =  AdvertisementUtil::getConditionWhere($request->prod_max_adver_cost, $query, "prod_max_adver_cost");
                if ($request->prod_available_quantity)
                    $query =  AdvertisementUtil::getConditionWhere($request->prod_available_quantity, $query, "prod_available_quantity");
                return $query;
            });
        }
        return $query;
    }

    public static function SalesTypeAdsCalculations($query, Request $request)
    {
        $relation = ["SalesTypeHistoryAds" => function ($ads_query) use ($request) {
            $ads_query = Ads::adsCalculationSubQueries($ads_query, $request->start_date, $request->end_date, false)->groupBy('sales_type');
            $ads_query = AdvertisementUtil::filterQueryParams($ads_query, $request);
            return $ads_query;
        }, "labels" => function ($subquery) {
            $subquery->whereCurrentStatus('active')->whereType('sales_type')->where('sub_system', 'advertisement');
        }];



        $start_date = Carbon::parse($request->start_date)->toDateString();
        $end_date = Carbon::parse($request->end_date)->toDateString();
        $query = $query->with($relation)->whereHas("SalesTypeHistoryAds", function ($ads_query) use ($start_date, $end_date) {
            return $ads_query->whereBetween("data_date", [$start_date, $end_date]);
        });
        $query = $query->selectRaw("count(distinct(platform_id)) as total_platforms");
        $query = $query->selectRaw("count(distinct(application_id)) as total_organizations");
        $query = $query->selectRaw("count(distinct(ad_account_id)) as total_accounts");
        $query = $query->selectRaw("count(distinct(server_campaign_id)) as total_campaigns");
        $query = $query->selectRaw("count(distinct(server_ad_adset_id)) as total_adsets");
        $query = $query->selectRaw("count(distinct(server_ad_id)) as total_ads");
        $query = $query->selectRaw("min(created_at) as first_generated_date");
        $query = $query->selectRaw("min(created_at)");
        $query = $query->selectRaw("GROUP_CONCAT(DISTINCT landing_page_link) as pcode_landing_urls");
        $query = AdvertisementUtil::filterByQueryIds($query, $request, "sales_type");
        $query = AdvertisementUtil::filterByConColumns($query, $request);
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
            $queryBuilder = new UriQueryBuilder(new Platform(), $request);
            $queryBuilder->itemsPerPage = 20;
            $query = $queryBuilder->query->select(["id", "code", "platform_name", "platform_key", "advertisement_status"]);
            $query = AdvertisementUtil::countAndAdsCalculations(
                $query,
                $request,
                "platform_id",
                "platforms.code",
                ["total_companies", "total_products", "total_platforms"],
                ["labels" => function ($subquery) {
                    $subquery->whereCurrentStatus('active')->where('sub_system', 'advertisement');
                }]
            );
            $status = strtolower($request->status) ?? null;
            if ($status) {
                $query = $query->where("advertisement_status", $status);
            }
            $searchInColumns = ["code", "platform_name", "platform_key"];
            $query = $this->searchContent($query, $searchInColumns, $request->input('searchContent'));
            $total = $query->get()->count();
            if ($getCount) {
                return $total;
            }
            $relationCounts = [
                "adThroughConnection,history_ads,ad_id",
                "adsetThroughConnection,history_adsets,adset_id",
                "campaignThroughConnection,history_campaigns,campaign_id"
            ];
            $query = self::getTotals($query, $request, $relationCounts);
            $query = $query->withCount(['remarks' => (fn ($q) => $q->whereSubSystem('advertisement'))]);
            // ->with(['adsets' => function ($query) use ($request) {
            //     $query->when($request, function ($query) use ($request) {
            //         if ($request->start_date && $request->end_date)
            //             $query->whereBetween(DB::raw('DATE(history_adsets.created_at)'), [$request->start_date, $request->end_date])
            //                 ->select('bid', 'currency');
            //         else
            //             $query->where(DB::raw('DATE(history_adsets.created_at)'), date("Y-m-d"))->select('bid', 'currency');
            //     });
            // }])
            $query = $query->withSum('adAccontThroughConnections as total_ad_account_balance', 'ad_account_balance');


            $query = $query->withCount(['adAccontThroughConnections as total_active_ad_account' => function ($adAccount) {
                return $adAccount->select(DB::raw('count(distinct(connections.server_account_id))'))->whereStatus("ACTIVE");
            }]);
            $query = $query->withCount(['adAccontThroughConnections as total_inactive_ad_account' => function ($adAccount) {
                return $adAccount->select(DB::raw('count(distinct(connections.server_account_id))'))->whereStatus("INACTIVE");
            }]);
            $query = $query->withCount('campaignThroughConnection')->with('connection_date', function ($query) {
                return $query->select(['platform_id', 'created_at'])->oldest();
            });

            if ($request->query->has("search_by_id")) {
                $data = $this->searchCode($query, $request);
                return $getCount ?
                    $total : response()->json(
                        ["result" => true, "total" => $total, "item" => $data]
                    );
            }

            $query = $query->orderBy('created_at', 'asc');
            $queryBuilder->query = $query;
            $paginate = $queryBuilder->build()->paginate()->getData();
            $items = Ads::makeAdsArrayAsObject($paginate->data);
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
            $queryBuilder = new UriQueryBuilder(new Application(), $request);
            $queryBuilder->itemsPerPage = 20;
            $query = $queryBuilder->query->select(["id", "code", "name", "advertisement_status"]);
            $query = AdvertisementUtil::countAndAdsCalculations(
                $query,
                $request,
                "application_id",
                "applications.code",
                ["total_companies", "total_products", "total_platforms", "total_organizations"],
                ["labels" => function ($subquery) {
                    $subquery->whereCurrentStatus('active')->where('sub_system', 'advertisement');
                }]
            );
            $status = strtolower($request->status) ?? null;
            if ($status) {
                $query = $query->where("advertisement_status", $status);
            }
            $searchInColumns = ["code", "name", "code", "whereHasOne,platform,platform_name"];
            $query = $this->searchContent($query, $searchInColumns, $request->input('searchContent'));
            $total = $query->get()->count();
            if ($getCount) {
                return $total;
            }
            $relationCounts = [
                "adThroughConnection,history_ads,ad_id",
                "adsetThroughConnection,history_adsets,adset_id",
                "campaignThroughConnection,history_campaigns,campaign_id"
            ];
            $query = self::getTotals($query, $request, $relationCounts);
            $query = $query->withCount(['remarks' => (fn ($q) => $q->whereSubSystem('advertisement'))])->with([
                'adsets' => function ($query) use ($request) {
                    $query->when($request, function ($query) use ($request) {
                        if ($request->start_date && $request->end_date)
                            $query->whereBetween(DB::raw('DATE(history_adsets.created_at)'), [$request->start_date, $request->end_date])
                                ->select('bid', 'currency');
                        else
                            $query->where(DB::raw('DATE(history_adsets.created_at)'), date("Y-m-d"))->select('bid', 'currency');
                    });
                }
            ]);

            $query = $query->withCount(['adAccontThroughConnections as total_active_ad_account' => function ($adAccount) {
                return $adAccount->select(DB::raw('count(distinct(connections.server_account_id))'))->whereStatus("ACTIVE");
            }]);
            $query = $query->withCount(['adAccontThroughConnections as total_inactive_ad_account' => function ($adAccount) {
                return $adAccount->select(DB::raw('count(distinct(connections.server_account_id))'))->whereStatus("INACTIVE");
            }]);
            $query = $query->withCount('campaignThroughConnection')->with('connection_date', function ($query) {
                return $query->select(['application_id', 'created_at'])->oldest();
            });

            if ($request->query->has("search_by_id")) {
                $data = $this->searchCode($query, $request);
                return $getCount ?
                    $total : response()->json(
                        ["result" => true, "total" => $total, "item" => $data]
                    );
            }

            $query = $query->orderBy('created_at', 'asc');
            $queryBuilder->query = $query;
            $paginate = $queryBuilder->build()->paginate()->getData();
            $items = Ads::makeAdsArrayAsObject($paginate->data);
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
        $time_start = microtime(true);
        $counts = [];
        $counts['country'] = $this->fetchCountryItems($request, true);

        $counts['company'] = $this->fetchCompanies($request, true);

        $counts['project'] = $this->fetchProjects($request, true);

        $counts['item_code'] = $this->fetchItemCode($request, true);

        $counts['platform'] = $this->fetchPlatforms($request, true);

        $counts['organization'] = $this->fetchOrganizations($request, true);

        $addAccount = new AdAccount();
        $counts['ad_account'] = $addAccount->fetchAdAccounts($request, true);

        $compaign = new Campaign();
        $counts['campaign'] = $compaign->fetchCampaigns($request, true);

        $adset = new Adset();
        $counts['ad_set'] = $adset->fetchAdsets($request, true);

        $ad = new Ads();
        $counts['sales_type'] = $ad->fetchSalesTypeITems($request, true);
        $counts['ad'] = $ad->fetchAdItems($request, true);
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        return response()->json([
            'counts' => $counts, 'result' => true, 'response_time' => $time,
        ]);
    }

    public static function findParentModel(Model $model, $id)
    {
        return $model->whereId($id)->first();
    }

    /**
     * store and export connection template,
     * @param  string|  $id
     * @param  stdClass | $request
     * @return json
     */
    public function exportAndStoreConnectionTemplate(Request $request, $id)
    {
        $request->all();
        try {
            DB::beginTransaction();
            $connection = new Connection();
            $account = AdAccountModel::find($id);
            if (empty($account)) {
                return response()->json(["result" => false, "message" => "selected_ad_account_not_found", "data" => []]);
            }
            $selectedProducts = json_decode($request->selected_products);
            //1. find the ad accound and its grand parents related,
            $accountModel = AdAccountModel::find($id);
            //2. find parent application
            $application = self::findParentModel(new Application(), $accountModel->application_id);
            //3. find parent platform
            $platform = self::findParentModel(new Platform(), $application->platform_id);
            //4. find the rest of data
            $ispp = Ispp::inRandomOrder()->whereStatus('completed')->first();
            //5. connection collection
            $connectionCollections = collect([]);
            foreach ($selectedProducts as $product) {
                $attributes = $request->only($connection->getFillable());
                $attributes["ad_account_id"] = $account->id;
                $attributes["server_account_id"] = $account->account_id;
                $attributes["code"] = rand(1000, 999999999);
                $attributes["pname"] = $product->code;
                $attributes["platform_id"] = $platform->id;
                $attributes["sales_type"] = "Single Sale";
                $attributes['application_id'] = $application->id;
                $attributes['ispp_id'] = $ispp->id;
                $connection = Connection::create($attributes);
                ConnectionRepository::generateLink($connection, '', $request);
                $connectionCollections->add($connection);
            }
            //6. export the template
            $excel_sheet_url = self::generateConnectionExcelSheets($connectionCollections);
            DB::commit();
            return response()->json($excel_sheet_url);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    /**
     * export connection template
     * @param  collection | $collections
     * @return string
     */
    public static function generateConnectionExcelSheets(Collection $collections)
    {
        Excel::store(
            new ConnectionExport($collections),
            'export-excel-files/connection-template.xlsx',
            'public'
        );
        return (env("APP_URL") . Storage::url('export-excel-files/connection-template.xlsx'));
    }

    /**
     * will validate connection data
     * @param  Request | $request
     * @return array | json
     */
    public static function validateAdIds(Request $request)
    {
        try {
            $rows = json_decode($request->rows);
            $adErrors = [];
            $connectionData = [];
            $rows = array_column($rows, "ad_id", "index");
            foreach ($rows as $index => $adId) {
                $conn = Connection::where('server_ad_id', $adId)->first();
                if ($conn) {
                    array_push($connectionData, [
                        'code' => $conn->pcode,
                        'id' => $conn->id,
                        'server_account_id' => $conn->server_account_id,
                        'server_campaign_id' => $conn->server_campaign_id,
                        'server_ad_adset_id' => $conn->server_ad_adset_id,
                        'server_ad_id' => $conn->server_ad_id,
                    ]);
                } else {
                    array_push($adErrors, [
                        "row" => $index,
                        "column" => "ad_id",
                        "descriptions" => $adId . " is not found",
                    ]);
                }
            }
            return response()->json(["errors" => $adErrors, "connection_data" => $connectionData]);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 500);
        }
    }

    public function deleteItemCode($request)
    {
        try {
            $otp_code =   OtpCode::where('code', $request->otp_code)->where('item_code', $request->pcode)->first();
            if (!$otp_code) {
                return response()->json('Invalid OTP Code', 500);
            }

            $date =  Carbon::now();
            $isExpired =   OtpCode::where('code', $request->otp_code)->where('expire_at', "<", $date)->first();
            if ($isExpired) {

                return response()->json('OTP Code Expired', 500);
            }
            $relation = ["application", "platform", "adAccount"];

            //delete ad and update server ad status
            $connections = Connection::with($relation)->where("server_ad_id", "!=", null)->where("pcode", $request->pcode)->get();
            $ad_result = [];
            foreach ($connections as $key => $connection) {
                $ad_result[] =  $this->disableAdStatus($connection);
            }

            //delete adset and update server adset status
            $connections2 = Connection::with($relation)->where("server_ad_id", "!=", null)->groupBy('server_ad_adset_id')
                ->whereDoesntHave('historyAdds')->get();

            $adset_result = [];
            foreach ($connections2 as $key => $connection) {
                $adset_result[] =  $this->disableAdsetStatus($connection);
            }
            //delete campaign and update server campaign status
            $connections3 = Connection::with($relation)->where("server_ad_id", "!=", null)->groupBy('server_campaign_id')
                ->whereDoesntHave('adsets')->get();
            $campaign_result = [];
            foreach ($connections3 as $key => $connection) {
                $campaign_result[] =  $this->disableCampaignStatus($connection);
            }

            // delete those tables that is not exist in online sales
            $onlineSales = OnlineSales::whereProductCode($request->pcode)->first();
            $connectionData = Connection::find($request->record_id);
            $this->deleteCCPS($connectionData); // delete country, company, project and sales type when any of them is one record
            if (!$onlineSales) {
                $connectionData->allItemStatus()->delete();
                $this->deleteDataIfNoteExistInOnlineSales($connectionData, $request->pcode);
            }
            $final_result =  array_filter($ad_result,  function ($var) {
                return $var != "success";
            });

            // delete connection that has no ad;
            $connection_result =   $this->deleteUnusedConnection($connectionData, $ad_result, $request->pcode, count($final_result) == 0);
            if (count($final_result) == 0) {
                $date =  Carbon::now();
                OtpCode::where('code', $request->otp_code)->delete();
                OtpCode::where('expire_at', "<", $date)->delete();
            }
            return response()->json(['final_result' => $final_result, 'ad_result' => $ad_result, 'adset_result' => $adset_result, 'campaign_result' => $campaign_result, "connection_result" => 'connection_result']);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json($th->getMessage(), 500);
        }
    }
    // delete country, company, project and sales type when any of them is one record
    public function deleteCCPS($model)
    {
        if (Connection::whereCountryId($model->country_id)->count() == 1) {
            $country = Country::whereHas('connections')->find($model->country_id);
            $country->labels()->detach();
            $country->remarks()->delete();
        }
        if (Connection::whereCompanyId($model->company_id)->count() == 1) {
            $company = Company::whereHas('connections')->find($model->company_id);
            $company->labels()->detach();
            $company->remarks()->delete();
        }
        if (Connection::whereProjectId($model->project_id)->count() == 1) {
            $project = Project::whereHas('connections')->find($model->project_id);
            $project->labels()->detach();
            $project->remarks()->delete();
        }
        if (Connection::whereSalesType($model->sales_type)->count() == 1) {
            $salesType = Connection::whereSalesType($model->sales_type)->first();
            $salesType->SalesTypeLabels()->detach();
            Remark::whereRemarkableType(get_class($salesType))->whereType('sales_type')->delete();
        }
    }
    public function deleteDataIfNoteExistInOnlineSales($connectionData, $pcode)
    {
        $note = $connectionData->onlineSalesNote()->get();
        $connectionData->onlineSalesNote()->delete();
        try {
            $ids = [];
            foreach ($note as $n) {
                $ids[] = $n['id'];
            }
            $ids[] = $pcode;
            $allPath = CloudAttachment::whereIn('attachmentable_id', $ids)->get('path')->toArray();
            $path = [];
            foreach ($allPath as $d) {
                $path[] = $d['path'];
            }
            if ($path) {
                $deleteCloudFile = CloudinaryFileUtils::multiDeleteByPath($path);
                if ($deleteCloudFile)
                    $connectionData->productProfileInfo()->delete();
            }
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
    public function disableAdStatus($connection)
    {
        $result = 'ad';
        $platform_name = $connection->platform->platform_name;
        switch ($platform_name) {
            case 'snapchat':
                $result = Snapchat::disableAdStatus($connection);
                break;
            case 'tiktok':
                $result = TikTok::disableTiktokStatus($connection, 'ad');
                break;
            default:
                return "change_status_not_available";
        }
        if ($result == 'success') {
            $ad_id = $connection->server_ad_id;
            $pcode =  $connection->pcode;
            HistoryAd::where('ad_id', $ad_id)->delete();
            DB::table('remarks')->whereIn('remarkable_id', [$ad_id, $pcode])->delete();
            DB::table('labelables')->whereIn('labelable_id', [$ad_id, $pcode])->delete();
            DB::table('reasonables')->whereIn('reasonable_id', [$ad_id, $pcode])->delete();
        }
        return $result;
    }
    public function disableAdsetStatus($connection)
    {
        # code...
        $result = 'adset';
        $platform_name = $connection->platform->platform_name;
        switch ($platform_name) {
            case 'snapchat':
                $result = Snapchat::disableAdsetStatus($connection);
                break;
            case 'tiktok':
                $result = TikTok::disableTiktokStatus($connection, 'adgroup');
                break;
            default:
                return "change_status_not_available";
        }
        if ($result == 'success') {
            $adset_id = $connection->server_ad_adset_id;
            HistoryAdset::where('adset_id', $adset_id)->delete();
            DB::table('remarks')->where('remarkable_id', $adset_id)->delete();
            DB::table('labelables')->where('labelable_id', $adset_id)->delete();
            DB::table('reasonables')->where('reasonable_id', $adset_id)->delete();
        }
        return $result;
    }
    public function disableCampaignStatus($connection)
    {
        $result = 'campaign';
        $platform_name = $connection->platform->platform_name;
        switch ($platform_name) {
            case 'snapchat':
                $result = Snapchat::disableCampaignStatus($connection);
                break;
            case 'tiktok':
                $result = TikTok::disableTiktokStatus($connection, 'campaign');
                break;
            default:
                return "change_status_not_available";
        }
        if ($result == 'success') {
            $campaign_id = $connection->server_campaign_id;
            HistoryCampaign::where('campaign_id', $campaign_id)->delete();
            DB::table('remarks')->where('remarkable_id', $campaign_id)->delete();
            DB::table('labelables')->where('labelable_id', $campaign_id)->delete();
            DB::table('reasonables')->where('reasonable_id', $campaign_id)->delete();
        }
        return $result;
    }
    public function deleteUnusedConnection($model, $ad_result, $pcode, $final_result)
    {


        // DB::table('remarks')->where('remarkable_id', $pcode)->delete();
        // DB::table('labelables')->where('labelable_id', $pcode)->delete();
        // DB::table('reasonables')->where('reasonable_id', $pcode)->delete();
        $model->labels()->detach();
        $model->remarks()->delete();
        $model->reasonables()->detach();

        // $q = new TempFile();
        // $profileInfo = ProductProfileInfo::where('item_code', $pcode)->first();
        // if ($profileInfo) {
        //     $images = json_decode($profileInfo->getRawOriginal('prod_image'));
        //     foreach ($$images as $path) {
        //         $q = new TempFile();
        //         if ($q->deleteTempfile('public/' . $path));
        //     }
        //     ProductProfileInfo::where('item_code', $pcode)->delete();
        // }

        $connections = Connection::whereDoesntHave('historyAdds')->delete();
        return $connections;
    }
}
