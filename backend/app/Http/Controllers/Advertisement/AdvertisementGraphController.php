<?php

namespace App\Http\Controllers\Advertisement;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Models\Advertisement\HistoryAd;
use App\Models\Advertisement\Application;
use App\Repositories\Advertisement\AdvertisementProfileUtil;
use App\Repositories\Advertisement\AdvertisementTabs\Ads;
use App\Repositories\Advertisement\AdvertisementUtil;
use Illuminate\Support\Facades\DB;

class AdvertisementGraphController extends Controller
{
    public function fetchGraphProfileData(string $graph_section, Request $request)
    {
        # code...
        try {
            $start_date = Carbon::parse($request->start_date);
            $end_date = Carbon::parse($request->end_date);
            if ($start_date->gt($end_date)) {
                $request->merge([
                    "start_date" => $end_date->toDateString(),
                    "end_date" => $start_date->toDateString()
                ]);
            }
            if ($graph_section == "extra-info") {
                return AdvertisementProfileUtil::ProfileExtaInfo($request);
            }
            if ($graph_section == "total_info") {
                return AdvertisementProfileUtil::ProfileTabData($request);
            }



            //code...
        } catch (\Throwable $th) {
            //throw $th;
            return $th->getMessage();
        }
    }
    public function fetchGraphProfileStatistics(Request $request)
    {

        $request->merge([
            "graph_base" => $this->findDateType($request)
        ]);
        // return $request->all();
        try {
            $start_date = Carbon::parse($request->start_date);
            $end_date = Carbon::parse($request->end_date);
            if ($start_date->gt($end_date)) {
                $request->merge([
                    "start_date" => $end_date->toDateString(),
                    "end_date" => $start_date->toDateString()
                ]);
            }
            return AdvertisementProfileUtil::AdvertisementProfileStatistics($request);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function findDateType($request)
    {
        $diff = date_diff(Carbon::parse($request->start_date), Carbon::parse($request->end_date));

        if ($diff->y >= 2)
            return 'yearly';
        elseif ($diff->y == 1 || $diff->m > 4)
            return 'monthly';
        elseif ($diff->m >= 1 && $diff->m <= 4)
            return 'weekly';
        else
            return 'daily';
    }
    public function fetchGraphData(string $tab_name,  Request $request)
    {

        $start_date = Carbon::parse($request->start_date);
        $end_date = Carbon::parse($request->end_date);
        if ($start_date->gt($end_date)) {
            $request->merge([
                "start_date" => $end_date->toDateString(),
                "end_date" => $start_date->toDateString()
            ]);
        }
        if ($tab_name == "tabs") {
            return $this->fetchAllGraphTabs($request);
        }
        // $request->request->remove("start_date");
        // $request->request->remove("end_date");
        $request->merge([
            "graph_base" => $this->findDateType($request)
        ]);
        $columns = HistoryAd::getCountableRawColumns();
        if (in_array($tab_name, $columns)) {
            return $this->fetchGraphTabStatistics($tab_name, $request);
        }
        return $this->fetchGraphTabStatisticsCalculations($tab_name, $request);
    }


    private function fetchGraphTabStatistics(string $tab_name, Request $request)
    {
        $dates = $this->parseDates($request->dates);
        $query = HistoryAd::query()->whereBetween('data_date', [$request->start_date, $request->end_date]);
        $query = AdvertisementProfileUtil::groupByGraphBase($query, $request);
        $query  = Ads::filterQuery($query, $request);
        $query = $query->selectRaw("SUM($tab_name) as $tab_name")->orderBy("date")->get();
        // $result = collect();

        return AdvertisementProfileUtil::sortData($request, $query, $tab_name);
        // foreach ($dates as $date) {
        //     $item = $spend_list->where("data_date", $date)->first();
        //     if (!$item) {
        //         $result->push([
        //             $tab_name => 0,
        //             "data_date" => $date
        //         ]);
        //     } else {
        //         $result->push($item->toArray());
        //     }
        // }

        // $spend_list = $result->map(fn ($item) => (float) $item[$tab_name])->toArray();
        // $response = [
        //     "label" => $tab_name,
        //     "data" => $spend_list,
        // ];
        // return response()->json($response);
    }

    private function fetchGraphTabStatisticsCalculations(string $tab_name, Request $request)
    {
        $dates = $this->parseDates($request->dates);
        $query = HistoryAd::query()->whereBetween('data_date', [$request->start_date, $request->end_date]);
        $query = AdvertisementProfileUtil::groupByGraphBase($query, $request);
        $query  = Ads::filterQuery($query, $request);
        $round  = AdvertisementUtil::$ROUND;
        switch ($tab_name) {
            case "cpo":
                $query = $query->selectRaw("round( (sum(spend) / sum(crm_total_orders)), $round)  AS cpo");
                break;
            case "cpm":
                $query = $query->selectRaw("round( (sum(spend) / sum(impressions)) * 1000, $round)  AS cpm");
                break;
            case "ctr":
                $query = $query->selectRaw("round( (sum(clicks) / sum(impressions)) * 100, $round)  AS ctr");
                break;
            case "cpc":
                $query = $query->selectRaw("round( (sum(spend) / sum(clicks)), $round)  AS cpc");
                break;
            case "cpp":
                $query = $query->selectRaw("round( (sum(spend) / sum(result)) * 100, $round)  AS cpp");
                break;
            case "story_open_rate":
                $query = $query->selectRaw("round( (sum(story_opens) / sum(impressions)) * 100, $round)  AS story_open_rate");
                break;
            case "cost_per_story_open":
                $query = $query->selectRaw("round( (sum(spend) / sum(story_opens)) * 100, $round)  AS cost_per_story_open");
                break;
            case "crm_confirmed_percentage":
                $query = $query->selectRaw("round( (sum(crm_confirm) / sum(crm_total_orders)) * 100, $round)  AS crm_confirmed_percentage");
                break;
            case "crm_profit_lose":
                $query = $query->selectRaw("round( (sum(crm_total_sale) - (sum(spend) + sum(crm_product_cost) + sum(crm_delivery_cost))), $round)  AS crm_profit_lose");
                break;
            case "crm_cancelled_percentage":
                $query = $query->selectRaw("round( (sum(crm_cancelled) / sum(crm_total_orders)) * 100, $round)  AS crm_cancelled_percentage");
                break;
            case "crm_send_back_percentage":
                $query = $query->selectRaw("round( (sum(crm_repeated) / sum(crm_total_orders)) * 100, $round)  AS crm_send_back_percentage");
                break;
            case "crm_difference":
                $query = $query->selectRaw("round( (sum(crm_confirm) / sum(crm_total_pickedup)) * 100, $round)  AS crm_difference");
                break;
            case "crm_delivered_percentage":
                $query = $query->selectRaw("round( (sum(crm_logis_deliverd) / sum(crm_total_pickedup)) * 100, $round)  AS crm_delivered_percentage");
                break;
            case "crm_logis_cancelled_percentage":
                $query = $query->selectRaw("round( (sum(crm_logis_cancelled) / sum(crm_total_pickedup)) * 100, $round)  AS crm_logis_cancelled_percentage");
                break;
            case "crm_final_percentage":
                $query = $query->selectRaw("round( (sum(crm_logis_deliverd) / sum(crm_total_orders)) * 100, $round)  AS crm_final_percentage");
                break;
            case "crm_total_expense":
                $query = $query->selectRaw("round( (sum(spend) + sum(crm_product_cost) + sum(crm_delivery_cost)), $round)  AS crm_total_expense");
                break;
            case "crm_profit_percentage":
                $query = $query->selectRaw("round( ((sum(crm_total_sale) + ( sum(spend) + sum(crm_product_cost) + sum(crm_delivery_cost))) / sum(crm_total_sale)) * 100, $round)  AS crm_profit_percentage");
                break;
            case "crm_product_cost_percentage":
                $query = $query->selectRaw("round( (sum(crm_product_cost) / sum(crm_total_sale)) * 100, $round)  AS crm_product_cost_percentage");
                break;
            case "crm_delivery_cost_percentage":
                $query = $query->selectRaw("round( (sum(crm_delivery_cost) / sum(crm_total_sale)) * 100, $round)  AS crm_delivery_cost_percentage");
                break;
            case "crm_ad_cost_percentage":
                $query = $query->selectRaw("round( (sum(spend) / sum(crm_total_sale)) * 100, $round)  AS crm_ad_cost_percentage");
                break;
            case "crm_percentage":
                $query = $query->selectRaw("round( ((sum(crm_confirm)+ sum(crm_cancelled)) / sum(crm_total_orders)) * 100, $round)  AS crm_percentage");
                break;
            case "ga_engagement_rate_percentage":
                $query = $query->selectRaw("round( (sum(ga_engaged_sessions) / sum(ga_sessions)) * 100, $round)  AS ga_engagement_rate_percentage");
                break;
            case "crm_logistics_percentage":
                $query = $query->selectRaw("round( ((sum(crm_logis_deliverd) + sum(crm_logis_cancelled)) / sum(crm_total_pickedup)) * 100, $round)  AS crm_logistics_percentage");
                break;
            default:
                return response()->json(["message" => "You tab name is invalid!"], 404);
        }
        $query = $query->get();
        // $result = collect();
        return AdvertisementProfileUtil::sortData($request, $query, $tab_name);
        // foreach ($dates as $date) {
        //     $item = $spend_list->where("data_date", $date)->first();
        //     if (!$item) {
        //         $result->push([
        //             $tab_name => 0,
        //             "data_date" => $date
        //         ]);
        //     } else {

        //         $result->push($item->toArray());
        //     }
        // }
        // $spend_list = $result->map(fn ($item) => (float) $item[$tab_name])->toArray();
        // $response = [
        //     "label" => $tab_name,
        //     "data" => $spend_list,
        // ];
        // return response()->json($response);
    }



    private function parseDates($dates)
    {
        if ($dates) {
            $final_dates = [];
            foreach ($dates as $date) {
                $date = Carbon::parse($date)->toDateString();
                $final_dates[] = $date;
            }
            return $final_dates;
        }

        return [];
    }

    private function fetchAllGraphTabs(Request $request)
    {
        $query  = HistoryAd::query();
        $query  = Ads::adsCalculationSubQueries($query, $request->start_date, $request->end_date, true);
        $query  = Ads::filterQuery($query, $request);
        $sum_of_tabs = $query->first();
        if ($sum_of_tabs) {
            $columns = collect($sum_of_tabs->toArray());
            $columns->forget(["ad_id", "server_adset_id", "currency", "ad_pcode"]);
            $graph_tabs = $columns->map(function ($value, $column) {
                return [
                    "id" => $column,
                    "value" => $value,
                ];
            })->values();
            return response()->json(["tab_items" => $graph_tabs]);
        }
        return response()->json(["tab_items" => []]);
    }



    public function graphActions(string $action,  Request $request)
    {
        switch ($action) {
            case "total_graphs":
                return $this->totalGraphs();
            case "date_graphs":
                return $this->dateGraphs($request->id);
        }
    }

    private function totalGraphs()
    {
        $graphs = Application::withTrashed()->with(["connections", "platform", "adThroughConnection"])->get();
        return response()->json($graphs);
    }

    private function dateGraphs($id)
    {
        if ($id) {
            $ids = explode(",", $id);
            $graphs = Application::whereIn("id", $ids)->forceDelete();
            return response()->json($graphs);
        }
        return response()->json();
    }

    public function fetchAdInsides(Request $request)
    {
        try {
            $start_date = Carbon::parse($request->start_date);
            $end_date = Carbon::parse($request->end_date);
            if ($start_date->gt($end_date)) {
                $request->merge([
                    "start_date" => $end_date->toDateString(),
                    "end_date" => $start_date->toDateString()
                ]);
            }
            return AdvertisementProfileUtil::AdInsides($request);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function canvaComparing(Request $request)
    {
        # code...
        try {
            $start_date = Carbon::parse($request->start_date);
            $end_date = Carbon::parse($request->end_date);
            if ($start_date->gt($end_date)) {
                $request->merge([
                    "start_date" => $end_date->toDateString(),
                    "end_date" => $start_date->toDateString()
                ]);
            }
            return AdvertisementProfileUtil::canvaComparing($request);
        } catch (\Throwable $th) {
            return  response()->json($th->getMessage(), 500);
        }
    }
}
