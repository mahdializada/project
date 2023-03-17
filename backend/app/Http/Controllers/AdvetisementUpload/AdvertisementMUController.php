<?php

namespace App\Http\Controllers\AdvetisementUpload;

use Carbon\Carbon;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Advertisement\Connection;
use App\Models\AdvetisementUpload\HistoryAdMU;
use App\Models\AdvetisementUpload\ConnectionMU;
use App\Models\AdvetisementUpload\HistoryAdsetMU;
use App\Models\AdvetisementUpload\HistoryCampaignMU;
use App\Repositories\Advertisement\AdvertisementUtil;
use App\Repositories\Advertisement\Platforms\Teebalhoor;
use App\Repositories\AdvetisementUpload\AdvertisementTabs\AdsMU;
use App\Repositories\AdvetisementUpload\AdvertisementTabs\AdsetMU;
use App\Repositories\AdvetisementUpload\HistoryCampaignMURepository;
use App\Repositories\AdvetisementUpload\AdvertisementTabs\CampaignMU;

class AdvertisementMUController extends Controller
{
    protected static $data_date = "";

    protected static $conn_data     = [];
    protected static $ad_set_data   = [];
    protected static $ad_data       = [];
    protected static $campaign_data = [];

    public function fetchItems(Request $request, $section)
    {
        if (!$request->start_date) {
            $start_date = Carbon::yesterday()->toDateString();
            $request->merge(["start_date" => $start_date]);
        }
        if (!$request->end_date) {
            $end_date = Carbon::yesterday()->toDateString();
            $request->merge(["end_date" => $end_date]);
        }

        switch ($section) {
            case "campaign":
                $repository = new CampaignMU();
                return $repository->fetchCampaigns($request);
            case 'ad_set':
                $repository = new AdsetMU();
                return $repository->fetchAdsets($request);
            case 'ad':
                $repository = new AdsMU();
                return $repository->fetchAdItems($request);
            default:
                return response()->json(["message"=> "invalid section type"], 404);
        }
    }

    public function storeMUHistoryData(Request $request)
    {
        try {
            DB::beginTransaction();
            self::$conn_data     = json_decode($request->con_data);
            self::$data_date     = $request->data_date;
            self::$ad_data       = json_decode($request->ad);
            self::$ad_set_data   = json_decode($request->ad_set);
            self::$campaign_data = json_decode($request->campaign);
            self::storeHistoryCampaignData();
            DB::commit();
            return response()->json(['result' => true, 'message' => 'records_inserted_successfully'], 200);
        } catch (\Throwable$th) {
            return response()->json(['errors' => $th->getMessage()], 500);
        }
    }

    public static function storeHistoryAds($adSetModel)
    {
        foreach (self::$ad_data as $ad) {
            $connectionModel = Connection::where('server_ad_id', $ad->ad_id)->first();
            $historyAdModel  = HistoryAdMU::create([
                "code"                            => rand(9999, 9999999999),
                "ad_name"                         => $ad->ad_name,
                "ad_id"                           => $ad->ad_id,
                "adset_id"                        => $adSetModel->id,
                "server_adset_id"                 => $adSetModel->adset_id,
                "server_account_id"               => $connectionModel->server_account_id,
                "view_completion"                 => $ad->view_completion,
                "delivery_status"                 => $ad->delivery_status,
                "result"                          => $ad->result,
                "impressions"                     => $ad->impressions,
                "viewable_impressions"            => $ad->viewable_impressions,
                "two_second_video_views"          => $ad->two_second_video_views,
                "six_second_video_views"          => $ad->six_second_video_views,
                "video_views"                     => $ad->video_views,
                "average_screen_time"             => $ad->average_screen_time,
                "spend"                           => $ad->spend,
                "clicks"                          => $ad->clicks,
                "total_page_views"                => $ad->total_page_views,
                "story_opens"                     => $ad->story_opens,
                "currency"                        => $ad->currency,
                "reach"                           => $ad->reach,
                "cost_per_fifteen_sec_video_view" => $ad->cost_per_fifteen_sec_view,
                "frequency"                       => $ad->frequency,
                "optimization_goal"               => $ad->optimization_goal,
                "end_time"                        => $ad->end_time,
                "data_date"                       => self::$data_date,
            ]);
            // fetch CRM Data
            $CRMData = self::fetchCRMData($connectionModel, $ad->product_code);
            // parse data
            $deliverd        = (int)$CRMData->get("Deliverd");
            $logis_cancelled = (int)$CRMData->get("LogCanceld");
            $total_picked_up = (int)$CRMData->get("Pickedup");
            $picked_up       = $total_picked_up - ($logis_cancelled + $deliverd);
            // update CRM data
            $historyAdModel->update([
                "crm_total_orders"    => (int)$CRMData->get("total"),
                "crm_confirm"         => (int)$CRMData->get("confirm"),
                "crm_repeated"        => (int)$CRMData->get("repeated"),
                "crm_cancelled"       => (int)$CRMData->get("cancel"),
                "crm_total_pickedup"  => (int)$CRMData->get("Pickedup"),
                "crm_pickedup"        => AdvertisementUtil::round($picked_up),
                "crm_logis_deliverd"  => (int)$CRMData->get("Deliverd"),
                "crm_logis_cancelled" => $logis_cancelled,
                "crm_total_sale"      => AdvertisementUtil::round($CRMData->get("TotalSale")),
                "crm_product_cost"    => AdvertisementUtil::round($CRMData->get("ProductCost")),
                "crm_delivery_cost"   => AdvertisementUtil::round($CRMData->get("delivery_cost")),
            ]);
        }
    }

    public static function storeHistoryAdSetData($adSetData, $campaignModel)
    {
        foreach ($adSetData as $adset) {
            $adsetModel = HistoryAdsetMU::create([
                "adset_id"           => $adset->ad_set_id,
                "server_campaign_id" => $adset->campaign_id,
                "code"               => rand(1000, 9999999),
                "name"               => $adset->name,
                "delivery_status"    => $adset->delivery_status,
                "daily_budget"       => $adset->daily_budget,
                "currency"           => $adset->currency,
                "bid"                => $adset->bid,
                "pixel_id"           => $adset->pixel_id,
                "start_time"         => $adset->start_time,
                "end_time"           => $adset->end_time,
                "data_date"          => self::$data_date,
                "data_timestamp"     => Carbon::now(),
                "campaign_id"        => $campaignModel->id,
            ]);
            //3. store ads
            self::storeHistoryAds($adsetModel);
        }
    }

    public static function storeConnectionData($historyAdModel, $connectionData)
    {
        $connectionData['code']          = rand(9999, 9999999999);
        $connectionData['history_ad_id'] = $historyAdModel->id;
        $muConnectionModel               = ConnectionMU::create($connectionData);
        return $muConnectionModel;
    }

    public static function fetchCRMData($connectionModel, $product_code)
    {
        $dateRange = self::$data_date;
        $body      = [
            "from"    => $dateRange,
            "to"      => $dateRange,
            "product" => [$product_code],
            "ad_key"  => [$connectionModel->code],
        ];
        $response = Teebalhoor::alhoorCompanies($connectionModel->country, $body, "/api/getOrders");
        $data =  $response->json('data');
        return collect($data);
    }

    //store campaigns data
    public static function storeHistoryCampaignData()
    {
        foreach (self::$campaign_data as $cam) {
            $connectionModel = Connection::where('server_campaign_id', $cam->campaign_id)->first();
            $campaignModel   = HistoryCampaignMU::create([
                "code"              => rand(100000, 9999999999),
                "campaign_id"       => $cam->campaign_id,
                "name"              => $cam->name,
                "server_account_id" => $connectionModel->server_account_id,
                "objective_type"    => $cam->objective_type,
                "budget_mode"       => $cam->budget_mode,
                "budget"            => $cam->budget,
                "campaign_type"     => $cam->campaign_type,
                "delivery_status"   => $cam->delivery_status,
                "objective"         => $cam->objective,
                "start_time"        => $cam->start_time,
                "end_time"          => $cam->end_time,
                "data_date"         => self::$data_date,
                "ad_account_id"     => $connectionModel->ad_account_id,
                "data_timestamp"    => Carbon::now(),
                "server_created_at" => $cam->server_created_at,
                "server_updated_at" => $cam->server_updated_at,
            ]);
            //2. store ad set data
            self::storeHistoryAdSetData(self::$ad_set_data, $campaignModel);
        }
    }
}
