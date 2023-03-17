<?php

namespace App\Repositories\Advertisement;

use App\Models\Advertisement\HistoryAdset;
use App\Repositories\Repository;
use Illuminate\Support\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class HistoryAdsetRepository extends Repository
{

    public function createOrUpdateAdset(array $adset_attributes, string $campaign_id, Carbon $timestamp)
    {

        // try {
        //code...

        if ($adset_attributes["currency"] == "AED") {


            if (array_key_exists("bid", $adset_attributes)) {
                $bid                     = $adset_attributes["bid"] / 3.76;
                $adset_attributes["bid"] = AdvertisementUtil::round($bid);
            }

            if (array_key_exists("daily_budget", $adset_attributes)) {
                $daily_budget                     = $adset_attributes["daily_budget"] / 3.76;
                $adset_attributes["daily_budget"] = AdvertisementUtil::round($daily_budget);
            }
        }
        if (array_key_exists("bid_strategy", $adset_attributes))
            $adset_attributes["bid_strategy"] = $adset_attributes["bid_strategy"];
        $condition = [
            ["adset_id", "=", $adset_attributes["adset_id"]],
            ["server_campaign_id", "=", $adset_attributes["server_campaign_id"]],
            ["campaign_id", "=", $campaign_id],
            ["data_date", "=", $timestamp->toDateString()],
        ];
        $adset = HistoryAdset::where($condition)->first();
        if ($adset) {
            unset($adset_attributes["adset_id"]);
            unset($adset_attributes["data_date"]);
            unset($adset_attributes["server_campaign_id"]);
            unset($adset_attributes["campaign_id"]);
            $adset->update($adset_attributes);
            return $adset;
        }
        $adset_attributes["campaign_id"]    = $campaign_id;
        $adset_attributes["data_date"]      = $timestamp->toDate();
        $adset_attributes["data_timestamp"] = $timestamp;
        $adset_attributes["code"]           = uniqueCode(HistoryAdset::class, "ADS");

        return HistoryAdset::create($adset_attributes);
        // } catch (\Throwable $th) {
        //     return $th->getMessage();
        // }
    }

    public static function updateAdsetBidBudget(array $adset_attributes, $request = null)
    {

        try {



            $condition = [
                ["adset_id", "=", $adset_attributes["adset_id"]],
                ["server_campaign_id", "=", $adset_attributes["server_campaign_id"]],
                ["data_date", "=", Carbon::now()->toDateString()],
            ];
            $adset = HistoryAdset::where($condition)->first();

            if ($adset) {
                unset($adset_attributes["adset_id"]);
                unset($adset_attributes["server_campaign_id"]);

                $adset->update($adset_attributes);
                // get history
                $adset->changesHistory()->create(["value" => $request->value, "column_name" => $request->type, "user_id" => Auth::user()->id]);
                return response()->json($adset, Response::HTTP_OK);
            }
            return response()->json($adset, Response::HTTP_NO_CONTENT);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return 'item_not_found';
    }
}
