<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use League\CommonMark\Extension\CommonMark\Node\Inline\Strong;

class UsersChartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('scope:uv')->only(['index', 'show']);
        $this->middleware('scope:uc')->only(['store']);
        $this->middleware('scope:uu')->only(['update']);
        $this->middleware('scope:ud')->only(['destroy']);
    }

    public function index()
    {
        $weekNumber              =  date('W', strtotime('2021-12-25'));
        $start_day_of_week       =  date("Y-m-d", strtotime("-1 week"));
        $end_day_of_week         =  date("Y-m-d", strtotime('+0 day'));

        $month_name = date('M', strtotime("first day of this month"));
        $first_day_of_month = date("Y-m-d", strtotime("first day of this month"));
        // $last_day_of_month  = date("Y-m-d", strtotime("last day of -3 month"));
        // return $lastmonth."===>>".date('Y-m-01',strtotime('-1 month'));
    }

    public function weekFilter(Request $request)
    {
        $date          = Carbon::parse($request->date);
        $weekStartDate = $date->startOfWeek()->format('Y-m-d');
        $weekEndDate   = $date->endOfWeek()->format('Y-m-d');
        $users         = User::whereBetween('created_at', [$weekStartDate, $weekEndDate])->get();
        for ($i = 0; $i < 7; $i++) {
            # code...
            $week_data[]  = 0;
            $categories[] = date("D d M", strtotime(" +" . $i . " day", strtotime($weekStartDate)));
        }

        for ($i = 0; $i < count($users); $i++) {
            for ($j = 0; $j < 7; $j++) {
                $search_date  = $j + 1;
                $created_at = substr($users[$i]->created_at, 0, 10);
                $created_at = date("N", strtotime($created_at));
                if ($created_at == $search_date) {
                    $week_data[$j]++;
                }
            }
        }
        return response()->json(["data" => $week_data, "categories" => $categories]);
    }
    public function monthFilter(Request $request)
    {
        $date               = $request->date;
        $month_name         = date('M', strtotime($date));
        $year               = date('Y', strtotime($date));
        $first_day_of_month = date("Y-m-01", strtotime($date));
        $last_day_of_month  = date("Y-m-t", strtotime($date));
        $month_length       = date("t", strtotime($date));
        $users              = User::whereBetween('created_at', [$first_day_of_month, $last_day_of_month])->get();
        $month_data         = [];
        $categories         = [];
        // initial value
        for ($i = 0; $i < $month_length; $i++) {
            $categories[]  = $month_name . " " . $i + 1 . " " . $year;
            $month_data[]  = 0;
        }
        // count user data
        for ($i = 0; $i < count($users); $i++) {
            for ($j = 0; $j < $month_length; $j++) {
                $search_date  = date("Y-m-d", strtotime(" +" . $j . " day", strtotime($first_day_of_month)));
                $created_date = substr($users[$i]->created_at, 0, 10);
                if ($created_date == $search_date) {
                    $month_data[$j]++;
                }
            }
        }
        return response()->json(["data" => $month_data, "categories" => $categories]);
    }

    public function yearFilter(Request $request)
    {

        # code...
        $year_data          = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $date               = $request->date;
        $year               = date('Y', strtotime($date));
        $categories         = [
            "Jan " . $year, "Feb " . $year, "Mar " . $year, "Apr " . $year, "May " . $year, "Jun " . $year,
            "Jul " . $year, "Aug " . $year, "Sep " . $year, "Oct " . $year, "Nov " . $year, "Dec " . $year
        ];
        $users              = DB::table('users')->whereYear('created_at', $year)->get();


        for ($i = 0; $i < count($users); $i++) {
            for ($j = 0; $j < 12; $j++) {
                $search_date  = $j + 1;
                $created_at = substr($users[$i]->created_at, 0, 10);
                $created_at = date("m", strtotime($created_at));
                if ($created_at == $search_date) {
                    $year_data[$j]++;
                }
            }
        }
        return response()->json(["data" => $year_data, "categories" => $categories]);
    }


    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
