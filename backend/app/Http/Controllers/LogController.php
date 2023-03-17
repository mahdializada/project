<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use SessionLog;
use Illuminate\Support\Str;


use function PHPUnit\Framework\fileExists;
use function Symfony\Component\VarDumper\Dumper\esc;

class LogController extends Controller
{
    private $sessionLog;
    public function __construct()
    {
        $this->sessionLog = new SessionLog();

        $this->middleware('scope:uav')->only(['index', 'show']);
        $this->middleware('scope:uac')->only(['store']);
        $this->middleware('scope:uau')->only(['update']);
        $this->middleware('scope:uad')->only(['destroy']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        //return
        $path = storage_path() . '/logs/dailylogs-' . date('Y-m-d') . '.log';
        return response()->json(["logs" => $this->readFiles($path, $request->system)]);
    }




    public function filterLogs(Request $request)
    {


        $startDate  =  "";
        $endDate    =  "";
        $system     = $request->system;
        $logCollection = [];
        $i = 0;
        if ($request->date) {
            $startDate  =  $request->date[0];
            if (count($request->date) == 2) {
                $endDate    =  $request->date[1];
            }
        }
        if ($startDate != "" && $endDate != "") {

            while ($endDate > $startDate) {
                $startDate =  date("Y-m-d", strtotime("+" . $i . " day", strtotime($startDate)));
                // $startDate = $startDate->modify("+" . $i . " day");
                $path      =  storage_path() . '/logs/dailylogs-' . $startDate . '.log';


                if (file_exists($path)) {
                    # code...

                    $logFile                = file($path);
                    foreach ($logFile as $index => $line) {
                        $string             = substr($line, strpos($line, "{"));
                        $logdata            = json_decode($string);
                        $logdata->date      = substr($line, 1, 10);
                        $logdata->time      = substr($line, 11, 9);
                        if ($logdata->system == $system) {
                            // page logdata->page
                            $logCollection[]    = $logdata;
                        }
                    }
                }
                $i = 1;
            }

            return response()->json(["logs" => $logCollection]);
        } else if ($startDate == "" && $endDate == "") {
            $path = storage_path() . '/logs/dailylogs-' . date('Y-m-d') . '.log';
            return response()->json(["logs" => $this->readFiles($path, $request->system)]);
        } else if ($startDate != "" && $endDate == "") {
            $path  =  storage_path() . '/logs/dailylogs-' . $startDate . '.log';
            return response()->json(["logs" => $this->readFiles($path, $request->system)]);
        }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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
        // search log
        $path = storage_path() . '/logs/dailylogs-' . $id . '.log';
        return response()->json(["logs" => $this->readFiles($path, 'ss')]);
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
    public function destroy(Request $request, $id)
    {
        $ids     = explode(',', $id);
        $path     = storage_path() . '/logs/dailylogs-' . $request->date . '.log';
        $line     = "";
        try {
            if (fileExists($path)) {
                $contents = file_get_contents($path);
                foreach (file($path) as $index => $line) {
                    foreach ($ids as $value) {
                        if ($value == $index) {
                            $contents = str_replace($line, '', $contents);
                            file_put_contents($path, $contents);
                        }
                    }
                }
                return  response()->json(['result' => true]);
            } else {
                return  response()->json(['result' => false]);
            }
        } catch (\Throwable $th) {
            return  response()->json(['result' => false]);
        }
    }
    public function readFiles($path, $system)
    {
        $logCollection = [];
        if (file_exists($path)) {
            # code...
            $logFile                = file($path);
            foreach ($logFile as $index => $line) {
                $string             = substr($line, strpos($line, "{"));
                $logdata            = json_decode($string);
                $logdata->date      = substr($line, 1, 10);
                $logdata->time      = substr($line, 11, 9);
                if ($logdata->system == $system) {

                    $logCollection[]    = $logdata;
                }
            }
        }

        return $logCollection;
    }
    // for test
    public function locationTracker()
    {
        return    $this->getAddress(34.4907139, 69.0612465);
    }


    function getAddress($latitude, $longitude)
    {
        $geocodeFromLatLong = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?latlng=' . trim($latitude) . ',' . trim($longitude) . '&sensor=false');
        $output = json_decode($geocodeFromLatLong);
        $status = $output->status;
        return $output;

        $address = ($status == "OK") ? $output->results[1]->formatted_address : '';
        if (!empty($address)) {
            return $address;
        } else {
            return false;
        }
    }
}
