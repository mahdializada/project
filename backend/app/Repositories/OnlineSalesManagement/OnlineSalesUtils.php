<?php

namespace App\Repositories\OnlineSalesManagement;

use App\Models\Advertisement\Labelable;
use App\Models\Advertisement\Project;
use App\Models\Advertisement\Reasonable;
use App\Models\Company;
use App\Models\Country;
use App\Models\OnlineSalesManagement\OnlineSales;
use App\Models\Remark;

use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Support\Facades\DB;

class OnlineSalesUtils
{
    public static  function ExtraInfo($request)
    {
        $labels = self::labelInfo($request);
        $remarks = self::remarkInfo($request);
        $status = self::statusInfo($request);
        $getStatisticsInfo = self::getStatisticsInfo($request);
        return response()->json(['labels' => $labels, "remarks" => $remarks, 'status' => $status, 'graph_data' => $getStatisticsInfo]);
    }


    public static function labelInfo($request)
    {
        $query =  Labelable::where('labelable_id', $request->request_id)->whereSubSystem($request->sub_system ?? 'online_sales')->with('label:id,label,color', 'creator:id,firstname,lastname,image');
        $query = $query->whereBetween(DB::raw('DATE(created_at)'), array($request->start_date, $request->end_date));
        $labels = $query->orderBy('created_at', 'desc')->get()->groupBy(function ($data) {
            return Carbon::parse($data->created_at)->format('d-M-y') . ',' . $data->created_by . ',' . $data->status;
        })->toArray();
        return $labels;
    }

    public static function statusInfo($request)
    {

        $query = Reasonable::where('reasonable_id', $request->request_id)->where('reasonable_type', get_class(new OnlineSales()))
            ->with([
                'reasons',
                'reasonable:id', 'creator:id,firstname,lastname,image'
            ]);

        $query = $query->whereBetween(DB::raw('DATE(created_at)'), array($request->start_date, $request->end_date))->get()->groupBy(function ($data) {
            return Carbon::parse($data->created_at)->format('d-M-y') . ',' . $data->created_by . ',' . $data->status;
        })->toArray();
        return $query;
    }


    public static function remarkInfo($request)
    {
        $query = Remark::where('remarkable_id', $request->request_id)->whereSubSystem($request->sub_system ?? 'online_store')
            ->with([
                'user:id,code,firstname,lastname,image',
                'reactions:id,reaction_type,remark_id,user_id',
                'reactions.user:id,firstname,lastname,image',
            ]);
        $query = $query->whereBetween(DB::raw('DATE(created_at)'), array($request->start_date, $request->end_date))->get();
        return $query;
    }

    public static function getStatisticsInfo($request)
    {
        $key = $request->model_name;
        if ($key == 'item_code')
            return  ['data' => [], "labels" => []];

        if ($key == 'country' || $key == 'company' || $key == 'project')
            $key = $key . '_id';


        $query = OnlineSales::whereBetween(DB::raw('DATE(created_at)'), [$request->start_date, $request->end_date])->where(
            $key,
            $request->request_id
        );
        $query = $query->groupBy($key);
        return self::sortDataForGraph($key, $request, $query);
    }


    public static function sortDataForGraph($key, $request, $query)
    {
        $start_date = new DateTime($request->start_date);
        $end_date = new DateTime($request->end_date);
        $interval = $end_date->diff($start_date)->days;

        $type = 'day';
        if ($interval >= 0 && $interval <= 31) {
            $query =  $query->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as date"));
            $period = new DateInterval('P1D');
        } else if ($interval >= 31 && $interval <= 92) {
            $query =  $query->select(DB::raw("WEEK(created_at) as date"));
            $period = new DateInterval('P1W');
            $end_date->modify('next Sunday');
            $type = 'weak';
        } else if ($interval > 32 && $interval <= 365) {
            $query =  $query->select(DB::raw("MONTH(created_at) as date"));
            $period = new DateInterval('P1M');
            $end_date =  $end_date->modify('last day of this month');
            $type = 'month';
        } else {
            $query =  $query->select(DB::raw("YEAR(created_at) as date"));
            $period = new DateInterval('P1Y');
            $type = 'year';
        }

        $query = $query->selectRaw("sum(case when $key = '$request->request_id' then 1 else 0 end) as total_products");
        $records = $query->groupBy('date')->get();
        $data = [];
        $labels = [];

        do {
            $formatted_date =  $start_date->format('Y-m-d');
            $start_date =   $start_date->add($period);
            $temp_val = 0;
            foreach ($records as  $record) {
                if ($type == 'day') {
                    if ($record['date'] == $start_date->format('Y-m-d')) {
                        $temp_val = $record['total_products'];
                    }
                } else if ($type == 'weak') {
                    $week_number = $start_date->format('W');
                    if ($record['date'] == $week_number) {
                        $temp_val = $record['total_products'];
                    }
                } else if ($type == 'month') {
                    $month_number = date('M', strtotime($formatted_date));
                    if ($record['date'] == $month_number) {
                        $temp_val = $record['total_products'];
                    }
                } else if ($type == 'year') {
                    $year = date('Y', strtotime($formatted_date));
                    if ($record['date'] == $year) {
                        $temp_val = $record['total_products'];
                    }
                }
            }
            $data[]   = $temp_val;
            $labels[] = $start_date->format('d M Y');
        } while ($start_date <= $end_date);

        // foreach ($date_range as  $date) {
        //     $temp_val = 0;
        //     $formatted_date = $date->format("Y-m-d");

        //     foreach ($records as  $record) {
        //         if ($type == 'day') {
        //             if ($record['date'] == $date->format('Y-m-d')) {
        //                 $temp_val = $record['total_products'];
        //             }
        //         } else if ($type == 'weak') {
        //             $week_number = $date->format('W');
        //             if ($record['date'] == $week_number) {
        //                 $temp_val = $record['total_products'];
        //             }
        //         } else if ($type == 'month') {
        //             $month_number = date('M', strtotime($formatted_date));
        //             if ($record['date'] == $month_number) {
        //                 $temp_val = $record['total_products'];
        //             }
        //         } else if ($type == 'year') {
        //             $year = date('Y', strtotime($formatted_date));
        //             if ($record['date'] == $year) {
        //                 $temp_val = $record['total_products'];
        //             }
        //         }
        //     }
        //     $data[]   = $temp_val;
        //     $labels[] = $date->format('d M Y');
        // }

        return ['data' => $data, "labels" => $labels, 'records' => $records];
    }
}
