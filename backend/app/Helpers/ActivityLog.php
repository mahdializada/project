<?php

use App\Http\Middleware\DailyLogContentParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ActivityLog extends DailyLogContentParser
{

    public function setLog($request, $system, $page, $event)
    {
        $lastmonth = date('Y-m-d', strtotime('last month'));
        $path = storage_path() . '/logs/dailylogs-' . $lastmonth . '.log';
        if (file_exists($path)) {
            File::delete($path);
        }
        $content = '';
        if ($event == 'delete') {
            $content = $this->getContentOfDelete($page, $request);
        } elseif ($event == "change status") {
            if ($page == "design_request_auto_review") {
                $content = $this->getContentOfAutoReview($request);
                $event = $request['status'];
            } else {
                $content = $this->getContentOfChangeStatus($page, $request);
                $event = $request->status;
            }

            // $content = "dddddd";
        } else {
            switch ($page) {
                case 'company':
                    $content = $this->getContentOfCompany($request);

                    break;
                case 'user':
                    $content = $this->getContentOfUser($request);
                    break;
                case 'department':
                    $content = $this->getContentOfDepartment($request);
                    break;
                case 'business_location':
                    $content = $this->getContentOfBusinessLocation($request);
                    break;
                case 'language':
                    $content = $this->getContentOfLanguage($request);
                    break;
                case 'translation':
                    $content = $this->getContentOfLanguage($request);
                    break;
                case 'team':
                    $content = $this->getContentOfTeamOrRole($request);
                    break;
                case 'role':
                    $content = $this->getContentOfTeamOrRole($request);
                    break;
                case 'design_request':
                    if ($event == "assign_users") {
                        if ($request->query->get("type") === "unassign") {
                            $event = "unassign_user";
                            $content = $this->getcontentOfUnAssignUser($request);
                        } else {
                            $content = $this->getcontentOfAssignUser($request);
                        }
                    } else {
                        $content = $this->getContentOfDesignRequest($request);
                    }

                    break;


                default:
                    $content = "";
                    # code...
                    break;
            }
        }
        if ($page == "design_request_auto_review") {
            $address = getAddress($request['latitude'], $request['longitude']);
            $location = @$address['countryName'] . ", " . @$address["name"];
        } else {
            $address = getAddress($request->header('latitude'), $request->header('longitude'));
            $location = @$address['countryName'] . ", " . @$address["name"];
        }


        if (Auth::check() && Auth::user()->tracing_status == 1) {
            $logId = Str::uuid();
            $logs = [
                "system"     => $system,
                "id"         => $logId->toString(),
                "page"       => $page,
                "user_id"    => Auth::user()->id,
                "user_code"    => Auth::user()->code,
                "username"  => Auth::user()->username,
                "location"   => $location,
                "event"      => $event,
                "page"       => $page,
                "content"    => $content,
            ];
            Log::channel('dailylog')->info("logs", $logs);
        }
        return "";
    }
}
