<?php

namespace App\Repositories\AdvetisementUpload;

use App\Models\Advertisement\AdAccount;
use App\Models\AdvetisementUpload\HistoryCampaignMU;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use App\Repositories\Repository;
use Illuminate\Support\Carbon;

class HistoryCampaignMURepository extends Repository
{

    public function createOrUpdateCampaign(array $campaign_attributes, AdAccount $account, Carbon $timestamp)
    {
        $condition = [
            ["server_account_id", "=", $account->account_id],
            ["ad_account_id", "=", $account->id],
            ["campaign_id", "=", $campaign_attributes["campaign_id"]],
            ["data_date", "=", $timestamp->toDateString()],
        ];
        $campaign = HistoryCampaignMU::where($condition)->first();
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
        $campaign_attributes["code"]              = uniqueCode(HistoryCampaignMU::class, "CMP");
        return HistoryCampaignMU::create($campaign_attributes);
    }
    public function fetchItems($request)
    {

        try {
              $relations                  = [
                'adAccount:id,name', 'campaignAdsets:id,server_campaign_id,bid',
                "platform:platforms.id,platforms.platform_name",
            ];
            $queryBuilder               = new UriQueryBuilder(new HistoryCampaignMU(), $request);
            $queryBuilder->itemsPerPage = 20;
            $today                      = Carbon::today()->toDateTimeString();
            $query                      = $queryBuilder->query->with($relations);

            if ($request->status) {
                $query = $query->where("status", $request->status);
            }
            $total    = $query->get()->count();
            $getCount = false;

            if ($request->query->has("search_by_id")) {
                $data = $this->searchCode($query, $request, [], false, "code", false, false);
                return $getCount ?
                $total : response()->json(
                    ["result" => true, "total" => $total, "item" => $data]
                );
            }
            $searchInColumns     = ["campaign_id", "code", "campaign_type", "name", "status", "objective", "objective_type", "budget", "budget_mode"];
            $query               = $this->searchContent($query, $searchInColumns, $request->input('searchContent'));
            $queryBuilder->query = $query;
            $paginate            = $queryBuilder->build()->paginate()->getData();
            return $getCount ?
            $total : response()->json(
                ["result" => true, "total" => $total, "items" => $paginate->data]
            );
        } catch (\Throwable$th) {
            return response()->json(["result" => false, "code" => $th->getMessage()]);
        }
    }
}
