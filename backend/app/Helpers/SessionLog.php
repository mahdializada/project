<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SessionLog
{

    public function setSession($request)
    // public function setSession($request, $system, $page, $event)
    {
        $lastmonth = date('Y-m-d', strtotime('last month'));
        $path = storage_path() . '/logs/sessionlogs-' . $lastmonth . '.log';
        if (file_exists($path)) {
            File::delete($path);
        }

        $location   = geoip()->getLocation($request->ip())->toArray();
        $location   = $location['country'] . ", " . $location['city'] . ", " . $location['state_name'];
        $company = "";
        // if (Auth::check() && Auth::user()->tracing_status == 1) {
        if (Auth::check() && Auth::user()->tracing_status == 1) {
            $logId = Str::uuid();
            $logs = [
                "id"         => $logId->toString(),
                "user_id"    => Auth::user()->code,
                "username"  => Auth::user()->username,
                "location"   => $location,
                "company"   => $company,
            ];
            Log::channel('sessionlogs')->info("logs", $logs);
        }
        // }
    }
}
