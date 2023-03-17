<?php

namespace App\Repositories\AdvetisementUpload;

use App\Models\Company;
use App\Models\Country;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Utils\ResponseConstant;
use App\Models\SingleSales\Ispp;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use App\Models\Advertisement\Project;
use Illuminate\Support\Facades\Crypt;
use App\Models\Advertisement\Platform;
use App\Models\Advertisement\HistoryAd;
use App\Models\Advertisement\Connection;
use App\Models\Advertisement\Application;
use App\Models\Advertisement\ConnectionTemplate;
use App\Models\AdvetisementUpload\ConnectionMU;
use App\Models\AdvetisementUpload\HistoryAdMU;
use App\Repositories\Advertisement\Platforms\TikTok;
use App\Repositories\Advertisement\Platforms\Facebook;
use App\Repositories\Advertisement\Platforms\GoogleAd;
use App\Repositories\Advertisement\Platforms\Snapchat;
use App\Repositories\Advertisement\Platforms\Teebalhoor;
use App\Repositories\Advertisement\AdvertisementTabs\AdAccount;

class ConnectionMURepository extends Repository
{

    public function store(Request $request)
    {
        try {
            $connection = new ConnectionMU();
            $teebalhoor_product = Teebalhoor::checkProductCodeCollection([$request->pcode]);
            $product_name = collect($teebalhoor_product)->get($request->pcode);
            if (!$product_name) {
                return response()->json(["result" => false, "message" => "product_code_not_found"]);
            }

            $account = AdAccount::findAdAccount($request->application_id, $request->ad_account_id);
            if (!$account) {
                return response()->json(["result" => false, "message" => "selected_ad_account_not_found"]);
            }

            $attributes = $request->only($connection->getFillable());
            $attributes["ad_account_id"] = $account->id;
            $attributes["server_account_id"] = $account->account_id;
            $attributes["code"] = rand(1000, 999999999);
            $attributes["pname"] = $product_name;
            $connection = ConnectionMU::create($attributes);
            $link = self::generateLink($connection);
            if ($request->save_as_template == 'yes') {
                ConnectionTemplate::create([
                    "connection_id" => $connection->id,
                    "name" => $request->template_name,
                ]);
            }
            return response()->json(["result" => true, "connection" => $connection, "link" => $link], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 501);
        }
    }

    public static function generateLink(Connection $connection)
    {
         $link = $connection->landing_page_link;
        $base64 =base64_encode($connection->code);
        $postfix = "?bc=$base64";
        if (Str::contains($link, "?")) {
            $postfix = "&bc=$base64";
        }
        $connection->generated_link = $connection->landing_page_link . $postfix;
        $connection->hashed_code = $base64;
        $connection->save();
        return $connection->generated_link;
    }

    public function validationRules($is_update = false): array
    {
        $rules = [
            'pcode' => ['required', 'min:1', "max:255", "string"],
            'landing_page_link' => ['required', 'min:1', "url"],
            'platform_id' => ['required', 'exists:platforms,id', "uuid"],
            'application_id' => ['required', 'exists:applications,id', "uuid"],
            'company_id' => ['required', 'exists:companies,id', "uuid"],
            'country_id' => ['required', 'exists:countries,id', "uuid"],
            'ispp_id' => ['required', 'exists:ispp_ssp,id', "uuid"],
            "sales_type" => ["required", "in:Single Sale,Shopping Cart"],
            "ad_account_id" => ["required"],
            "save_as_template" => ["nullable", 'in:yes,no'],
            "template_name" => ["required_if:save_as_template,yes"],
        ];
        if ($is_update) {
            $rules["id"] = ['required', 'exists:connections,id'];
        }

        return $rules;
    }

    public function update(Request $request)
    {
        try {
            $relation = ["application", "platform", "adAccount"];
            $connection = ConnectionMU::with($relation)->find($request->connection_id);
            $condition = [
                ["ad_id", "=", $request->ad_id],
                ["server_account_id", "=", $connection->adAccount->account_id],
            ];
            $exists = HistoryAdMU::where($condition)->first();
            if ($exists) {
                return response()->json(["result" => false, "ad_id_exists" => true]);
            }
            DB::beginTransaction();
            $ad_item = $this->filterPlatform($connection, $request->ad_id);
            if ($ad_item) {
                $connection->media_link = $ad['media_link'] ?? null;
                $connection->media_id = $ad['media_id'] ?? null;
                $connection->media_size = $ad['media_size'] ?? null;
                $connection->history_ad_id = $ad_item->id;
                $connection->server_ad_id = $ad_item->ad_id;
                $connection->server_ad_adset_id = $ad_item->server_adset_id;
                $connection->server_campaign_id = $ad_item->adset->server_campaign_id;
                $connection->save();
                DB::commit();
                return response()->json(["result" => true], Response::HTTP_OK);
            }
            DB::rollback();
            return response()->json(["result" => false, "something_went_wrong" => true]);
        } catch (\Throwable $th) {
            DB::rollback();
            if ($th->getCode() == 404) {
                return response()->json(["result" => false, "not_found" => true]);
            }
            return response()->json(["result" => false, "message" => $th->getMessage()], 403);
        }
    }

    private function filterPlatform($connection, $ad_id)
    {
        $platform_name = $connection->platform->platform_name;
        switch ($platform_name) {
            case 'facebook':
                return Facebook::createAdWithAdsetAndCampaign($connection, $ad_id);
            case 'snapchat':
                return Snapchat::createAdWithAdsetAndCampaign($connection, $ad_id);
            case 'tiktok':
                return TikTok::createAdWithAdsetAndCampaign($connection, $ad_id);
            case 'google ads':
                return GoogleAd::createAdWithAdsetAndCampaign($connection, $ad_id);
            default:
                return null;
        }
    }

    public function countries()
    {
        $columns = ["id", "name", "code", "iso2"];
        $countries = Country::where(['status' => 'active', 'advertisement_status' => "active"])->whereHas("companies")->select($columns)->get();
        return $countries;
    }

    public function countryCompany($country_id)
    {
        $columns = ["id", "name", "code", "logo"];
        if ($country_id == "connections") {
            $companies = Company::whereHas("connection")->where(['status' => 'active', 'advertisement_status' => "active"])->select($columns)->get();
            return $companies;
        }

        $companies = Company::whereHas(
            "countries",
            fn ($query) => $query->where("country_id", $country_id)
        )->where(['status' => 'active', 'advertisement_status' => "active"])->select($columns)->get();
        return $companies;
    }

    public function teebalhoorProducts()
    {
        $products = Teebalhoor::fetchTeebalhoorProducts();
        return $products;
    }

    public function companyIspp($company_id)
    {
        $ispps = Ispp::where("status", "completed")
            ->where("company_id", $company_id)
            ->select(["id", "code"])->get();
        $items = collect();
        foreach ($ispps as $ispp) {
            $items->push([
                "id" => $ispp->id,
                "product_name" => $ispp->code,
                "code" => $ispp->code,
            ]);
        }
        return $items;
    }

    public function companyProject($company_id)
    {

        $projects = Project::query()->whereJsonContains("companies", $company_id)->where(['advertisement_status' => "active"])->select(["id", "name", "code"])->get()->toArray();

        return $projects;
    }
    public function companyPlatform($company_id)
    {

        $platforms = Platform::whereRelation("companies", "company_id", $company_id)
            ->where("platform_name", "!=", "google analytics")
            ->where(['advertisement_status' => "active"])
            ->select(["id", "platform_name", "code"])
            ->get();

        $platforms = $platforms->map(function ($item) {
            $item->name = $item->platform_name;
            return $item;
        });

        return $platforms;
    }

    public function platformApplication($platform_id)
    {
        $applications = Application::where("platform_id", $platform_id)->where(['advertisement_status' => "active"])->select(["id", "name", "code"])->get();
        return $applications;
    }

    public function fetchAdAccounts($application_id)
    {
        $application = Application::with("platform")->find($application_id);
        if ($application) {
            $platform_name = $application->platform->platform_name;
            switch ($platform_name) {
                case "facebook":
                    return Facebook::fetchAddAccounts($application_id);
                case "snapchat":
                    $fields = ["id", "name"];
                    return Snapchat::fetchAdAccountsField($application_id, $fields);
                case "tiktok":
                    return TikTok::fetchAdAccounts($application);
                case "google ads":
                    return GoogleAd::fetchAdAccounts($application);
                default:
                    return [];
            }
        }
        return [];
    }

    public static function connectionAccount()
    {
        $relation = ["application", "platform", "adAccount", "historyAd"];
        $connections = ConnectionMU::with($relation)
            ->whereHas("application", function ($query) {
                return $query->where("system_status", "ACTIVE")
                    ->whereNotNull("access_token")->whereNotNull("refresh_token");
            })->whereHas("platform", function ($query) {
                return $query->where("system_status", "ACTIVE");
            })->where("history_ad_id", "!=", null)
            ->groupBy(["server_account_id", "history_ad_id"])->get();
        return $connections;
    }

    /**
     * Check for connection code is exists or not
     */
    public static function checkForConnectionCode(string $connection_code)
    {
        $connection_exists = ConnectionMU::where("code", $connection_code)->first();
        if ($connection_exists) {
            return response()->json(["status" => ResponseConstant::$SUCESS, "message" => "Connection code found!"]);
        }
        return response()->json(["status" => ResponseConstant::$FAILED, "message" => "Connection code not found!"], 404);
    }
}
