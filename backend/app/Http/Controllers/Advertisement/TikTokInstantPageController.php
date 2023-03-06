<?php


namespace App\Http\Controllers\Advertisement;

use App\Http\Controllers\Controller;
use App\Models\Advertisement\Connection;
use App\Models\Company;
use App\Models\Country;
use App\Models\TiktokWebhook;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TikTokInstantPageController extends Controller
{

    public function getTiktokOrder(Request $request)
    {

        try {
            //code...
            $orders = TiktokWebhook::get();
            return response()->json($orders);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['result' => false, 'error', $th->getMessage()], 400);
        }
    }



    public function instantOrders(Request $request, $account_name)
    {
        // return $request->all();
        // return response()->json(['result' => true], 200);
        try {
            if ($request->entry) {
                $data = $request->entry[0];
                $lead_data = $data['changes'];
                $crm_data = [];

                foreach ($lead_data as $value) {

                    if (str_contains($value['field'], 'اسم') || str_contains($value['field'], 'الاسم') || str_contains($value['field'], 'name')) {
                        $crm_data['name'] = $value['value'];
                    }

                    if (str_contains($value['field'], 'العنوان') || str_contains($value['field'], 'address')) {
                        $crm_data['address'] = $value['value'];
                    }

                    // : "اختر الامارة :",
                    if (str_contains($value['field'], 'الامارة') || str_contains($value['field'], 'المدينة')  || str_contains($value['field'], 'city')) {
                        $crm_data['city'] = $value['value'];
                    }

                    if (str_contains($value['field'], 'رقم') || str_contains($value['field'], 'phone_number')) {
                        $crm_data['phone'] = $value['value'];
                    }

                    // "اختار العرض :",
                    if (str_contains($value['field'], 'اختار') || str_contains($value['field'], 'offer')) {

                        if (!str_contains($value['value'], '+')) {
                            $array =  explode(" ", $value['value']);
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
                        } else {
                            // one plus
                            $array =  explode(" ", $value['value']);
                            $qty_and_price = [];
                            for ($i = 0; $i < count($array); $i++) {
                                $num =  $array[$i];
                                $int = (int)$num;
                                if ($int > 0) {
                                    $qty_and_price[] = $int;
                                }
                            }
                            $crm_data['qty'] = @$qty_and_price[0] + @$qty_and_price[1];
                            $crm_data['price'] = @$qty_and_price[2];
                        }
                    }
                }
                $connection =   Connection::with('adAccount')->whereServerAdId($data['ad_id'])->first();
                $country =   Country::whereId($connection->country_id)->first();
                $company =   Company::whereId($connection->company_id)->first();


                $crm_data['pcode'] = $connection['pcode'];
                $po = $connection['generated_link'];
                $po = strrchr($po, "po=");
                $po = explode("=", $po);
                $crm_data['po'] = $po[1];
                $crm_data['ad_key'] =  $connection['code'];
                $crm_data['project'] =  $company['name'] == "UAE-Teebalhoor" ? 1 : 0;
                $getDomain = self::getDomain($country['name']);

                $connection = $connection->toArray();
                $crm_data['account_name'] =  $connection["ad_account"]['name'];
                $url = $getDomain['domain'] . "public/api/add-tiktok-order";

                $response = Http::withToken($getDomain['token'])->post($url, $crm_data);
                // TiktokWebhook::create(['event' => json_encode($response->json()), "account_name" => 'crm response']);

                $data = $request->entry[0];
                TiktokWebhook::create(['event' => json_encode($request->all()), "account_name" => $data['page_name']]);
                if ($response) {
                    return $response->json();
                } else {
                    return $response->json();
                }
            }
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function getDomain($county_name)
    {
        $domain = "https://api.teebalhoor.net/";


        switch ($county_name) {
            case 'Iraq':
                $domain = "https://iraqapi.teebalhoor.net/";
                $token = "1|cY6kP8F8Li9CXeMW2beFasi7m6KAVjWM1E9D7vVP";
                break;
            case 'Kuwait':
                $domain = "https://qatarapi.teebalhoor.net/";
                $token = "244|HWu2iir3oXsmZczZvAwhYRfL2DNzl7vlQ0m1Mj3t";
                break;
            case 'Qatar':
                $domain = "https://qatarapi.teebalhoor.net/";
                $token = "244|HWu2iir3oXsmZczZvAwhYRfL2DNzl7vlQ0m1Mj3t";
                break;
            default:
                $domain = "https://api.teebalhoor.net/";
                $token = "1236|zDPnJxH4v9hf3fcgHHzVNhDbETG6X9AU7KmslMFi";
                break;
        }

        return ["domain" => $domain, "token" => $token];
    }
}
// dddddd
