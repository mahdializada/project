<?php

namespace App\Repositories\Advertisement\Platforms;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use App\Models\Advertisement\Application;
use App\Repositories\Advertisement\PlatformUrl;
use App\Repositories\Advertisement\AdvertisementUtil;
use Illuminate\Support\Carbon;

class GoogleAnalytics
{
    private static string $SCOPE = "https://www.googleapis.com/auth/analytics";

    public static function fetchGoogleAnalytics($property_id, $search_value, $extra_data = null)
    {
        try {


            $body = self::prepareBody($search_value, $extra_data);

            $url =  "properties/$property_id:runReport";
            $application = Application::whereHas("platform", function ($query) {
                $query->where("platform_name", "google analytics");
            })->first();
            if ($application) {
                $options = ["method" => "POST", "body" => $body];
                $options["url"] = $url;
                $response = self::authenticateAndFetch($application, $options);
                $rows = AdvertisementUtil::getValue("rows", $response);
                return self::parseGoogleAnalytics($rows);
            }
            return [];
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage(), 500);
        }
    }

    public static function parseGoogleAnalytics($analytics)
    {
        $item = [];
        if ($analytics && count($analytics) > 0) {
            $first_item = $analytics[0]["metricValues"];
            $item["ga_total_users"] = (int) $first_item[0]["value"];
            $item["ga_new_users"] =  (int)$first_item[1]["value"];
            $item["ga_user_engagement"] =  (int)$first_item[2]["value"];
            $item["ga_sessions"] = (int) $first_item[3]["value"];
            $item["ga_engaged_sessions"] = (int) $first_item[4]["value"];
            $item["ga_page_views"] = (int) $first_item[5]["value"];
        }
        return $item;
    }

    private static function prepareBody($search_value, $extra_data = null)
    {

        $todayDate = Carbon::today()->setTimezone("Asia/Dubai")->toDateString();
        return  [
            "dateRanges" => [
                "startDate" =>  $extra_data['start_date'],
                "endDate" => $extra_data['start_date'],
                // "startDate" => $todayDate,
                // "endDate" => $todayDate,
            ],
            "metrics" => [
                ["name" => "totalUsers"],
                ["name" => "newUsers"],
                ["name" => "userEngagementDuration"],
                ["name" => "sessions"],
                ["name" => "engagedSessions"],
                ["name" => "screenPageViews"],
            ],
            "dimensionFilter" => [
                "filter" => [
                    "fieldName" => "landingPage",
                    "stringFilter" => [
                        "value" => "bc=$search_value",
                        "caseSensitive" => false,
                        "matchType" => "CONTAINS"
                    ]
                ]
            ],
        ];
    }


    /**
     * Authenticate Google ads and fetch records
     */
    public static function authenticateAndFetch(Application $application, array $options, $break = false)
    {
        try {


            $token = $application->access_token;
            $url = PlatformUrl::$GOOGLE_ANALYTICS_BASE_URL . $options["url"];
            $headers = [
                "Accept" => "application/json",
                "Content-Type" => "application/json",
            ];
            $http = Http::withHeaders($headers)->withToken($token);
            $body = [];
            if (array_key_exists("query", $options)) {
                $http =   $http->withOptions(["query" => $options["query"]]);
            }
            if (array_key_exists("body", $options)) {
                $body =   $options["body"];
            }
            switch ($options["method"]) {
                case 'POST':
                    $response = $http->post($url, $body);
                    break;
                default:
                    $response = $http->get($url);
                    break;
            }
            if ($break && $response->failed()) {
                if ($response->status() === Response::HTTP_UNAUTHORIZED) {
                    AdvertisementUtil::expireApplication($application);
                    throw new Exception("google_token_expired", 401);
                }
                throw new Exception("google_fetching_error", 0);
            }
            if ($response->status() == Response::HTTP_UNAUTHORIZED) {
                $is_logged_in =  self::refreshToken($application);
                if ($is_logged_in) return self::authenticateAndFetch($application, $options, true);
                return [];
            }
            if ($response->successful()) {
                return $response->json();
            }
            $response->throw();
        } catch (\Throwable $th) {
            throw new Exception("google analytic" . $th->getMessage(), 404);
        }
    }


    /**
     * Refresh and Authenticate Google ads API Manager Account
     */
    public static function refreshToken(Application $application)
    {
        $body = [
            "client_id" => $application->client_id,
            "client_secret" => $application->client_secret,
            "refresh_token" => $application->refresh_token,
            "grant_type" => "refresh_token",
        ];
        $url = PlatformUrl::$GOOGLE_ADS_AUTH_BASE_URL . "token";
        $response = Http::post($url, $body);
        if ($response->successful()) {
            $data =  $response->json();
            $application->access_token = $data["access_token"];
            $application->expires_in = $data["expires_in"];
            $application->save();
            return true;
        }
        AdvertisementUtil::expireApplication($application);
        $response->throw();
        return false;
    }


    /**
     * Get Access Token and Refresh Token & Create Application
     */

    public static function getTokenAndStore(array $application_data, $application_id = null)
    {

        $body = [
            "client_id" => $application_data["client_id"],
            "client_secret" => $application_data["client_secret"],
            "code" => $application_data["code"],
            "grant_type" => "authorization_code",
            "redirect_uri" => PlatformUrl::$GOOGLE_REDIRECT_URL,
        ];
        $url = PlatformUrl::$GOOGLE_ADS_AUTH_BASE_URL . "token";
        $response = Http::post($url, $body);
        if ($response->successful()) {
            $token_data =  $response->json();
            if ($application_id) {
                $update = [
                    "access_token" => $token_data["access_token"],
                    "refresh_token" => $token_data["refresh_token"],
                    "expires_in" => $token_data["expires_in"],
                    "updated_by" => auth()->id(),
                    "system_status" => "ACTIVE",
                ];
                Application::where("id", $application_id)->update($update);
                return true;
            }
            $attributes = [
                "code" => rand(1000000, 100000000),
                "name" => $application_data["name"],
                "platform_id" => $application_data["platform_id"],
                "redirect_url" =>  self::googleRedirectUrl($application_data["client_id"]),
                "scope" =>  self::$SCOPE,
                "client_id" => $application_data["client_id"],
                "client_secret" => $application_data["client_secret"],
                "access_token" => $token_data["access_token"],
                "refresh_token" => $token_data["refresh_token"],
                "expires_in" => $token_data["expires_in"],
                "created_by" => auth()->id(),
                "updated_by" => auth()->id(),
                "country_id" => $application_data["country_id"],
            ];
            $check_application = Application::where([
                ["client_id", "=", $attributes["client_id"]],
                ["client_secret", "=", $attributes["client_secret"]],
                ["scope", "=", $attributes["scope"]],
            ])->first();
            if ($check_application) return false;
            $app = Application::create($attributes);
            $app->users()->sync($application_data['users']);
            return true;
        }
        return false;
    }

    private static function googleRedirectUrl(string $client_id)
    {
        return "https://accounts.google.com/o/oauth2/v2/auth?redirect_uri=" . PlatformUrl::$GOOGLE_REDIRECT_URL . "&prompt=consent&response_type=code&client_id=$client_id&scope=" . self::$SCOPE . "&access_type=offline";
    }
}
