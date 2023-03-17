<?php

namespace App\Repositories\AdvetisementUpload;

use App\Models\AdvetisementUpload\HistoryAdsetMU;
use Illuminate\Support\Carbon;
use App\Repositories\Repository;

class HistoryAdsetMURepository extends Repository
{

    public function createOrUpdateAdset(array $adset_attributes, string $campaign_id, Carbon $timestamp)
    {
        $condition = [
            ["adset_id", "=", $adset_attributes["adset_id"]],
            ["server_campaign_id", "=", $adset_attributes["server_campaign_id"]],
            ["campaign_id", "=", $campaign_id],
            ["data_date", "=", $timestamp->toDateString()],
        ];
        $adset = HistoryAdsetMU::where($condition)->first();
        if ($adset) {
            unset($adset_attributes["adset_id"]);
            unset($adset_attributes["data_date"]);
            unset($adset_attributes["server_campaign_id"]);
            unset($adset_attributes["campaign_id"]);
            $adset->update($adset_attributes);
            return $adset;
        }
        $adset_attributes["campaign_id"] = $campaign_id;
        $adset_attributes["data_date"] = $timestamp->toDate();
        $adset_attributes["data_timestamp"] = $timestamp;
        $adset_attributes["code"] = uniqueCode(HistoryAdsetMU::class, "ADS");

        return HistoryAdsetMU::create($adset_attributes);
    }
}
