<?php

namespace App\Http\Controllers\Advertisement;

use App\Http\Controllers\Controller;
use App\Models\Advertisement\AdAccount;
use App\Models\Advertisement\Application;
use App\Models\Advertisement\Connection;
use App\Models\Advertisement\HistoryAd;
use App\Models\Advertisement\HistoryAdset;
use App\Models\Advertisement\HistoryCampaign;
use App\Models\Advertisement\Platform;
use App\Models\Advertisement\Project;
use App\Models\Company;
use App\Models\Country;
use Carbon\Carbon;
use Illuminate\Http\Request;
use  Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AdvertisementHistoryController extends Controller
{
    //
    public function budgetHistory(Request $request)
    {

        switch ($request->model) {
            case 'country':
                return $this->tabBudgetHistory($request, 'country_id');
            case 'company':
                return $this->tabBudgetHistory($request, 'company_id');
            case 'project':
                return $this->tabBudgetHistory($request, 'project_id');
            case 'sales_type':
                return $this->tabBudgetHistory($request, 'sales_type');
            case 'item_code':
                return $this->tabBudgetHistory($request, 'pcode');
            case 'platform':
                return $this->tabBudgetHistory($request, 'platform_id');
            case 'organization':
                return $this->tabBudgetHistory($request, 'application_id');
            case 'ad':
                return $this->tabBudgetHistory($request, 'ad_account_id');
            case 'campaign':
                return $this->tabBudgetHistory($request, 'campaign_id');
            case 'ad_set':
                return $this->tabBudgetHistory($request, 'server_ad_adset_id');

            default:
                # code...
                break;
        }
    }

    public function tabBudgetHistory($request, $condition_name)
    {
        $select = ['adset_id', 'currency', 'data_date', 'name', 'server_campaign_id', 'daily_budget', 'data_timestamp'];
        $query = HistoryAdset::with(['platform:platform_id,platform_name', 'adAccount:account_id,name']);
        if ($request->model == "campaign") {
            $query = $query->whereHas('campaign', function ($query) use ($request) {
                return $query->where('campaign_id', $request->item_id);
            });
        } elseif ($request->model == 'ad_set') {
            $query = $query->where('adset_id', $request->item_id);
        } elseif ($request->model == "ad") {
            $query = $query->whereHas('adsetAds', function ($query) use ($request) {
                return $query->where('ad_id', $request->item_id);
            });
        } elseif ($request->model == "item_code") {
            $query = $query->whereHas('connections', function ($query) use ($request) {
                return $query->where('pcode', $request->item_id);
            });
        } elseif ($request->model == "sales_type") {
            $query = $query->whereHas('connections', function ($query) use ($request) {
                return $query->where('sales_type', $request->item_id);
            });
        }

        if ($request->start_date && $request->end_date) {
            $start_date = Carbon::parse($request->start_date);
            $end_date = Carbon::parse($request->end_date);
            if ($start_date->gt($end_date)) {
                $request->merge([
                    "start_date" => $end_date->toDateString(),
                    "end_date" => $start_date->toDateString(),
                ]);
            }
            $query = $query->whereBetween('data_date', [$request->start_date, $request->end_date]);
        } else {
            $query = $query->where('data_date', date("Y-m-d"));
        }
        $totals = $this->budgetAndSpendTotal($request, clone  $query);

        $budgets = $query->select($select)->orderBy('data_date', 'desc')->get();
        return response()->json(['budgets' => $budgets,  "extra_data" => $totals]);
    }
    public function budgetAndSpendTotal($request, $query)
    {
        $total_spend = 0;
        if ($request->start_date && $request->end_date) {

            $spends = $query->with('adsetAds', function ($query) use ($request) {
                return $query->whereBetween('data_date', [$request->start_date, $request->end_date])->select(['ad_id', 'adset_id', 'spend', 'data_date', 'server_adset_id'])->selectRaw('sum(spend) as total_spend');
            })->groupBy('adset_id')->get()->toArray();
        } else {
            $spends = $query->with('adsetAds', function ($query) use ($request) {
                return $query->where('data_date', date("Y-m-d"))->select(['ad_id', 'adset_id', 'spend', 'data_date', 'server_adset_id'])->selectRaw('sum(spend) as total_spend');
            })->groupBy('adset_id')->get()->toArray();
        }
        foreach ($spends as $adset) {
            $total_spend += $adset['adset_ads'][0]['total_spend'] ?? 0;
        }
        if ($request->model == "campaign") {
            $budgets = $query->whereHas('campaign', function ($query) use ($request) {
                return $query->where('campaign_id', $request->item_id);
            })->select('daily_budget', 'currency')->selectRaw('sum(daily_budget) as total_budget')->get()->toArray();
        } else {
            $budgets = $query->select('daily_budget', "currency")->selectRaw('sum(daily_budget) as total_budget')->get()->toArray();
        }
        $total_budgets = 0;
        foreach ($budgets as $budget) {
            $total_budgets += (float) $budget['total_budget'] ?? 0;
        }
        return ["total_spend" => round($total_spend, 3), 'total_budget' => $total_budgets, "extra_data" => $spends, 'extra_budget' => $budgets];
    }

    public function budgetHistoryDetails(Request $request)
    {
        $query = HistoryAd::where('server_adset_id', $request->adset_id)->where('data_date', $request->date)->select(['ad_name',  'spend', 'currency'])->get();
        return $query;
    }





    public static function fetchCampaignsBidsHistory(Request $request)
    {

        try {
            $campaign_id = $request->campaign_id;
            $builder =  HistoryCampaign::where('campaign_id', $campaign_id);
            $history = $builder->with([
                'adAccount:id,account_id,name,currency',
                'connections:server_campaign_id,platform_id', 'connections.platform:id,platform_name',
                "campaignAdsets" =>
                function ($query) use ($request) {
                    $query->when($request, function ($query) use ($request) {
                        if ($request->start_date && $request->end_date)
                            $query->whereBetween('data_date', [$request->start_date, $request->end_date])
                                ->select('id', 'bid', 'currency', 'name', 'adset_id', 'server_campaign_id', 'data_date');
                        else
                            $query->where('data_date', date("Y-m-d"))->select('id', 'bid', 'currency', 'name', 'adset_id', 'server_campaign_id', 'data_date');
                    });
                }
            ]);

            $history = $builder->first();
            return response()->json(['data' => $history], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 500);
        }
    }

    public function fetchBidHistory(Request $request)
    {

        if ($request->adset_id)
            return $this->fetchAdsetBidHistory($request);
        try {



            $builder =  HistoryCampaign::groupBy('name')->whereHas('connections', function ($query) use ($request) {
                if ($request->country_id)
                    $query->where('country_id', $request->country_id);
                else if ($request->company_id)
                    $query->where('company_id', $request->company_id);
                else if ($request->project_id)
                    $query->where('project_id', $request->project_id);
                else if ($request->platform_id)
                    $query->where('platform_id', $request->platform_id);
                else if ($request->application_id)
                    $query->where('application_id', $request->application_id);
                else if ($request->ad_account_id)
                    $query->where('ad_account_id', $request->ad_account_id);
                else if ($request->item_code_id)
                    $query->where('pcode', $request->item_code_id);
                else if ($request->sales_type_id)
                    $query->where('sales_type', $request->sales_type_id);
            });
            if ($request->start_date && $request->end_date)
                $builder = $builder->whereBetween(DB::raw('DATE(created_at)'), [$request->start_date, $request->end_date]);
            else
                $builder = $builder->whereDate('created_at', date("Y-m-d"));
            $history = $builder->with([
                'adAccount:id,account_id,name,currency',
                'connections:server_campaign_id,platform_id,pname', 'connections.platform:id,platform_name',
                "campaignAdsets" =>
                function ($query) use ($request) {
                    $query->when($request, function ($query) use ($request) {
                        if ($request->start_date && $request->end_date)
                            $query->whereBetween('data_date', [$request->start_date, $request->end_date])
                                ->select('id', 'bid', 'name', 'adset_id', 'server_campaign_id', 'data_date', 'currency');
                        else
                            $query->where('data_date', date("Y-m-d"))->select('id', 'bid', 'name', 'currency', 'adset_id', 'server_campaign_id', 'data_date');
                    });
                }
            ]);

            $history = $builder->orderBy('created_at', 'desc')->get();
            return response()->json(['data' => $history], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 500);
        }
    }


    public function fetchAdsetBidHistory(Request $request)
    {
        try {

            $builder =  HistoryAdset::where('adset_id', $request->adset_id);
            if ($request->start_date && $request->end_date)
                $builder = $builder->whereBetween(DB::raw('DATE(created_at)'), [$request->start_date, $request->end_date]);
            else
                $builder = $builder->whereDate('created_at', date("Y-m-d"));

            $history = $builder->orderBy('created_at', 'desc')->get();
            return response()->json(['data' => $history], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 500);
        }
    }
    public function tabCount(Request $request)
    {
        return $request;
        # code...
        //     if ($request->country_id)
        //     $query->where('country_id', $request->country_id);
        // else if ($request->company_id)
        //     $query->where('company_id', $request->company_id);
        // else if ($request->project_id)
        //     $query->where('project_id', $request->project_id);
        // else if ($request->platform_id)
        //     $query->where('platform_id', $request->platform_id);
        // else if ($request->application_id)
        //     $query->where('application_id', $request->application_id);
        // else if ($request->ad_account_id)
        //     $query->where('ad_account_id', $request->ad_account_id);
        // else if ($request->item_code_id)
        //     $query->where('id', $request->item_code_id);

    }
    public function bidCount()
    {
        # code...

    }
}
