<?php

namespace App\Repositories\Advertisement;

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
use App\Models\Advertisement\ItemStatus;
use App\Models\Advertisement\Reasonable;
use App\Models\ContentLibrary\ContentLibraryMedia;
use App\Repositories\Advertisement\Platforms\TikTok;
use App\Repositories\Advertisement\Platforms\Facebook;
use App\Repositories\Advertisement\Platforms\GoogleAd;
use App\Repositories\Advertisement\Platforms\Snapchat;
use App\Repositories\Advertisement\Platforms\Teebalhoor;
use App\Repositories\Advertisement\AdvertisementTabs\AdAccount;
use Carbon\Carbon;
use Exception;

class ConnectionRepository extends Repository
{

    public function store(Request $request)
    {
        try {
            $connection = new Connection();
            $country = Country::withTrashed()->find($request->country_id);
            $teebalhoor_product = Teebalhoor::checkProductCodes($country, [$request->pcode]);
            $product_name = collect($teebalhoor_product)->get($request->pcode);
            if (!$product_name) {
                return response()->json(["result" => false, "message" => "product_code_not_found"]);
            }
            $account = AdAccount::findAdAccount($request->application_id, $request->ad_account_id);
            if (!$account) {
                return response()->json(["result" => false, "message" => "selected_ad_account_not_found"]);
            }
            if (!$account->account_po) {
                $account->account_po = $request->account_po;
                $account->save();
            }
            $attributes = $request->only($connection->getFillable());
            $attributes["ad_account_id"] = $account->id;
            $attributes["server_account_id"] = $account->account_id;
            $attributes["code"] = rand(1000, 999999999);
            $attributes["pname"] = $product_name;
            $connection = Connection::create($attributes);
            $link = self::generateLink($connection, $account->account_po, $request);
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

    public static function generateLink(Connection $connection, $account_po = '', $request) //account_po where it com from
    {
        $link = $connection->landing_page_link;
        $base64 = base64_encode($connection->code);
        $postfix = "?bc=$base64&$account_po";
        if (Str::contains($link, "?")) {
            $postfix = "&bc=$base64&$account_po";
        }
        if ($request->quick_by) {
            $connection->generated_link = $connection->landing_page_link . $postfix . '&quick=on';
        } else {
            $connection->generated_link = $connection->landing_page_link . $postfix;
        }
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
            "sales_type" => ["required", "in:Landing Page Sales,Quick Card Sales,WhatsApp Sales"],
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
        $ads = [];
        foreach ($request->ads as $key => $ad) {
            try {
                $relation = ["application", "platform", "adAccount"];
                $connection = Connection::with($relation)->find($ad['connection_id']);
                if ($ad['media_id']) {
                    ContentLibraryMedia::where('id', $ad['media_id'])->update(['used_in_advertisement' => true, 'status' => 'publish']);
                }

                $condition = [
                    ["ad_id", "=",  $ad['ad_id']],
                    ["server_account_id", "=", $connection->adAccount->account_id],
                ];

                $exists = HistoryAd::where($condition)->first();
                if (!$exists) {



                    DB::beginTransaction();
                    $ad_item = $this->filterPlatform($connection, $ad['ad_id']);
                    if ($ad_item) {
                        $connection->media_link = $ad['media_link'] ?? null;
                        $connection->media_id = $ad['media_id'] ?? null;
                        $connection->media_size = $ad['media_size'] ?? null;
                        $connection->history_ad_id = $ad_item->id;
                        $connection->server_ad_id = $ad_item->ad_id;
                        $connection->server_ad_adset_id = $ad_item->server_adset_id;
                        $connection->server_campaign_id = $ad_item->adset->server_campaign_id;
                        $ad_item->ad_pcode = $connection->pcode;
                        $ad_item->save();
                        $connection->save();
                        $itemStatus = ItemStatus::where('item_code', $connection->pcode)->where('country_id', $connection->country_id)->get();
                        if (!$itemStatus) {
                            date_default_timezone_set("Asia/Dubai");
                            ItemStatus::create([
                                'item_code' => $connection->pcode,
                                'country_id' => $connection->country_id,
                                'item_status' => 'ready_to_sale',
                                'color' => '#0049b0',
                                'created_by' => auth()->user()->id,
                                'created_at' => date('Y-m-d H:i:s'),
                                'end_date' => null,
                            ]);
                        }
                        DB::commit();
                        $ads[] = ['ad_id' => $ad['ad_id'], "result" => true, "generated_link" => $connection->generated_link];
                    }
                    DB::rollback();
                } else {
                    $ads[] = ['ad_id' => $ad['ad_id'], "exists" => true, "result" => false];
                    Connection::where('id', $ad['connection_id'])->delete();
                }
            } catch (\Throwable $th) {
                DB::rollback();
                if ($th->getCode() == 404 || $th->getCode() == 6001) {
                    $ads[] = ['ad_id' => $ad['ad_id'], "result" => false, "not_found" => true, 'error_code' => $th->getCode(), "message" => $th->getMessage()];
                } else {

                    $ads[] = ['ad_id' => $ad['ad_id'], "result" => false, 'error_code' => $th->getCode(), "message" => $th->getMessage()];
                }
                Connection::where('id', $ad['connection_id'])->delete();
            }
        }
        return $ads;
    }
    public function update3(Request $request)
    {
    }

    public function update2(Request $request)
    {
        $ads = [];
        foreach ($request->ads as $key => $ad) {
            try {
                $exist = false;
                $connection = $this->findOrCreateConnection($request, $key, $ad['ad_id']);
                if ($ad['media_id']) {
                    ContentLibraryMedia::where('id', $ad['media_id'])->update(['used_in_advertisement' => true, 'status' => 'publish']);
                }
                if ($connection) {
                    DB::beginTransaction();
                    $ad_item = $this->filterPlatform($connection, $ad['ad_id']);
                    if ($ad_item) {
                        $connection->media_link = $ad['media_link'] ?? null;
                        $connection->media_id = $ad['media_id'] ?? null;
                        $connection->media_size = $ad['media_size'] ?? null;
                        $connection->history_ad_id = $ad_item->id;
                        $connection->server_ad_id = $ad_item->ad_id;
                        $connection->server_ad_adset_id = $ad_item->server_adset_id;
                        $connection->server_campaign_id = $ad_item->adset->server_campaign_id;
                        $ad_item->ad_pcode = $connection->pcode;
                        $itemStatus = ItemStatus::where('item_code', $connection->pcode)->where('country_id', $connection->country_id)->get();
                        if (!$itemStatus) {
                            date_default_timezone_set("Asia/Dubai");
                            ItemStatus::create([
                                'item_code' => $connection->pcode,
                                'country_id' => $connection->country_id,
                                'item_status' => 'ready_to_sale',
                                'color' => '#0049b0',
                                'created_by' => auth()->user()->id,
                                'created_at' => date('Y-m-d H:i:s'),
                                'end_date' => null,
                            ]);
                        }
                        $ad_item->save();
                        $connection->save();

                        DB::commit();
                        $ads[] = ['ad_id' => $ad['ad_id'], "result" => true, "generated_link" => $connection->generated_link];
                    }
                    DB::rollback();
                } else {
                    $exist = true;
                    $ads[] = ['ad_id' => $ad['ad_id'], "exists" => $exist, "result" => false];
                }
            } catch (\Throwable $th) {
                DB::rollback();
                $ads[] = ['ad_id' => $ad['ad_id'], "result" => false, "not_found" => true, 'error_code' => $th->getCode(), "message" => $th->getMessage()];
            }
        }

        // return response()->json($ads);
        return $ads;
    }

    public function findOrCreateConnection(Request $request, $index, $ad_id)
    {
        $relation = ["application", "platform", "adAccount"];
        $connection = Connection::with($relation)->find($request->connection_id);
        $condition = [
            ["ad_id", "=", $ad_id],
            ["server_account_id", "=", $connection->adAccount->account_id],
        ];
        $exists = HistoryAd::where($condition)->first();
        if ($exists)
            return false;
        if ($index != 0) {
            $attributes = $connection->toArray();
            // $connection->getFillable();
            // return  $attributes;
            // $connection = Connection::create($attributes);


            $attributes["code"] = rand(1000, 999999999);
            // $connection = $connection->toArray();
            // return $connection['ad_account']['account_po'];

            $connection = Connection::create($attributes);
            $link = self::generateLink($connection, $connection->adAccount->account_po, $request);
            if ($request->save_as_template == 'yes') {
                ConnectionTemplate::create([
                    "connection_id" => $connection->id,
                    "name" => $request->template_name,
                ]);
            }
            return $connection ? $connection : false;
        }
        return $connection;
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
        $countries = Country::where(['status' => 'active', 'advertisement_status' => "active"])->whereHas("companies", fn ($query) => $query->where('status', 'active'))->select($columns)->get();
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

    public function teebalhoorProducts($ids)
    {
        $type = 0;
        $ids = explode(",", $ids);
        $company_id = $ids[0];
        $country_id = $ids[1];
        $company = Company::withTrashed()->find($company_id);
        $company_name = Str::lower($company->name);
        $country = Country::withTrashed()->find($country_id);
        // $invalid_products = Connection::where('advertisement_status',  'inactive')
        //     ->groupBy('pcode')->pluck('pcode')->toArray();
        if (count($ids) > 2) {
            $type = $ids[2];
        } else {
            if ($company_name == 'uae-magicstyle')
                $type = 1;
            if ($company_name == 'uae-aegallery') {
                $type = 2;
            }
        }
        $products = Teebalhoor::fetchTeebalhoorProducts($country, $company_name, $type);
        // ->filter(fn ($key) => !in_array($key['pcode'], $invalid_products))->values()->toArray();

        return $products;
    }
    public function filterActiveProducts($company_id)
    {
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

    public function platformApplication($ids)
    {
        $ids = explode(",", $ids);
        $platform_id = $ids[0];
        // $country_id = $ids[1];
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

    public function fetchAllAdAccounts()
    {

        $accounts = [];


        $applications = Application::where(['advertisement_status' => "active"])->select(["id", "name", "code"])->get();
        foreach ($applications as $key => $application) {
            $accounts = array_merge($accounts,  $this->fetchAdAccounts($application->id));
        }
        return $accounts;
    }
    // GET ysterday connections with active ads
    public static function connectionAccount($request = null)
    {
        $yesterday = Carbon::yesterday();
        $relation = ["application", "platform", "adAccount", "historyAd", "company", "country"];
        $connections = Connection::with($relation)
            ->whereHas("application", function ($query) {
                return $query->where("system_status", "ACTIVE")
                    ->whereNotNull("access_token")->whereNotNull("refresh_token");
            })->whereHas("platform", function ($query) {
                return $query->where("system_status", "ACTIVE");
            })
            ->whereHas("historyAdds", function ($query)  use ($yesterday) {
                // ->where("status", "ACTIVE")
                return $query->where("status", "ACTIVE")->where(
                    'data_date',
                    $yesterday->toDateString()
                );
            })->where("history_ad_id", "!=", null)
            ->groupBy(["server_account_id", "history_ad_id"])->get();
        return $connections;
    }
    // public static function connectionAccount($request = null)
    // {

    //     $relation = ["application", "platform", "adAccount", "historyAd", "company", "country"];
    //     $connections = Connection::with($relation)
    //         ->whereHas("application", function ($query) {
    //             return $query->where("system_status", "ACTIVE")
    //                 ->whereNotNull("access_token")->whereNotNull("refresh_token");
    //         })->whereHas("platform", function ($query) {
    //             return $query->where("system_status", "ACTIVE");
    //         })
    //         ->whereHas("connectionAd", function ($query) {
    //             return $query->where("status", "ACTIVE");
    //         })->where("history_ad_id", "!=", null);
    //     if ($request != null) {
    //         if ($request->dates)
    //             $connections = $connections->whereDate('created_at', "<=", $request->dates);
    //     }
    //     $connections = $connections->groupBy(["server_account_id", "history_ad_id"])->get();
    //     return $connections;
    // }

    public static function connectionAccountByPlatform($request)
    {
        $today = Carbon::now();
        $relation = ["application", "platform", "adAccount", "historyAd"];
        $connections = Connection::with($relation)
            ->whereHas("application", function ($query) {
                return $query->where("system_status", "ACTIVE")
                    ->whereNotNull("access_token")->whereNotNull("refresh_token");
            })
            ->whereHas("platform", function ($query) use ($request) {
                if ($request->platform)
                    return $query->where("system_status", "ACTIVE")->where("platform_name", $request->platform);
                else
                    return $query->where("system_status", "ACTIVE");
            })
            ->whereHas("historyAdds", function ($query)  use ($request, $today) {
                if ($request->ad_ids) {
                    return $query->whereIn('ad_id', $request->ad_ids)->where('data_date', $request->dates ? $request->dates : $today->toDateString());
                }
                return $query->where("status", "ACTIVE")->where('data_date', $request->dates ? $request->dates : $today->toDateString());
            })->where("history_ad_id", "!=", null);
        if ($request->dates)
            $connections = $connections->whereDate(DB::raw('DATE(created_at)'), "<=", $request->dates);
        $connections = $connections->groupBy(["server_account_id", "history_ad_id"])->get();
        return $connections;
    }


    /**
     * Check for connection code is exists or not
     */
    public static function checkForConnectionCode(string $connection_code)
    {
        $connection_exists = Connection::where("code", $connection_code)->first();
        if ($connection_exists) {
            return response()->json(["status" => ResponseConstant::$SUCESS, "message" => "Connection code found!"]);
        }
        return response()->json(["status" => ResponseConstant::$FAILED, "message" => "Connection code not found!"], 404);
    }

    public static function productStatusHistory(Request $request)
    {
        try {

            $query =  Connection::select('id', 'pcode', 'pname', 'created_at', 'country_id', 'company_id', 'project_id', 'advertisement_status');
            $query->where($request->column, $request->id)
                ->groupBy(["pcode"])->with(['reasonables2' => function ($query) use ($request) {
                    if ($request->start_date && $request->end_date)
                        $query->whereBetween(DB::raw('DATE(reasonables.created_at)'), [$request->start_date, $request->end_date])
                            ->orderBy('reasonables.created_at', 'desc');
                }]);


            $data = $query->get();
            $statusQuery = Reasonable::where('reasonable_type', 'App\Models\Advertisement\Connection')->whereDate('created_at', "<", $request->start_date)
                ->orderBY('created_at', 'desc')->get()->unique('reasonable_id');
            $data = $query->get()->unique(['reasonable_id']);
            return response()->json(["status" => ResponseConstant::$SUCESS, "data" => $data, 'resonable' => $statusQuery]);

            return response()->json(["status" => ResponseConstant::$SUCESS, "data" => $data]);
        } catch (Exception $exception) {
            return response()->json(["status" => ResponseConstant::$FAILED, "message" => $exception->getMessage()], 404);
        }
    }
}
