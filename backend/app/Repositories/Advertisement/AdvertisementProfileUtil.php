<?php

namespace App\Repositories\Advertisement;

use App\Models\Advertisement\Connection;
use App\Models\Advertisement\HistoryAd;
use App\Models\Advertisement\HistoryAdset;
use App\Models\Advertisement\HistoryCampaign;
use App\Models\Advertisement\Labelable;
use App\Models\Advertisement\Platform;
use App\Models\Advertisement\Reasonable;
use App\Models\Remark;
use App\Repositories\Advertisement\AdvertisementTabs\Ads;
use App\Repositories\Advertisement\AdvertisementTabs\Adset;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use Aws\History;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\DB;

class AdvertisementProfileUtil
{


    public static function ProfileExtaInfo($request)
    {
        if ($request->extra_info) {
            switch ($request->extra_info) {
                case 'label':
                    $label = self::labelInfo($request);
                    break;

                default:
                    # code...
                    break;
            }
            return response()->json($label);
        }

        if (!$request->page) {
            $label = self::labelInfo($request);
            $remark = self::remarkInfo($request);
            $status = self::StatusInfo($request);
            $bid = self::bidAndBudgetInfo($request);
            $related = self::relatedDataInfo($request);
            return response()->json(['labels' => $label, "remarks" => $remark, 'status' => $status, 'bid_and_budget' => $bid, 'related_data' => $related]);
        } elseif ($request->card == "related_ads") {
            $related = self::relatedDataInfo($request);
            return response()->json(['related_data' => $related]);
        } else {
            $bid = self::bidAndBudgetInfo($request);
            return response()->json(['bid_and_budget' => $bid]);
        }
    }
    public static function labelInfo($request, $custom = false)
    {
        $query =  Labelable::where('labelable_id', $request->request_id)->whereSubSystem($request->sub_system ?? 'advertisement')->with('label:id,label,color', 'creator:id,firstname,lastname,image');
        $query = $query->whereBetween(DB::raw('DATE(created_at)'), array($request->start_date, $request->end_date));
        if ($custom)
            $labels = $query->orderBy('created_at', 'desc')->get();
        $labels = $query->orderBy('created_at', 'desc')->get()->groupBy(function ($data) {
            return \Carbon\Carbon::parse($data->created_at)->format('d-M-y') . ',' . $data->created_by . ',' . $data->status;
        })->toArray();
        return $labels;
    }

    public static function StatusInfo($request)
    {
        $model =  self::ColumnSelector($request);
        $query = Reasonable::where('reasonable_id', $request->request_id)->where('reasonable_type', $model['model'])
            ->with([
                'reasons',
                'reasonable:id', 'creator:id,firstname,lastname,image'
            ]);

        $query = $query->whereBetween(DB::raw('DATE(created_at)'), array($request->start_date, $request->end_date))->get()->groupBy(function ($data) {
            return \Carbon\Carbon::parse($data->created_at)->format('d-M-y') . ',' . $data->created_by . ',' . $data->status;
        })->toArray();
        return $query;
    }


    public static function remarkInfo($request)
    {
        $query = Remark::where('remarkable_id', $request->request_id)->whereSubSystem($request->sub_system ?? 'advertisement')
            ->with([
                'user:id,code,firstname,lastname,image',
                'reactions:id,reaction_type,remark_id,user_id',
                'reactions.user:id,firstname,lastname,image',
            ]);
        // ->whereBetween(DB::raw('DATE(created_at)'), array($request->start_date, $request->end_date))
        $query = $query->whereBetween(DB::raw('DATE(created_at)'), array($request->start_date, $request->end_date))->get();
        return $query;
    }

    public static function bidAndBudgetInfo($request, $custom = false)
    {
        // whereBetween('data_date', [$request->start_date, $request->end_date])

        $model =  self::ColumnSelector($request);
        $query = HistoryAdset::whereHas('connections', function ($query) use ($request, $model) {
            return $query->where($model['model_id'], $request->request_id);
        });
        if ($request->model_name == 'ad_set') {
            $query = $query->select(['adset_id', 'data_date', 'created_at', 'bid', 'daily_budget']);
        } else {
            $query = $query->select(['adset_id', 'data_date'])->selectRaw('sum(bid) as bid')->selectRaw('sum(daily_budget) as daily_budget');
        }

        if ($custom) {
            $query = $query->whereBetween('data_date', [$request->start_date, $request->end_date]);
            return $query->get();
        }
        if ($request->model_name != 'ad_set') {
            $query =  $query->groupBy('adset_id');
        }

        return $query->latest('created_at')->paginate(10);
    }

    public  static function ColumnSelector($request)
    {
        $model_id = '';
        switch ($request->model_name) {

            case 'company':
                $model = 'App\Models\Company';
                $model_id = 'company_id';
                break;
            case 'organization':
                $model = 'App\Models\Advertisement\Application';
                $model_id = 'application_id';

                break;
            case 'platform':
                $model = 'App\Models\Advertisement\Platform';
                $model_id = 'platform_id';
                break;
            case 'country':
                $model = 'App\Models\Country';
                $model_id = 'country_id';
                break;

            case 'project':
                $model = 'App\Models\Advertisement\Project';
                $model_id = 'project_id';

                break;
            case 'ad_set':
                $model = 'App\Models\Advertisement\HistoryAdset';
                $model_id = 'server_ad_adset_id';
                break;
            case 'ad':
                $model = 'App\Models\Advertisement\HistoryAd';
                $model_id = 'server_ad_id';

                break;
            case 'campaign':
                $model = 'App\Models\Advertisement\HistoryCampaign';
                $model_id = "server_campaign_id";
                break;
            case 'ad_account':
                $model = 'App\Models\Advertisement\AdAccount';
                $model_id = "ad_account_id";
                break;
            case 'item_code':
                $model = 'App\Models\Advertisement\Connection';
                $model_id = "pcode";
                break;
            case 'sales_type':
                $model = 'App\Models\Advertisement\HistoryAd';
                $model_id = "sales_type";
                break;
        }
        return ['model' => $model, 'model_id' => $model_id];
    }


    public static function relatedDataInfo($request)
    {
        $relations = [
            "connection:id,code,server_ad_id,pcode,generated_link,landing_page_link",
            "platform:platforms.id,platforms.platform_name"
        ];
        if ($request->model_name != 'ad') {
            $model =  self::ColumnSelector($request);
        } else {
            $model =   ['model_id' => 'server_ad_adset_id'];
            $data =  HistoryAd::whereAdId($request->request_id)->first();
            $request->merge([
                "request_id" => $data->server_adset_id,
            ]);
        }
        // whereBetween('data_date', [$request->start_date, $request->end_date])

        $query = HistoryAd::whereHas('connection', function ($query) use ($request, $model) {
            return $query->where($model['model_id'], $request->request_id);
        });
        if ($request->model_name == 'ad') {
            $query = $query->whereNotIn('ad_id', [$data->ad_id]);
        }
        $query = $query->with('connection_date', function ($query) use ($model) {
            return $query->select([$model['model_id'], 'created_at'])->oldest();
        });
        $query = $query->selectRaw('COUNT(CASE WHEN status="INACTIVE" THEN 1 ELSE NULL END) AS inactive_ads, COUNT(CASE WHEN status="ACTIVE" THEN 1 ELSE NULL END) AS active_ads');

        $query =  $query->groupBy("ad_id");
        $query = $query->with($relations);
        $query = self::adsCalculationSubQueries($query);
        return $query->latest('created_at')->paginate(10);
    }

    public static function adsCalculationSubQueries($query,  $hide_columns = false)
    {
        // $start_date             = Carbon::parse($start_date)->toDateString();
        // $end_date               = Carbon::parse($end_date)->toDateString();
        $none_countable_columns = $hide_columns ? ["server_adset_id", "currency", "ad_pcode"] : HistoryAd::nonCountableColumns();
        $query                  = $query->select($none_countable_columns);
        $columns                = HistoryAd::getCountableRawColumns();
        foreach ($columns as $column) {
            $query = $query->selectRaw("SUM($column) as $column");
            if ($column == 'spend') {
                $round = AdvertisementUtil::$ROUND;
                $query = $query->selectRaw("round( (sum(spend) / sum(crm_total_orders)), $round)  AS cpo");
            }
        }

        $query = self::extraCalculation($query);
        return $query;
    }

    public static function extraCalculation($query)
    {
        $round = AdvertisementUtil::$ROUND;
        // $query = $query->selectRaw("round( (sum(spend) / sum(crm_total_orders)), $round)  AS cpo");
        $query = $query->selectRaw("round( (sum(spend) / sum(impressions)) * 1000, $round)  AS cpm");
        $query = $query->selectRaw("round( (sum(clicks) / sum(impressions)) * 100, $round)  AS ctr");
        $query = $query->selectRaw("round( (sum(spend) / sum(clicks)) , $round)  AS cpc");
        $query = $query->selectRaw("round( (sum(spend) / sum(result)) * 100, $round)  AS cpp");
        $query = $query->selectRaw("round( (sum(story_opens) / sum(impressions)) * 100, $round)  AS story_open_rate");
        $query = $query->selectRaw("round( (sum(spend) / sum(story_opens)) * 100, $round)  AS cost_per_story_open");
        $query = $query->selectRaw("round( (sum(crm_confirm) / sum(crm_total_orders)) * 100, $round)  AS crm_confirmed_percentage");
        $query = $query->selectRaw("round( (sum(crm_total_sale) - (sum(spend) + sum(crm_product_cost) + sum(crm_delivery_cost))), $round)  AS crm_profit_lose");
        $query = $query->selectRaw("round( (sum(crm_cancelled) / sum(crm_total_orders)) * 100, $round)  AS crm_cancelled_percentage");
        $query = $query->selectRaw("round( (sum(crm_repeated) / sum(crm_total_orders)) * 100, $round)  AS crm_send_back_percentage");
        $query = $query->selectRaw("round( (sum(crm_confirm) / sum(crm_total_pickedup)) * 100, $round)  AS crm_difference");
        $query = $query->selectRaw("round( (sum(crm_logis_deliverd) / sum(crm_total_pickedup)) * 100, $round)  AS crm_delivered_percentage");
        $query = $query->selectRaw("round( (sum(crm_logis_cancelled) / sum(crm_total_pickedup)) * 100, $round)  AS crm_logis_cancelled_percentage");
        $query = $query->selectRaw("round( (sum(crm_logis_deliverd) / sum(crm_total_orders)) * 100, $round)  AS crm_final_percentage");
        $query = $query->selectRaw("round( (sum(spend) + sum(crm_product_cost) + sum(crm_delivery_cost)), $round)  AS crm_total_expense");
        $query = $query->selectRaw("round( ((sum(crm_total_sale) + ( sum(spend) + sum(crm_product_cost) + sum(crm_delivery_cost))) / sum(crm_total_sale)) * 100, $round)  AS crm_profit_percentage");
        $query = $query->selectRaw("round( (sum(crm_product_cost) / sum(crm_total_sale)) * 100, $round)  AS crm_product_cost_percentage");
        $query = $query->selectRaw("round( (sum(crm_delivery_cost) / sum(crm_total_sale)) * 100, $round)  AS crm_delivery_cost_percentage");
        $query = $query->selectRaw("round( (sum(spend) / sum(crm_total_sale)) * 100, $round)  AS crm_ad_cost_percentage");
        $query = $query->selectRaw("round( ((sum(crm_confirm)+ sum(crm_cancelled)) / sum(crm_total_orders)) * 100, $round)  AS crm_percentage");
        $query = $query->selectRaw("round( (sum(ga_engaged_sessions) / sum(ga_sessions)) * 100, $round)  AS ga_engagement_rate_percentage");
        $query = $query->selectRaw("round( ((sum(crm_logis_deliverd) + sum(crm_logis_cancelled)) / sum(crm_total_pickedup)) * 100, $round)  AS crm_logistics_percentage");
        $query = $query->selectRaw("round( (sum(crm_total_orders)/ sum(crm_confirm)) , $round)  AS avg_sales_per_confirm");
        $query = $query->selectRaw("round( (sum(exact_price) * sum(crm_total_orders)) , $round)  AS total_register_sales");
        $query = $query->selectRaw("round( (sum(crm_total_sale)/ sum(crm_total_orders)) , $round)  AS avg_sales_order");
        return $query;
    }

    public static function ProfileTabData($request)
    {
        $total_labels = self::labelInfo($request, true);
        $total_remarks = self::remarkInfo($request);
        $bid_budget = self::bidAndBudgetInfo($request, true);
        $model =  self::ColumnSelector($request);

        $query = HistoryAd::whereBetween('data_date', [$request->start_date, $request->end_date]);
        $query = $query->whereHas('connection', function ($query) use ($request, $model) {
            return $query->where($model['model_id'], $request->request_id);
        });
        $round = AdvertisementUtil::$ROUND;
        $query = $query->selectRaw("round(SUM(spend),$round) as total_spend")->selectRaw("SUM(result) as total_result");
        $query = $query->selectRaw("SUM(crm_total_orders) as crm_total_orders");
        $query = $query->selectRaw("round( (sum(spend) / sum(crm_total_orders)), $round)  AS cpo");
        $query = $query->get();
        $spend =  (float) $query[0]['total_spend'];
        $result =  (float) $query[0]["result"];
        $cpo =  (float) $query[0]["cpo"];
        $crm_order =  (float) $query[0]["crm_total_orders"];
        $total_bid =  (float) $bid_budget[0]["bid"];
        $total_budget =  (float) $bid_budget[0]["daily_budget"];

        return [
            "total_spend" => $spend,
            "cpo" => $cpo,
            "result" => $result,
            "total_orders" => $crm_order,
            "total_bid" => $total_bid,
            "total_budgets" => $total_budget,
            'total_labels' => count($total_labels),
            'total_remarks' => count($total_remarks)
        ];
    }


    public static function groupByGraphBase($query, $request)
    {
        if ($request->graph_base == 'daily')
            $query =  $query->select(DB::raw("data_date as date"))->groupBy('date');
        else  if ($request->graph_base == 'weekly')
            $query =  $query->select(DB::raw("DATE_FORMAT(data_date, '%u') as date"))->groupBy('date');
        else  if ($request->graph_base == 'monthly')
            $query =  $query->select(DB::raw("DATE_FORMAT(data_date,'%Y-%m') as date"))->groupBy('date');
        else  if ($request->graph_base == 'yearly')
            $query =  $query->select(DB::raw("DATE_FORMAT(data_date,'%Y') as date"))->groupBy('date');
        return $query;
    }

    public static function AdvertisementProfileStatistics($request)
    {

        $model =  self::ColumnSelector($request);

        $query = HistoryAd::whereBetween('data_date', [$request->start_date, $request->end_date])
            ->orderBy('data_date', 'asc');
        $query = $query->whereHas('connection', function ($query) use ($request, $model) {
            if ($request->ad_ids && count($request->ad_ids) > 0 && $request->model_name == 'ad') {
                return $query->whereIn('ad_id', $request->ad_ids);
            }
            return $query->where($model['model_id'], $request->request_id);
        });

        if ($request->ad_ids && count($request->ad_ids) > 0 && $request->model_name != 'ad') {
            $query = $query->whereIn('ad_id', $request->ad_ids);
        }

        $query =  self::groupByGraphBase($query, $request);
        $column_name = $request->column_name;
        if (gettype($column_name) == 'array') {
            $data = [];
            foreach ($column_name as $col) {
                $data[] = self::getMultipleLabels($request, $col, clone $query);
            }
            return response()->json($data);
        }

        $columns = HistoryAd::getCountableRawColumns();
        if (in_array($column_name, $columns)) {
            $query = $query->selectRaw("SUM($column_name) as $column_name")->get();
        } else {
            $query =   self::fetchGraphTabStatisticsCalculations($column_name, $query);
        }
        return  response()->json(self::sortData($request, $query, $column_name));
    }

    // get multiple label
    public  static function getMultipleLabels($request, $column_name, $query)
    {
        # code...
        $columns = HistoryAd::getCountableRawColumns();
        if (in_array($column_name, $columns)) {
            $query = $query->selectRaw("SUM($column_name) as $column_name")->get();
        } else {
            $query =   self::fetchGraphTabStatisticsCalculations($column_name, $query);
        }
        return  self::sortData($request, $query, $column_name);
    }
    public function filterByDateType()
    {
        # code...
        // DATE_FORMAT(date, '%Y-%m') AS year_and_month

    }



    public static function fetchGraphTabStatisticsCalculations($column_name, $query)
    {

        $round = AdvertisementUtil::$ROUND;
        switch ($column_name) {
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
            case "ad_attraction_percentage":
                $query = $query->selectRaw("round( (sum(clicks) / sum(six_second_video_views)) * 100, $round)  AS ad_attraction_percentage");
                break;
            case "buyer_percentage":
                $query = $query->selectRaw("round( (sum(crm_total_orders) / sum(clicks)) * 100, $round)  AS buyer_percentage");
                break;

            case "view_quality_percantage":
                $query = $query->selectRaw("round( (sum(six_second_video_views) / sum(two_second_video_views)) * 100, $round)  AS view_quality_percantage");
                break;
            default:
                return response()->json(["message" => "You tab name is invalid!"], 404);
        }
        return  $query->get();
    }



    public static function sortData($request,  $query, $column_name)
    {
        if ($request->graph_base == 'daily') {
            return self::sortDaily($request,  $query, $column_name);
        } else  if ($request->graph_base == 'weekly') {
            return self::sortWeekly($request,  $query, $column_name);
        } else  if ($request->graph_base == 'monthly') {
            return self::sorMonthly($request,  $query, $column_name);
        } else  if ($request->graph_base == 'yearly') {
            return self::sortyearly($request,  $query, $column_name);
        }
    }


    public static function sortDaily($request,  $query, $column_name)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $type = $request->type;
        $date_list = clone $query->pluck('date');

        $i = 0;
        while (strtotime($start_date) <= strtotime($end_date)) {
            if (!in_array($start_date, $date_list->toArray())) {
                $query->push(['date' => $start_date, $column_name => 0]);
            }
            $start_date = date("Y-m-d", strtotime("+" . $i . " days", strtotime($start_date)));
            $i = 1;
        }
        $query =    $query->sortBy('date')->values()->all();
        $query = collect($query);
        $label =  self::formatLabel($request, clone $query);

        $data =  clone $query->pluck($column_name)->map(function ($data) {
            return (float) $data;
        });
        return ['label' => $label, 'data' => $data];
    }
    public static function sortWeekly($request,  $query, $column_name)
    {
        $date = new DateTime($request->start_date);
        $start_date = $date->format("W");
        $date2 = new DateTime($request->end_date);
        $end_date = $date2->format("W");


        $date_list =  clone $query->pluck('date')->map(function ($data) {
            return (int) $data;
        });


        while ($start_date <= $end_date) {
            if (!in_array($start_date, $date_list->toArray())) {
                $query->push(['date' => $start_date, $column_name => 0]);
            }
            $start_date++;
        }
        $query =    $query->sortBy('date')->values()->all();
        $query = collect($query);
        $label = self::formatLabel($request, clone $query);

        $data =  clone $query->pluck($column_name)->map(function ($data) {
            return (float) $data;
        });
        return ['label' => $label, 'data' => $data];
    }

    public static function sorMonthly($request,  $query, $column_name)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;



        $date = new DateTime($request->start_date);
        $start_month = $date->format("Y-m");

        $date_list = clone $query->pluck('date');


        while (strtotime($start_date) <= strtotime($end_date)) {
            if (!in_array($start_month, $date_list->toArray())) {
                $query->push(['date' => $start_date, $column_name => 0]);
            }
            $start_date = date("Y-m-d", strtotime("+" . 1 . " month", strtotime($start_date)));
            $date2 = new DateTime($request->start_date);
            $start_month = $date2->format("Y-m");
        }
        $query =    $query->sortBy('date')->values()->all();
        $query = collect($query);
        $label =  self::formatLabel($request, clone $query);

        $data =  clone $query->pluck($column_name)->map(function ($data) {
            return (float) $data;
        });
        return ['label' => $label, 'data' => $data];
    }

    public static function sortyearly($request,  $query, $column_name)
    {
        $date = new DateTime($request->start_date);
        $start_year = $date->format("Y");
        $date2 = new DateTime($request->start_date);
        $end_year = $date2->format("Y");
        $date_list = clone $query->pluck('date');


        while ($start_year <= $end_year) {
            if (!in_array($start_year, $date_list->toArray())) {
                $query->push(['date' => $start_year, $column_name => 0]);
            }
            $start_year++;
        }
        $query =    $query->sortBy('date')->values()->all();
        $query = collect($query);
        $label =  clone $query->pluck('date')->map(function ($data) {
            return (float) $data;
        });

        $data =  clone $query->pluck($column_name)->map(function ($data) {
            return (float) $data;
        });
        return ['label' => $label, 'data' => $data];
    }
    public static function formatLabel($request, $query)
    {
        if ($request->graph_base == 'weekly') {
            $dataNow =  new DateTime($request->start_date);
            $year = $dataNow->format("Y");

            return $query->pluck('date')->map(function ($label) use ($year) {
                $week_start = new DateTime();
                $week_start->setISODate($year, $label);
                return $week_start->format('d M Y');
            });
        } else if ($request->graph_base == 'monthly')
            return     $query->pluck('date')->map(function ($label) {
                return date("M Y", strtotime($label));
            });
        else
            return     $query->pluck('date')->map(function ($label) {
                return date("D d M", strtotime($label));
            });
    }

    public static function AdInsides($request)
    {
        $model = self::ColumnSelector($request);
        $query = HistoryAd::whereBetween('data_date', [$request->start_date, $request->end_date]);
        $query = $query->whereHas('connection', function ($query) use ($request, $model) {
            return $query->where($model['model_id'], $request->request_id);
        });
        $round = AdvertisementUtil::$ROUND;
        $query = $query->selectRaw("round( (sum(clicks) / sum(six_second_video_views)) * 100, $round)  AS ad_attraction_percentage");
        $query = $query->selectRaw("round( (sum(crm_total_orders) / sum(clicks)) * 100, $round)  AS buyer_percentage");
        $query = $query->selectRaw("round( (sum(six_second_video_views) / sum(two_second_video_views)) * 100, $round)  AS view_quality_percantage");
        $query = $query->get();
        $ad_attraction_percentage =  (float) $query[0]['ad_attraction_percentage'];
        $buyer_percentage         =  (float) $query[0]["buyer_percentage"];
        $view_quality_percantage  =  (float) $query[0]["view_quality_percantage"];

        return [
            "ad_attraction_percentage" => $ad_attraction_percentage,
            "view_quality_percantage" => $view_quality_percantage,
            "buyer_percentage" => $buyer_percentage,
        ];
    }

    public static function canvaComparing($request)
    {

        if ($request->request_id) {
            return self::canvaComparingData($request);
        }
        $relation = [
            'historyAdds:id,ad_id,ad_name as name',
            'historyAddsets:id,adset_id,name',
            'platform:id,platform_name',
            'campaigns:id,campaign_id,name'
        ];
        $select = ['server_campaign_id', 'platform_id', 'server_ad_id', 'server_ad_adset_id', 'id', 'media_link', 'created_at'];
        $query = '';
        $relation = 'connections';
        switch ($request->model_name) {
            case 'ad':
                $query = new HistoryAd();
                $query = $query->select(['id', 'ad_id', 'ad_name as name'])->groupBy('ad_id');
                $relation = 'connection';
                break;
            case 'ad_set':
                $query = new HistoryAdset();
                $query = $query->select(['id', 'adset_id', 'name'])->groupBy('adset_id');
                break;
            case 'campaign':
                $query = new HistoryCampaign();
                $query = $query->select(['id', 'campaign_id', 'name'])->groupBy('campaign_id');

                break;
            case 'platform':
                $query = new Platform();
                $query = $query->select(['id', 'platform_name'])->selectRaw('platform_name as name');
                $relation = 'adThroughConnection';
                break;
            default:
                # code...
                return [];
                break;
        }
        if ($request->model_name != 'platform') {
            $query = $query->whereBetween('data_date', [$request->start_date, $request->end_date]);
        }
        $query = $query->whereHas($relation, function ($query) use ($request) {
            $query = $query->where('media_link', $request->media_link);
            foreach ($request->filters as  $value) {
                //    # code...
                //     if ($value['ad_id']) {
                //         $query = $query->where('ad_id', $value['ad_id']);
                //     } elseif ($value['adset_id']) {
                //         # code...
                //         $query = $query->where('adset_id', $value['adset_id']);
                //     } elseif ($value['campaign_id']) {
                //         $query = $query->where('adset_id', $value['adset_id']);
                //     }
            }
            return $query;
        })->get();
        // $connection = Connection::select($select)->with($relation)->whereCanvaLink('media_link')->get()->groupBy('media_link');
        return response()->json($query);
    }

    public static function canvaComparingData($request)
    {
        $model = self::ColumnSelector($request);
        $query = HistoryAd::whereBetween('data_date', [$request->start_date, $request->end_date]);
        $query = $query->whereHas('connection', function ($query) use ($request, $model) {
            return $query->where($model['model_id'], $request->request_id)->whereMediaLink($request->media_link);
        });

        $query =  self::groupByGraphBase($query, $request);
        $data = [];
        $cols = ['ad_attraction_percentage', 'buyer_percentage', 'view_quality_percantage'];
        foreach ($cols as $col) {
            $temp = self::getMultipleLabels($request, $col, clone $query);
            $temp['column'] = $col;
            $data[] = $temp;
        }
        return response()->json($data);
    }
}
