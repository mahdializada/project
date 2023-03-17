<?php

namespace App\Jobs;

use App\Models\Advertisement\Connection;
use App\Models\Advertisement\HistoryAd;
use App\Models\Country;
use App\Models\ProductManagement\Request;
use App\Repositories\Advertisement\AdvertisementUtil;
use App\Repositories\Advertisement\Platforms\Teebalhoor;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RefreshCrmJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private Country  $country;
    private $date;
    public function __construct(Country $country, $request)
    {
        //
        $this->country = $country;


        if ($request->date) {
            $this->date = $request->date;
        } else {
            $this->date =   Carbon::today()->setTimezone("Asia/Dubai")->toDateString();
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //

        try {
            DB::beginTransaction();

            $country_iso = $this->country;
            $connection = collect($this->country->connections);

            $connection_code = $connection->pluck('code')->toArray();
            $p_code = $connection->pluck('pcode')->toArray();
            $crm_item_raw_data = Teebalhoor::fetchAlhoorCompanyTodayProductCRM($country_iso, $p_code, $connection_code, $this->date);
            foreach ($crm_item_raw_data as $crm_raw_data) {
                foreach ($connection->toArray() as $conn) {

                    if ($conn['pcode'] == $crm_raw_data['product'] && $conn['code'] == $crm_raw_data['ad_key']) {

                        $crm_data =    $this->parsCrmData(collect($crm_raw_data));

                        $condition = [
                            ["ad_id", "=", $conn["server_ad_id"]],
                            ["server_adset_id", "=", $conn["server_ad_adset_id"]],
                            ["data_date", "=", $this->date]
                        ];
                        $adset = HistoryAd::where($condition)->first();
                        if ($adset) {
                            $adset->update($crm_data);
                        }
                    }
                }
            }
            DB::commit();

            //code...
            return  response()->json(['result' => true], 201);
        } catch (\Throwable $th) {

            DB::rollBack();
            return  response()->json(['result' => false, 'error' => $th->getMessage()], 400);
        }
    }
    public function parsCrmData(Collection $crm_data)
    {
        $deliverd = (int) $crm_data->get("Deliverd");
        $logis_cancelled = (int) $crm_data->get("LogCanceld");
        $total_picked_up = (int) $crm_data->get("Pickedup");
        $picked_up = $total_picked_up - ($logis_cancelled + $deliverd);
        return [
            "crm_total_orders" => (int) $crm_data->get("total"),
            "crm_confirm" => (int) $crm_data->get("confirm"),
            "crm_repeated" => (int) $crm_data->get("repeated"),
            "crm_cancelled" => (int) $crm_data->get("cancel"),
            "crm_total_pickedup" => (int) $crm_data->get("Pickedup"),
            "crm_pickedup" => AdvertisementUtil::round($picked_up),
            "crm_logis_deliverd" => (int) $crm_data->get("Deliverd"),
            "crm_logis_cancelled" => $logis_cancelled,
            "crm_total_sale" => AdvertisementUtil::round($crm_data->get("TotalSale")),
            "crm_product_cost" => AdvertisementUtil::round($crm_data->get("ProductCost")),
            "crm_delivery_cost" => AdvertisementUtil::round($crm_data->get("delivery_cost")),
            'crm_refresh_date' => Carbon::now()
        ];
    }
}
