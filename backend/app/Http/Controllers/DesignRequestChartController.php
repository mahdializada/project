<?php

namespace App\Http\Controllers;

use App\Models\DesignRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DesignRequestChartController extends Controller
{
    //
    public function index(Request $request)
    {
        # code...
        $type = $request->filter_type;
        $date = $request->date;

        if ($type == 'week') {
            return    $this->weekFilter($date);
        } elseif ($type == "month") {
            return   $this->monthFilter($date);
        } elseif ($type == "year") {
            return   $this->yearFilter($date);
        } else {
            return   $this->dayFilter($date);
        }
    }

    public function dayFilter($date)
    {

        $date          = Carbon::parse($date);
        $design_request =  DesignRequest::whereDay('created_at', '=', $date)->get();
        // return $design_request;
        for ($i = 0; $i < 24; $i++) {
            # code...
            $waiting[] = 0;
            $inprogress[] = 0;
            $cancelled[] = 0;
            $completed[] = 0;
            $design_reject[] = 0;
            $storyboard_reject[] = 0;
            $labels[] = $i + 1;
            // $labels[] = date("D d M", strtotime(" +" . $i . " day", strtotime($weekStartDate)));
        }


        for ($i = 0; $i < count($design_request); $i++) {
            for ($j = 0; $j < 24; $j++) {
                $search_hour  = $j + 1;
                $date          = Carbon::parse($design_request[$i]->created_at);
                $date = substr($date, 11, 11);
                $hour = substr($date, 0, 2);
                $status = $design_request[$i]->status;

                if ($hour == $search_hour) {
                    if ($status == 'waiting') {
                        $waiting[$j]++;
                    } elseif ($status == 'cancelled') {
                        $cancelled[$j]++;
                    } elseif ($status == 'completed') {
                        $completed[$j]++;
                    } else {
                        $inprogress[$j]++;
                    }
                    $design_reject[$j] += $design_request[$i]->design_reject_count;
                    $storyboard_reject[$j] += $design_request[$i]->storyboard_reject_count;
                }
            }
        }
        return response()->json(['waiting' => $waiting, 'inprogress' => $inprogress, 'cancelled' => $cancelled, 'completed' => $completed, 'design_reject' => $design_reject, 'storyboard_reject' => $storyboard_reject, "labels" => $labels]);
    }



    public function weekFilter($date)
    {
        $date          = Carbon::parse($date);
        $weekStartDate = $date->startOfWeek()->format('Y-m-d');
        $weekEndDate   = $date->endOfWeek()->format('Y-m-d');
        $design_request         = DesignRequest::whereBetween('created_at', [$weekStartDate, $weekEndDate])->get();
        for ($i = 0; $i < 7; $i++) {
            # code...
            $waiting[] = 0;
            $inprogress[] = 0;
            $cancelled[] = 0;
            $completed[] = 0;
            $design_reject[] = 0;
            $storyboard_reject[] = 0;
            $labels[] = date("D d M", strtotime(" +" . $i . " day", strtotime($weekStartDate)));
        }

        for ($i = 0; $i < count($design_request); $i++) {
            for ($j = 0; $j < 7; $j++) {
                $search_date  = $j + 1;
                $created_at = substr($design_request[$i]->created_at, 0, 10);
                $created_at = date("N", strtotime($created_at));
                $status = $design_request[$i]->status;

                if ($created_at == $search_date) {
                    if ($status == 'waiting') {
                        $waiting[$j]++;
                    } elseif ($status == 'cancelled') {
                        $cancelled[$j]++;
                    } elseif ($status == 'completed') {
                        $completed[$j]++;
                    } else {
                        $inprogress[$j]++;
                    }
                    $design_reject[$j] += $design_request[$i]->design_reject_count;
                    $storyboard_reject[$j] += $design_request[$i]->storyboard_reject_count;
                }
            }
        }
        return response()->json(['waiting' => $waiting, 'inprogress' => $inprogress, 'cancelled' => $cancelled, 'completed' => $completed, 'design_reject' => $design_reject, 'storyboard_reject' => $storyboard_reject, "labels" => $labels]);
    }


    public function monthFilter($date)
    {
        $date               = $date;
        $month_name         = date('M', strtotime($date));
        $year               = date('Y', strtotime($date));
        $first_day_of_month = date("Y-m-01", strtotime($date));
        $last_day_of_month  = date("Y-m-t", strtotime($date));
        $month_length       = date("t", strtotime($date));
        $design_request     = DesignRequest::whereBetween('created_at', [$first_day_of_month, $last_day_of_month])->get();
        $month_data         = [];

        $labels         = [];
        // initial value
        for ($i = 0; $i < $month_length; $i++) {
            $labels[]  =  $i + 1;
            // $labels[]  = $month_name . " " . $i + 1 . " " . $year;
            $month_data[]  = 0;
            $waiting[] = 0;
            $inprogress[] = 0;
            $cancelled[] = 0;
            $completed[] = 0;
            $design_reject[] = 0;
            $storyboard_reject[] = 0;
        }
        // count user data
        for ($i = 0; $i < count($design_request); $i++) {
            for ($j = 0; $j < $month_length; $j++) {
                $search_date  = date("Y-m-d", strtotime(" +" . $j . " day", strtotime($first_day_of_month)));
                $created_at = substr($design_request[$i]->created_at, 0, 10);
                $status = $design_request[$i]->status;

                // if ($created_at == $search_date) {
                //     $month_data[$j]++;
                // }

                if ($created_at == $search_date) {
                    if ($status == 'waiting') {
                        $waiting[$j]++;
                    } elseif ($status == 'cancelled') {
                        $cancelled[$j]++;
                    } elseif ($status == 'completed') {
                        $completed[$j]++;
                    } else {
                        $inprogress[$j]++;
                    }
                    $design_reject[$j] += $design_request[$i]->design_reject_count;
                    $storyboard_reject[$j] += $design_request[$i]->storyboard_reject_count;
                }
            }
        }
        return response()->json(['waiting' => $waiting, 'inprogress' => $inprogress, 'cancelled' => $cancelled, 'completed' => $completed, 'design_reject' => $design_reject, 'storyboard_reject' => $storyboard_reject, "labels" => $labels]);
    }

    public function yearFilter($date)
    {

        # code...
        $date               = $date;
        $year               = date('Y', strtotime($date));
        $labels         = [
            "Jan", "Feb", "Mar", "Apr", "May", "Jun",
            "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
        ];
        $design_request              = DB::table('design_requests')->whereYear('created_at', $year)->get();


        for ($i = 0; $i < 12; $i++) {
            # code...
            $waiting[] = 0;
            $inprogress[] = 0;
            $cancelled[] = 0;
            $completed[] = 0;
            $design_reject[] = 0;
            $storyboard_reject[] = 0;
        }

        for ($i = 0; $i < count($design_request); $i++) {
            for ($j = 0; $j < 12; $j++) {
                $search_date  = $j + 1;
                $created_at = substr($design_request[$i]->created_at, 0, 10);
                $created_at = date("m", strtotime($created_at));
                $status = $design_request[$i]->status;

                if ($created_at == $search_date) {
                    if ($status == 'waiting') {
                        $waiting[$j]++;
                    } elseif ($status == 'cancelled') {
                        $cancelled[$j]++;
                    } elseif ($status == 'completed') {
                        $completed[$j]++;
                    } else {
                        $inprogress[$j]++;
                    }
                    $design_reject[$j] += $design_request[$i]->design_reject_count;
                    $storyboard_reject[$j] += $design_request[$i]->storyboard_reject_count;
                }
            }
        }
        return response()->json(['waiting' => $waiting, 'inprogress' => $inprogress, 'cancelled' => $cancelled, 'completed' => $completed, 'design_reject' => $design_reject, 'storyboard_reject' => $storyboard_reject, "labels" => $labels]);
    }
}
