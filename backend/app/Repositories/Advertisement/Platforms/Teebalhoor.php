<?php

namespace App\Repositories\Advertisement\Platforms;

use Exception;
use App\Models\Country;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use App\Models\Advertisement\Connection;
use App\Repositories\Advertisement\PlatformUrl;

class Teebalhoor
{

    protected static string $API_TOKEN = '243|8N6VG1TGQBetQP34s1SC9uztOfQNZO5dq0wleW71';

    public static function fetchProducts($connections)
    {
        try {
            $product_codes = [];
            $connection_codes = [];
            foreach ($connections as $connection) {
                array_push($product_codes, $connection->pcode);
                array_push($connection_codes, $connection->code);
            }
            $from = Carbon::today()->toDateString();
            $to = Carbon::today()->toDateString();
            $body = [
                "from" => $from,
                "to" => $to,
                "product" => $product_codes,
                "ad_key" => $connection_codes,
            ];
            $auth_url = PlatformUrl::$QATAR_TEEB_AL_HOOR_BASE_UR;
            $url = $auth_url . "/api/getOrders";
            $body = json_encode($body);
            // $body = '{"product":["TM20"]}';
            $response = Http::withToken(Teebalhoor::$API_TOKEN)->withBody($body, "application/json")->get($url);
            if ($response->successful()) {
                $data =  $response->json("data");
                return collect($data)->keyBy("ad_key");
            }
            $response->throw();
        } catch (\Throwable $th) {
            throw new Exception("teebalhoor_products_error", 8000);
        }
    }


    public static function fetchTodayProduct(string $product_code, string $connection_code)
    {
        $today = Carbon::today()->setTimezone("Asia/Dubai")->toDateString();
        $body = [
            "from" => $today,
            "to" =>  $today,
            "product" => [$product_code],
            "ad_key" => [$connection_code],
        ];
        self::fetchCRMData($body);
    }

    public static function fetchCRMData($body)
    {
        $auth_url = PlatformUrl::$QATAR_TEEB_AL_HOOR_BASE_UR;
        $url = $auth_url . "/api/getOrders";
        $body = json_encode($body);
        $response = Http::withToken(Teebalhoor::$API_TOKEN)->withBody($body, "application/json")->get($url);
        if ($response->successful()) {
            $data =  $response->json("data");
            $data = collect($data)->first();
            return collect($data);
        }
        throw new Exception("teebalhoor_products_error", 9000);
    }
    public static function fetchTodayProductCRM(array $product_code, array $connection_code)
    {
        $today = Carbon::today()->setTimezone("Asia/Dubai")->toDateString();
        $body = [
            "from" => $today,
            "to" =>  $today,
            "product" => $product_code,
            "ad_key" => $connection_code,
        ];
        $auth_url = PlatformUrl::$QATAR_TEEB_AL_HOOR_BASE_UR;
        $url = $auth_url . "/api/getOrders";
        $body = json_encode($body);
        $response = Http::withToken(Teebalhoor::$API_TOKEN)->withBody($body, "application/json")->get($url);
        if ($response->successful()) {
            $data =  $response->json("data");
            $data = collect($data);
            return collect($data);
        }
        throw new Exception("teebalhoor_products_error", 9000);
    }

    public static function checkProductCodeCollection($pcodes, $withLingePageLink = false)
    {
        try {
            $base_url = PlatformUrl::$QATAR_TEEB_AL_HOOR_BASE_UR;
            $url = $base_url . "/api/check-products" . ($withLingePageLink ? "?with_lp_link=true" : "");
            $body = [
                "product_codes" => $pcodes
            ];
            $body = json_encode($body);
            $response = Http::withToken(Teebalhoor::$API_TOKEN)->withBody($body, "application/json")->get($url);
            $body = $response->json();
            if (array_key_exists("result", $body) && $body['result'])
                return $body['data'];
            else
                return [];
            $response->throw();
        } catch (\Throwable $th) {
            throw new Exception("teebalhoor_fetching_product_code_error" . $th->getMessage(), 5000);
        }
    }


    public static function checkProductCode($pcode)
    {
        try {
            $base_url = PlatformUrl::$QATAR_TEEB_AL_HOOR_BASE_UR;
            $url = $base_url . "/api/products/check?code=$pcode";
            $response = Http::withToken(Teebalhoor::$API_TOKEN)->get($url);
            $body = $response->json();
            if ($body)
                return $body["status"];
            $response->throw();
        } catch (\Throwable $th) {
            throw new Exception("teebalhoor_fetching_product_code_error", 8000);
        }
    }

    public static function checkPCode($pcode)
    {

        try {
            $from = Carbon::today()->toDateString();
            $to = Carbon::today()->toDateString();
            $body = [
                "from" => $from,
                "to" => $to,
                "products" => $pcode,
            ];

            $url = "https://qatarapi.teebalhoor.net/public/api/check-products-with-date";
            $response = Http::withToken(self::$API_TOKEN)->withBody($body, "application/json")->get($url);
            $body = $response->json();
            if ($body)
                return $body['data'];
            $response->throw();
        } catch (\Throwable $th) {
            throw new Exception("teebalhoor_fetching_product_code_error", 8000);
        }
    }

    //return only product code and product name
    public static function fetchTeebalhoorProducts(Country $country,  string $company_name, $type = 0)
    {
        $url = "/products?with_landing_link=true&company_name=$company_name&page_type=$type";

        $response = self::alhoorCompanies($country, [], $url);

        $teebalhoor_products = $response->json();
        $products = collect($teebalhoor_products);
        return $products;
    }



    /**
     * Authenticate to teebalhoor server
     */
    public static function authenticate()
    {
        $auth_url = PlatformUrl::$TEEB_AL_HOOR_BASE_UR;
        $body = [
            "email" => "order@teebalhoor.com",
            "password" => "7iHgXL7jMWcNywa",
        ];
        $url = $auth_url . "/api/login";
        $response = Http::post($url, $body);
        if ($response->successful()) {
            $data =  $response->json("data");
            return  $data["access_token"];
        }
        $response->throw();
    }

    public static function checkProductCodes(Country $country, array $pcodes, $withLingePageLink = false)
    {
        try {
            $url = "/api/check-products" . ($withLingePageLink ? "?with_lp_link=true" : "");
            $body = [
                "product_codes" => $pcodes
            ];
            $response = self::alhoorCompanies($country, $body, $url);
            $body = $response->json();
            if (array_key_exists("result", $body) && $body['result'])
                return $body['data'];
            else
                return [];
            $response->throw();
        } catch (\Throwable $th) {
            throw new Exception("teebalhoor_fetching_product_code_error" . $th->getMessage(), 5000);
        }
    }

    public static function serverProducts(Connection $connection, $extra_data = null)
    {

        $today = Carbon::today()->setTimezone("Asia/Dubai")->toDateString();

        $body = [
            "from" => $extra_data['start_date'],
            "to" =>  $extra_data['start_date'],
            // "from" => $today,
            // "to" => $today,
            "product" => [$connection->pcode],
            "ad_key" => [$connection->code],
        ];
        $url = "/api/getOrders";
        $response = self::alhoorCompanies($connection->country, $body, $url);
        $data =  $response->json("data");
        $data = collect($data)->first();
        return collect($data);
    }


    public static function alhoorCompanies(Country $country, array $body, string $route)
    {
        $country_iso2 = $country->iso2;
        $token = null;
        $domain = null;

        switch ($country_iso2) {
            case "kw":
                $token = "245|XFfeY6KF0b7TUhtoJ697fOANTvDk86C0xvtaUn2W";
                $domain = "kuwaitapi";
                break;
            case "iq":
                $token = "2|qHkli4eSBZYZgq51V75Bc6OTSLhdtBcaTqeseZHh";
                $domain = "iraqapi";
                break;
            case "ae":
                $token = "1237|uRg9yACOiSQln5HDXO5tWqdL6CgXMRPcQj9vUziv";
                $domain = "api";
                break;

            case "qa";
                $token = "245|rhY8p15CQkCmylPQ1EUfI5Vav0mRDFGtqj2jygaG";
                $domain = "qatarapi";
                break;
            case "sa":
                $token = "1237|uRg9yACOiSQln5HDXO5tWqdL6CgXMRPcQj9vUziv";
                $domain = "api";
                break;
        }
        if ($token && $domain) {
            $url = "https://$domain.teebalhoor.net/public$route";
            $body = json_encode($body);
            $response = Http::withToken($token)->withBody($body, "application/json")->get($url);
            return $response;
        }
        throw new Exception("invalid_country", 9000);
    }
    public static function fetchAlhoorCompanyTodayProductCRM($country, array $product_code, array $connection_code, string $refreshDate)
    {
        $body = [
            "from" => $refreshDate,
            "to" =>  $refreshDate,
            "product" => $product_code,
            "ad_key" => $connection_code,
        ];
        $url = "/api/getOrders";
        $response = self::alhoorCompanies($country, $body,  $url);
        if ($response->successful()) {
            $data =  $response->json("data");
            $data = collect($data);
            return collect($data);
        }
        throw new Exception("teebalhoor_products_error", 9000);
    }
}
