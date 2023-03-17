<?php

namespace App\Repositories\AdvetisementUpload;

use App\Models\AdvetisementUpload\HistoryAdMU;
use Illuminate\Support\Carbon;
use App\Repositories\Repository;

class HistoryAdMURepository extends Repository
{

    public function createOrUpdateAd(
        array $ad_attributes,
        string $adset_id,
        Carbon $timestamp
    ) {
        $condition = [
            ["ad_id", "=", $ad_attributes["ad_id"]],
            ["server_adset_id", "=", $ad_attributes["server_adset_id"]],
            ["adset_id", "=", $adset_id],
            ["data_date", "=", $timestamp->toDateString()],
        ];
        $adset = HistoryAdMU::where($condition)->first();
        if ($adset) {
            unset($ad_attributes["ad_id"]);
            unset($ad_attributes["data_date"]);
            unset($ad_attributes["server_adset_id"]);
            unset($ad_attributes["adset_id"]);
            $adset->update($ad_attributes);
            return $adset;
        }
        $ad_attributes["adset_id"] = $adset_id;
        $ad_attributes["data_date"] = $timestamp->toDate();
        $ad_attributes["data_timestamp"] = $timestamp;
        $ad_attributes["code"] = uniqueCode(HistoryAdMU::class, "AD");
        return HistoryAdMU::create($ad_attributes);
    }
}
