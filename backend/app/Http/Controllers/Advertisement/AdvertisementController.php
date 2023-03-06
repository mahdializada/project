<?php

namespace App\Http\Controllers\Advertisement;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Advertisement\Platform;
use App\Models\Advertisement\HistoryAd;
use App\Repositories\CompanyRepository;
use App\Repositories\CountryRepository;
use Illuminate\Support\Facades\Storage;
use App\Models\Advertisement\HistoryAdset;
use App\Models\Advertisement\HistoryCampaign;
use App\Exports\Advertisement\ConnectionExport;
use App\Repositories\Advertisement\PlatformRepository;
use App\Repositories\Advertisement\Platforms\Teebalhoor;
use App\Repositories\Advertisement\AdvertisementTabs\Ads;
use App\Repositories\Advertisement\AdvertisementRepository;
use App\Repositories\Advertisement\AdvertisementTabs\Adset;
use App\Repositories\Advertisement\AdvertisementTabs\Campaign;
use App\Repositories\Advertisement\AdvertisementTabs\AdAccount;
use App\Models\Advertisement\AdAccount as AdvertisementAdAccount;
use App\Models\Advertisement\Application;
use App\Models\Advertisement\Connection;
use App\Models\Advertisement\DisabledAd;
use App\Models\Advertisement\ItemStatus;
use App\Models\CommonChangesHistory;
use App\Models\TiktokWebhook;
use App\Repositories\Advertisement\AdvertisementUtil;
use App\Repositories\Advertisement\Platforms\Snapchat;
use App\Repositories\Advertisement\Platforms\TikTok;
use App\Repositories\Advertisement\HistoryAdRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class AdvertisementController extends Controller
{

    public function __construct()
    {
        $this->middleware('scope:advv')->only(['fetchItems', 'fetchFilterItems', 'getCounts']);
    }

    public function validateAdIds(Request $request)
    {
        $repository = new AdvertisementRepository();
        return $repository->validateAdIds($request);
    }

    public function exportAndStoreConnectionTemplate(Request $request, $id)
    {
        $repository = new AdvertisementRepository();
        return $repository->exportAndStoreConnectionTemplate($request, $id);
    }

    public function validateExcelRows(Request $request, $id)
    {
        try {
            $raw_codes = collect(json_decode($request->rows));
            $teeb_products = collect(Teebalhoor::checkProductCodeCollection($raw_codes, true));
            $not_existing_products = $raw_codes->diff($teeb_products->pluck('code'));
            return response()->json(['errors' => $not_existing_products, 'product_data' => $teeb_products], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function exportConnectionTemplate(Request $request, $id)
    {
        Excel::store(
            new ConnectionExport($id),
            'export-excel-files/connection_template.xlsx',
            'public'
        );
        return response()->json(env("APP_URL") . Storage::url('export-excel-files/connection_template.xlsx'));
    }

    public function fetchItems(Request $request, String $type)
    {
        $start_date = Carbon::parse($request->start_date);
        $end_date = Carbon::parse($request->end_date);
        if ($start_date->gt($end_date)) {
            $request->merge([
                "start_date" => $end_date->toDateString(),
                "end_date" => $start_date->toDateString(),
            ]);
        }
        $repository = new AdvertisementRepository();
        switch ($type) {
            case 'country':
                return $repository->fetchCountryItems($request);
            case 'company':
                return $repository->fetchCompanies($request);
            case 'project':
                return $repository->fetchProjects($request);
            case 'sales_type':
                return $repository->fetchSalesType($request);
            case 'item_code':
                return $repository->fetchItemCode($request);

            case 'ispp_code':
                return $repository->fetchIsppCode($request);
            case 'platform':
                return $repository->fetchPlatforms($request);
            case 'organization':
                return $repository->fetchOrganizations($request);
            case 'ad_account':
                $addAccount = new AdAccount();
                return $addAccount->fetchAdAccounts($request);
            case 'campaign':
                $compaign = new Campaign();
                return $compaign->fetchCampaigns($request);
            case 'ad_set':
                $adset = new Adset();
                return $adset->fetchAdsets($request);
            case 'ad':
                $ad = new Ads();
                return $ad->fetchAdItems($request);
            default:
                return response()->json(["not_allowed_tab" => true], 404);
        }
    }

    public function fetchFilterItems(String $filter_type)
    {
        $response = ["type" => $filter_type];
        switch ($filter_type) {
            case 'countries':
                $response["items"] = CountryRepository::connectionCountries();
                break;
            case 'companies':
                $response["items"] = CompanyRepository::connectionCompanies();
                break;
            case 'platforms':
                $response["items"] = PlatformRepository::connectionPlatforms();
                break;
            case 'ad_accounts':
                $response["items"] = AdAccount::connectionAdAccounts();
                break;
            case 'campaigns':
                $response["items"] = Campaign::connectionCampaigns();
                break;
            case 'adsets':
                $response["items"] = Adset::connectionAdsets();
                break;

            default:
                return response()->json(["not_allowed_tab" => true], 404);
        }
        return response()->json($response);
    }

    public function changeStatus(Request $request)
    {
        // return [];


        if ($request->multipleItems) {
            return $this->changeStatusMultiple($request);
        }
        $request->validate([
            "section" => ["required", "in:campaign,ad_set,ad"],
            "item_id" => ["required"],
            "status" => ["required"],
            'reason_ids' => ['required', 'exists:reasons,id'],
        ]);
        $section = $request->section;
        $response = ["type" => $section];
        $item_id = $request->item_id;
        switch ($section) {
            case "campaign":
                $response["status"] = Campaign::updateCampaignStatus($item_id);
                break;
            case "ad_set":
                $response["status"] = Adset::updateAdsetStatus($item_id);
                break;
            case "ad":

                $response["status"] = Ads::updateAdStatus($item_id);
                break;
            default:
                return response()->json(["not_allowed_tab" => true], 404);
        }
        if ($response["status"] == "success") {

            $this->storeReason($request);
        }
        return response()->json($response);
    }

    public function changeStatusMultiple($request)
    {
        $request->validate([
            "section" => ["required", "in:campaign,ad_set,ad"],
            "status" => ["required"],
            'multipleItems' => ['required'],
        ]);
        try {
            //code...

            DB::beginTransaction();
            foreach ($request->multipleItems as $key => $value) {
                $section = $request->section;
                $response = ["type" => $section];
                $request->merge([
                    "item_id" => $value['id'],
                    "reason_ids" => $value['reasons'],
                ]);
                $item_id = $request->item_id;

                switch ($section) {
                    case "campaign":
                        $response["status"] =  Campaign::updateCampaignStatus($item_id);
                        break;
                    case "ad_set":
                        $response["status"] = Adset::updateAdsetStatus($item_id);
                        break;
                    case "ad":
                        $response["status"] = Ads::updateAdStatus($item_id);
                        break;
                    default:
                        return response()->json(["not_allowed_tab" => true], 404);
                }

                if ($response["status"] == "success") {
                    $this->storeReason($request);
                }
            }
            DB::commit();
            return response()->json($response);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json($th->getMessage(), 500);
        }
    }

    private function storeReason($request)
    {
        $section = $request->section;
        $item_id = $request->item_id;
        $model = null;
        switch ($section) {
            case "campaign":
                $model = HistoryCampaign::where("campaign_id", $item_id)->first();
                break;
            case "ad_set":
                $model = HistoryAdset::where("adset_id", $item_id)->first();
                break;
            case "ad":
                $model = HistoryAd::where("ad_id", $item_id)->first();
                break;
            default:
                break;
        }
        if ($model) {
            $uuid = Str::uuid();
            $model->reasonables()->attach($request->reason_ids, ['created_by' => $request->user()->id, "status" => $request->status, 'uuid' => $uuid, 'tab' => $section, 'created_at' => Carbon::now()]);
        }
    }

    public function checkConnection(Request $req)
    {
        $request = $req->all();
        $AccountId = array_column($request, "account_id");
        $pcode = array_column($request, "pcode");
        $accountIdExists = AdvertisementAdAccount::whereIn("account_id", $AccountId)->get("account_id")->toArray();
        $pcodeExists = Teebalhoor::checkPCode($pcode);
        $accountIdExists = array_column($accountIdExists, "account_id");
        $pcodeExists = array_column($pcodeExists, "p_code");
        $result = [
            "account_id" => $accountIdExists,
            "pcoce" => $pcodeExists,
        ];
        return response()->json($result);
    }

    public function getCounts(Request $request)
    {
        $repository = new AdvertisementRepository();
        return $repository->getcounts($request);
    }

    public function getLastRefresh(Request $request)
    {
        $query = new HistoryAd();
        if ($request->platform != 'all')
            return $this->getPlatformLastRefresh($query, $request->platform);


        $ad = $query->select('id', 'data_timestamp')->orderBy('data_timestamp', "DESC")->first();
        if ($ad) {
            return response()->json(['date' => $ad->data_timestamp]);
        }

        return response()->json(['no_record' => true]);
    }
    public function getPlatformLastRefresh($query, $platform)
    {

        if ($platform == "crm") {

            $ad = $query->orderBy('crm_refresh_date', "DESC")->first();
            if ($ad) {
                return response()->json(['date' => $ad->crm_refresh_date]);
            }
        } else if ($platform == "facebook") {
            $ad = $query->orderBy('facebook_refresh_date', "DESC")->first();
            if ($ad) {
                return response()->json(['date' => $ad->facebook_refresh_date]);
            }
        } else if ($platform == "snapchat") {
            $ad = $query->orderBy('snapchat_refresh_date', "DESC")->first();
            if ($ad) {
                return response()->json(['date' => $ad->snapchat_refresh_date]);
            }
        } else if ($platform == "tiktok") {
            $ad = $query->orderBy('tikrok_refresh_date', "DESC")->first();
            if ($ad) {
                return response()->json(['date' => $ad->tikrok_refresh_date]);
            }
        } else if ($platform == "google ads") {
            $ad = $query->orderBy('google_ads_refresh_date', "DESC")->first();
            if ($ad) {
                return response()->json(['date' => $ad->google_ads_refresh_date]);
            }
        }
        return response()->json(['no_record' => true]);
    }

    public function getAppAdAccounts(Request $request, $id)
    {
        try {
            $platform = Platform::findOrFail($id);
            $ad_accounts = $platform->ad_accounts;
            return response()->json(['ad_accounts' => $ad_accounts], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getAccountPixel(Request $request)
    {
        # code...
    }
    public function getCrmLastRefresh($request)
    {
        # code...
    }
    public function updateBidBudget(Request $request)
    {


        try {
            //code...


            switch ($request->platform) {
                case 'facebook':

                    break;
                case 'snapchat':
                    return Snapchat::updateBidBudget($request);
                    break;
                case 'tiktok':
                    return TikTok::updateBidBudget($request);
                    break;
                case 'google ads':

                    break;
                default:
                    return null;
            }
        } catch (\Throwable $th) {

            return $th->getMessage();
        }
    }

    public function checkingAds(Request $request)
    {
        $repository = new HistoryAdRepository();
        if ($request->type == 'order')
            return $repository->checkCrmOrders($request);
        else
            return $repository->checkingAds($request);
    }

    public function downloadCheckingAdId()
    {
        return response()->json(env("APP_URL") . Storage::url('export-excel-files/checking-ad-id.xlsx'));
    }
    public function storeTiktokOrder(Request $request)
    {

        try {
            TiktokWebhook::create(['event' => json_encode($request->all())]);
            return response()->json(['result' => true]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['result' => false, 'error', $th->getMessage()], 400);
        }
    }
    public function getAdsetTargeting(Request $request, $id)
    {
        try {
            $select = ["locations", "adset_id", "gender", 'optimization_goal', 'server_campaign_id', 'age_groups', 'languages', 'network_types', 'operating_systems', 'device_models', 'placements', 'placement_type', 'smart_targeting', 'promotion_type'];

            if ($request->platform == 'tiktok') {
                $relation = ["application", "adAccount", "historyAddsets"];
                $connections = Connection::with($relation)
                    ->whereHas("historyAddsets", function ($query) use ($request) {
                        return $query->where("server_ad_adset_id", $request->id);
                    })->first();
                $data =    HistoryAdset::with(['campaign:campaign_id,objective_type'])->whereAdsetId($id)->select($select)->latest()->first();
                $location =  TikTok::getLocationInfo($connections, $request->id, $data);
                # code...
                $adsets =   HistoryAdset::whereAdsetId($id)->select($select)->latest()->first();
                $adsets = collect($adsets)->put('locations', $location);

                return $adsets;
            }
            return    HistoryAdset::whereAdsetId($id)->select($select)->latest()->first();
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 500);
        }
    }

    public function changeItemStatus(Request $request)
    {
        try {
            date_default_timezone_set("Asia/Dubai");
            ItemStatus::where('item_code', $request->item_code)->where('country_id', $request->country_id)->update(['isActive' => false]);
            ItemStatus::create([
                'item_code' => $request->item_code,
                'country_id' => $request->country_id,
                'item_status' => $request->item_status['status'],
                'color' => $request->item_status['color'],
                'created_by' => auth()->user()->firstname . ' ' . auth()->user()->lastname,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            return response()->json(true, Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
    public function deleteItemCode(Request $request)
    {
        $repository = new AdvertisementRepository();
        return $repository->deleteItemCode($request);
    }

    public function addForDisabledStatusAd()
    {
        # code...
        return   AdvertisementUtil::refreshDisabledAds();
    }


    public function getBidBudgetHistory(Request $request)
    {
        # code...
        $start_date = Carbon::parse($request->start_date);
        $end_date = Carbon::parse($request->end_date);
        if ($start_date->gt($end_date)) {
            $request->merge([
                "start_date" => $end_date->toDateString(),
                "end_date" => $start_date->toDateString(),
            ]);
        }
        return   CommonChangesHistory::with('changedBy:id,firstname,lastname,image')
            ->where(['changeable_id' => $request->id, 'column_name' => $request->type])
            ->whereBetween(DB::raw('DATE(created_at)'), [$request->start_date, $request->end_date])->paginate(9);
    }
    public function deleteInActiveAds(Request $request)
    {
        try {

            DB::beginTransaction();

            $ad1 =    HistoryAd::where('data_date', $request->data_date)->where('spend', '0.00')->delete();
            $ad2 =    HistoryAd::where('data_date', $request->data_date)->whereNULL('spend')->delete();
            $adset =  HistoryAdset::where('data_date', $request->data_date)->whereDoesntHave('inactiveAds')->delete();
            $campaign =  HistoryCampaign::where('data_date', $request->data_date)->whereDoesntHave('inActiveAdset')->delete();
            DB::commit();

            return ['ad' => $ad1, 'null spend' => $ad2, 'adset' => $adset, 'campaign' => $campaign];
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json($th->getMessage(), 500);
        }
    }
}
