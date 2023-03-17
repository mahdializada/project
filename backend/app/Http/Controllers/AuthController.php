<?php

namespace App\Http\Controllers;



use DateTime;
use App\Models\User;
use App\Models\Reason;
use App\Models\Company;

use App\Models\Country;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\WrongPassword;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use App\Repositories\QueryBuilder\TimezoneMapper;

class AuthController extends Controller
{
    // protected $redirectTo = RouteServiceProvider::HOME;
    protected $timezone;
    public function __construct()
    {
    }

    public function login(Request $request)
    {
        $request->validate([
            'email_username' => 'required',
            'password' => 'required',
        ]);
        $credentials = $request->only(['email_username', 'password']);
        $userData = User::where('email', $credentials['email_username'])
            ->orWhere('username', $credentials['email_username'])->first();

        if ($userData) {
            $userTimeZone = TimezoneMapper::latLngToTimezoneString($request->latitude, $request->longitude);

            $isInLimitRange = $this->isUserTimezoneInLimitedRange($userData, $userTimeZone);
            if (!$isInLimitRange) {
                return response()->json(['timeLimit' => true], 401);
            } else if (Hash::check($credentials['password'], $userData->password) && $isInLimitRange) {
                $current_country = Country::where('id', $userData->current_country_id)->first();
                // if($geoipInfo->country == $current_country->name){
                if (
                    Auth::attempt(['email' => $credentials['email_username'], 'password' => $credentials['password']]) ||
                    Auth::attempt(['username' => $credentials['email_username'], 'password' => $credentials['password']])
                ) {

                    $user = Auth::user();



                    if ($user->status == 'active') {

                        // browser name



                        // return  $user->get_scopes();
                        // return  array_diff($scope, $user->get_scopes());
                        //  get login info
                        if (Auth::user()->tracing_status == 1) {
                            // $this->getSessionActivity($request);
                        }
                        $success['token'] = $user->createToken('MyApp')->accessToken;

                        return response()->json($success, 200);
                    } else {
                        return response()->json(['result' => 'inactive', 'message' => 'User Account is ' . $user->status], 401);
                    }
                } else {
                    return response()->json(['result' => 'invalid', 'message' => 'Invalid login details'], 401);
                }
                // }else {
                //     $reason = Reason::where('title', 'Current Country changed')->first();
                //     $countryChanged = DB::table('reason_user')->insert([
                //         'reason_id'     => $reason->id,
                //         'user_id'        => $userData->id,
                //         'changed_by'    => $userData->id,
                //         'status'        => 'warning',
                //         'created_at'    => Carbon::now()
                //     ]);
                //     $userData->status = "warning";
                //     $userData->update();
                //     return response()->json(['message' => 'User is Country has changed!'], Response::HTTP_TEMPORARY_REDIRECT);
                // }
            } else {
                $wrongPassword = DB::table('wrong_passwords')->insert([
                    'user_id' => $userData->id,
                    'created_at' => Carbon::now(),
                ]);
                $countResets = WrongPassword::where('user_id', $userData->id)->get();
                $resetTimes = 0;
                foreach ($countResets as $count) {
                    $created = strtotime($count->created_at);
                    $nowDate = strtotime(Carbon::now());
                    $dif = abs($nowDate - $created);
                    if ($dif < 3600) {
                        $resetTimes += 1;
                    }
                }
                if ($resetTimes > 5) {

                    $userData->status = "warning";
                    $userData->tokens()->delete();
                    $userData->update();
                    $reason = Reason::where('title', 'Use wrong password 5 times within a hour')->first();
                    $countryChanged = DB::table('reason_user')->insert([
                        'reason_id'     => $reason->id,
                        'user_id'        => $userData->id,
                        'changed_by'    => $userData->id,
                        'status'        => 'warning',
                        'created_at'    => Carbon::now()
                    ]);
                    DB::table('wrong_passwords')->where('user_id', $userData->id)->delete();
                    return response()->json(['Warning' => 'password wrong!', "data" => null], Response::HTTP_NOT_FOUND);
                }
            }
            return response()->json([
                'message' => 'Invalid login details',
            ], 401);
        } else {
            return response()->json(['message' => 'Invalid Login Details'], 401);
        }
    }



    // check for user schedule timestamps
    public function isUserTimezoneInLimitedRange(User $user, $userTimeZone)
    {

        // get user timestapms
        $userTimestamps = Carbon::now()->setTimezone($userTimeZone);

        $isDateBetweenUserDate = true;
        // check user has time schedule time
        if ($user->schedule_type != "unlimited") {
            // convert date range
            $dateRange = json_decode($user->date_range);
            // convert time range
            $timeRange = json_decode($user->time_range);

            // parse and get start timestamps
            $startDate = $dateRange->startRange;
            $startTime = date("H:i:s", strtotime($timeRange->startRange));
            $startTimestamps = Carbon::createFromFormat("Y-m-d H:i:s", $startDate . " " . $startTime)
                ->shiftTimezone($userTimeZone);

            // parse and get end timestamps
            $endDate = $dateRange->endRange;
            $endTime = date("H:i:s", strtotime($timeRange->endRange));
            $endTimestamps =  Carbon::createFromFormat("Y-m-d H:i:s", $endDate . " " . $endTime)
                ->shiftTimezone($userTimeZone);

            // check is user current time between selected start and end timestamps or not
            $isDateBetweenUserDate = $userTimestamps->between($startTimestamps, $endTimestamps);
            if ($isDateBetweenUserDate) {
                $currentUserTime = $userTimestamps->toTimeString();
                $currentStartTime = $startTimestamps->toTimeString();
                $currentEndTime = $endTimestamps->toTimeString();

                if ($currentUserTime >= $currentStartTime && $currentUserTime <= $currentEndTime) {
                    $isDateBetweenUserDate = true;
                } else {
                    $isDateBetweenUserDate = false;
                }
            }
        }
        return $isDateBetweenUserDate;
    }



    public function index(Request $request)
    {
        return $request->user();
    }


    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user->token()->delete()) {
            return response()->json([
                'success' => 'Logged out successfully.'
            ], 200);
        }
        return response()->json([
            'error' => 'Log out failed.'
        ], 401);
    }

    public function logoutFromAllDevices(Request $request)
    {
        $user = $request->user();
        $user->tokens->each(function ($token, $key) {
            $token->delete();
        });
        return response()->json([
            'success' => 'Logged out successfully.'
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user()->load(
            [
                "country:id,name",
                "currentCountry:id,name",
                "state:id,name",
                "companies:id,name,logo,status",
                "customizeTheme",
                "teams:id,code,name",
                "roles:id,code,name"
            ]
        );
        $user->scopes = $user->get_scopes();
        $selectedCompanies = json_decode($user->selected_companies);
        if ($selectedCompanies) {
            $authUserCompanies = Company::whereIn("id", $selectedCompanies)
                ->select(["id", "status", "name", "logo"])->get();
            $user->selectedCompanies = $authUserCompanies;
        } else {
            $user->selectedCompanies = [];
        }
        unset($user->permissions);
        return response()->json(['data' => $user, "result" => true]);
    }

    public function getSessionActivity($request)
    {

        $coords = ["latitude" => $request->latitude, "longtitude" => $request->longitude];
        $user = $request->user();
        $user->coords = $coords;
        $user->save();

        $address = getAddress($request->latitude, $request->longitude);
        $location = @$address['countryName'] . ", " . $address["name"];
        $logId = Str::uuid();
        $logs = [
            "id"         => $logId->toString(),
            "user_id"    => Auth::user()->id,
            "location"   => $location,
            "browser"   => $request->browser,
            "login_time" => date("Y-m-d h:i:sa"),
            "logout_time" => '',
        ];

        $lastmonth = date('Y-m', strtotime('last month'));
        $path = storage_path() . '/logs/sessionlog-' . $lastmonth . '.log';
        if (file_exists($path)) {
            File::delete($path);
        }
        Log::build([
            'driver' => 'single',
            'path' => storage_path('/logs/sessionlog-' . date('Y') . '-' . date('m') . '.log'),
        ])->info('logs', $logs);
    }
}
