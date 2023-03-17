<?php

namespace App\Repositories\Advertisement;

use App\Exports\ExportOrders;
use Illuminate\Support\Carbon;
use App\Repositories\Repository;
use App\Models\Advertisement\HistoryAd;
use App\Repositories\Advertisement\AdvertisementUtil;
use Illuminate\Support\Facades\Http;
use Mockery\Undefined;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class HistoryAdRepository extends Repository
{

    public function createOrUpdateAd(
        array $ad_attributes,
        string $adset_id,
        Carbon $timestamp,
        string $ad_pcode,
        $extra = null,
        $sales_type,
        $refreshColumn = 'snapchat_refresh_date'
    ) {

        if ($ad_attributes["currency"] == "AED") {
            if (array_key_exists("spend", $ad_attributes)) {
                $spend = (float) $ad_attributes["spend"];
                $spend = $spend / 3.76;
                $ad_attributes["spend"] = AdvertisementUtil::round($spend);
            }
        }
        $condition = [
            ["ad_id", "=", $ad_attributes["ad_id"]],
            ["server_adset_id", "=", $ad_attributes["server_adset_id"]],
            ["adset_id", "=", $adset_id],
            ["data_date", "=", $timestamp->toDateString()],
        ];
        $adset = HistoryAd::where($condition)->first();
        if ($adset) {
            if ($extra != null) {
                $ad_attributes = $this->fetchAdDateColumn($ad_attributes, $extra);
                unset($ad_attributes["data_timestamp"]);
                if (array_key_exists('platform', $extra))
                    unset($ad_attributes["data_timestamp"]);
            } else {
                $ad_attributes[$refreshColumn] = Carbon::now();
            }


            unset($ad_attributes["data_date"]);
            unset($ad_attributes["ad_id"]);
            unset($ad_attributes["server_adset_id"]);
            unset($ad_attributes["adset_id"]);
            unset($ad_attributes["ad_pcode"]);

            $ad_attributes["crm_refresh_date"] = Carbon::now();
            $adset->update($ad_attributes);
            return $adset;
        }
        $ad_attributes["adset_id"] = $adset_id;
        $ad_attributes["data_date"] = $timestamp->toDate();
        $ad_attributes["data_timestamp"] = $timestamp;
        $ad_attributes["ad_pcode"] = $ad_pcode;
        $ad_attributes["code"] = uniqueCode(HistoryAd::class, "AD");
        $ad_attributes["sales_type"] = $sales_type;
        return HistoryAd::create($ad_attributes);
    }

    public static function fetchAdDateColumn($ad_attributes, $extra)
    {

        try {
            if (array_key_exists('platform', $extra)) {
                switch ($extra['platform']) {
                    case 'facebook':
                        $ad_attributes["facebook_refresh_date"] = Carbon::now();
                        break;
                    case 'snapchat':
                        $ad_attributes["snapchat_refresh_date"] = Carbon::now();

                        break;
                    case 'tiktok':
                        $ad_attributes["tikrok_refresh_date"] = Carbon::now();
                        break;
                    case 'google ads':
                        $ad_attributes["google_ads_refresh_date"] = Carbon::now();

                        break;
                    default:
                        return null;
                }
            }

            return $ad_attributes;
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }

    public function checkingAds($request)
    {
        try {
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $spreadsheet = IOFactory::load($file->getRealPath());
                $sheet        = $spreadsheet->getActiveSheet();
                $row_limit    = $sheet->getHighestDataRow();
                $row_range    = range(3, $row_limit);
                $data = [];
                $historyAd = HistoryAd::selectRaw('ad_id')->distinct('ad_id')->get();
                foreach ($row_range as $key => $row) {
                    if ($sheet->getCell('A' . $row)->getValue()) {
                        if (!$historyAd->contains('ad_id', $sheet->getCell('A' . $row)->getValue())) {
                            $data[] = [
                                'ad_id' => $sheet->getCell('A' . $row)->getValue(),
                                'id' => $key + 3,
                            ];
                        }
                    }
                }
                return response()->json($data);
            }
        } catch (\Throwable $th) {
            return response()->json($th->getMessage());
        }
    }
    public function checkCrmOrders($request)
    {
        try {
            if ($request->hasFile('file')) {

                // $rows = Excel::toArray(new ExportOrders, $request->file('file')->store('temp'));
                $file = $request->file('file');



                $spreadsheet = IOFactory::load($file->getRealPath());
                $sheet        = $spreadsheet->getActiveSheet();
                $row_limit    = $sheet->getHighestDataRow();
                $row_range    = range(0, $row_limit);
                // return $row_range;
                $data = [];
                foreach ($row_range as $key => $row) {
                    $order = json_decode($sheet->getCellByColumnAndRow(2, $key)->getValue());
                    $crm_data = [];
                    if ($order) {
                        foreach ($order as $value) {
                            if ($value->question) {
                                if (str_contains($value->question, 'اسم')) {
                                    $crm_data['name'] = $value->answer;
                                }

                                if (str_contains($value->question, 'العنوان')) {
                                    $crm_data['address'] = $value->answer;
                                }

                                // : "اختر الامارة :",
                                if (str_contains($value->question, 'الامارة')) {
                                    $crm_data['city'] = $value->answer;
                                }

                                if (str_contains($value->question, 'رقم')) {
                                    $crm_data['phone'] = preg_replace('/\s+/', '', $value->answer);
                                }

                                // "اختار العرض :",
                                if (str_contains($value->question, 'اختار')) {
                                    $array =  explode(" ", $value->answer);
                                    $qty_and_price = [];
                                    for ($i = 0; $i < count($array); $i++) {
                                        $num =  $array[$i];
                                        $int = (int)$num;
                                        if ($int > 0) {
                                            $qty_and_price[] = $int;
                                        }
                                    }
                                    $crm_data['qty'] = @$qty_and_price[0];
                                    $crm_data['price'] = @$qty_and_price[1];
                                }
                            }
                        }
                    }
                    $pcode = explode("-",  $sheet->getCellByColumnAndRow(1, $key)->getValue());
                    $data[] = (object) array_merge([
                        'pcode' => preg_replace('/\s+/', '', $pcode),

                    ], ['key' => $key], $crm_data);
                }
                array_shift($data);
                array_shift($data);
                $data = [
                    "data" => $data, "start_date" => $request->start_date,
                    "end_date" => $request->end_date
                ];
                $url = "https://api.teebalhoor.net/public/api/check-order";
                $response = Http::post($url, $data);

                if ($response->successful()) {
                    $data2 =  $response->json();

                    $result = $this->arrangeOrders($data, $data2['not_found']);
                    return $result;
                    return response()->json([$data2, $data]);
                }
            }
        } catch (\Throwable $th) {
            return "error";
            return response()->json($th->getMessage());
        }
    }
    public function arrangeOrders($allData, $notFound)
    {
        $result = [];
        try {

            foreach ($allData['data'] as $key => $data) {
                $query = Collect($notFound);
                $query = $query->firstWhere('key', $data->key);
                $result[] =  (object) array_merge(["exist" => $query ? "not_exist" : "exist"], (array)$data);
            }

            return $result;
        } catch (\Throwable $th) {
            return response()->json($th->getMessage());
        }
    }
}
