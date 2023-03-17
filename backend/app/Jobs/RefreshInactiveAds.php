<?php

namespace App\Jobs;

use App\Http\Controllers\Advertisement\ConnectionController;
use App\Models\Advertisement\HistoryAd;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RefreshInactiveAds implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public array $historyAd;
    private $request;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $historyAd, $request)
    {
        $this->historyAd = $historyAd;
        $this->request = $request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $timestamp = Carbon::now();
        $condition = [
            ["ad_id", "=", $this->historyAd["ad_id"]],
            ["server_adset_id", "=   ", $this->historyAd["server_adset_id"]],
            ["adset_id", "=",  $this->historyAd["adset_id"]],
            ["data_date", "=", $timestamp->toDateString()],
        ];
        $ad = HistoryAd::where($condition)->first();
        if (!$ad) {
            $ad_attributes["adset_id"] = $this->historyAd["adset_id"];
            $ad_attributes["ad_id"] = $this->historyAd["ad_id"];
            $ad_attributes["server_adset_id"] = $this->historyAd["server_adset_id"];
            $ad_attributes["server_account_id"] = $this->historyAd["server_account_id"];
            $ad_attributes["data_date"] = $timestamp->toDateString();
            $ad_attributes["data_timestamp"] = $timestamp;
            $ad_attributes["ad_pcode"] = $this->historyAd['ad_pcode'];
            $ad_attributes["ad_name"] = $this->historyAd['ad_name'];
            $ad_attributes["code"] = uniqueCode(HistoryAd::class, "AD");
            $ad_attributes["sales_type"] = $this->historyAd['sales_type'];
            $ad_attributes["status"] = $this->historyAd['status'];
            HistoryAd::create($ad_attributes);
            ConnectionController::updateInActiveAdset($this->request, $this->historyAd["server_adset_id"], $timestamp);
        }
    }
}
