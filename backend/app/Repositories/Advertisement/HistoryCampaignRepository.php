<?php

namespace App\Repositories\Advertisement;

use App\Models\Advertisement\AdAccount;
use App\Models\Advertisement\HistoryCampaign;
use App\Repositories\Advertisement\AdvertisementUtil;
use App\Repositories\Repository;
use Illuminate\Support\Carbon;

class HistoryCampaignRepository extends Repository
{

    public function createOrUpdateCampaign(array $campaign_attributes, AdAccount $account, Carbon $timestamp)
    {
        if ($account->currency == "AED") {
            if (array_key_exists("budget", $campaign_attributes)) {
                $budget                        = (float)$campaign_attributes["budget"];
                $budget                        = $budget / 3.76;
                $campaign_attributes["budget"] = AdvertisementUtil::round($budget);
            }
        }
        $condition = [
            ["server_account_id", "=", $account->account_id],
            ["ad_account_id", "=", $account->id],
            ["campaign_id", "=", $campaign_attributes["campaign_id"]],
            ["data_date", "=", $timestamp->toDateString()],
        ];
        $campaign = HistoryCampaign::where($condition)->first();
        if ($campaign) {
            unset($campaign_attributes["campaign_id"]);
            unset($campaign_attributes["data_date"]);
            unset($campaign_attributes["ad_account_id"]);
            unset($campaign_attributes["server_account_id"]);
            $campaign->update($campaign_attributes);
            return $campaign;
        }
        $campaign_attributes["server_account_id"] = $account->account_id;
        $campaign_attributes["ad_account_id"]     = $account->id;
        $campaign_attributes["data_date"]         = $timestamp->toDate();
        $campaign_attributes["data_timestamp"]    = $timestamp;
        $campaign_attributes["code"]              = uniqueCode(HistoryCampaign::class, "CMP");
        return HistoryCampaign::create($campaign_attributes);
    }
}
