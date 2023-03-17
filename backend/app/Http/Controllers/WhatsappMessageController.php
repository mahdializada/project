<?php

namespace App\Http\Controllers;

use App\Models\Advertisement\Connection;
use App\Models\OnlineSalesManagement\OnlineSales;
use App\Models\OtpCode;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;

class WhatsappMessageController extends Controller
{
    public static function sendMessage($phone = "+93748418765", $name = 'rohullah hussaini', $application_name = 'whatsapp')
    {

        $bearer = "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiYjc3NWEwMTQwOTJlZTRhNTFkMWI3N2JhNDM3YWUwZTY1NDFmMzRlYzU3ZjRiZGZlOGE2ODJmMTQ0ZjgyMmE1ZThkOGRiNzIyNzZjMjUwNzQiLCJpYXQiOjE2NzQ0NTY4MjguMjA4NTg3LCJuYmYiOjE2NzQ0NTY4MjguMjA4NTg5LCJleHAiOjQ3OTg1OTQ0MjguMjAzMDc1LCJzdWIiOiIzMTgwNDYiLCJzY29wZXMiOltdfQ.k2wL-8em1PerpOKiAb8hTb_t1FL_SA4Krx8c5tQxCwTx3s1KyUbqhPFzo3EESETdlo3BW_oRXvgR8d9Zq71xug";

        $hsm_id = '40734'; //application expired template_id
        $channel_id = '723614';  //application expired chanel_id


        $application_url = env('FRONTEND_URL', 'null') . '/advertisement/applications';

        try {


            $client = new Client(); //GuzzleHttp\Client
            $response = $client->post(
                'https://app.trengo.com/api/v2/wa_sessions',
                [

                    'body' => '{
                        "params":
                            [
                                {
                                    "key":"{{1}}",
                                    "value":"' . $name . '"
                                },
                                {
                                    "key":"{{2}}",
                                    "value":"' . $application_name . '"
                                },
                                {
                                    "key":"{{3}}",
                                    "value":"' . $application_url . '"
                                }
                            ],
                            "hsm_id": "' . $hsm_id . '",
                    "recipient_phone_number": "' . $phone . '"

                        }',

                    'headers' => [
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'Authorization' => $bearer,
                    ],

                ]
            );
            return   ['status' => $response->getStatusCode()];
        } catch (\Exception $e) {

            return response()->json(
                [
                    'result' => false,
                    'message' => $e->getMessage()
                ],
                400
            );
        }

        /*****************************/
    }

    public function sendOtpcode($hsm_id, $phone, $params)
    {
        # code...
        try {

            $bearer = "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiYjc3NWEwMTQwOTJlZTRhNTFkMWI3N2JhNDM3YWUwZTY1NDFmMzRlYzU3ZjRiZGZlOGE2ODJmMTQ0ZjgyMmE1ZThkOGRiNzIyNzZjMjUwNzQiLCJpYXQiOjE2NzQ0NTY4MjguMjA4NTg3LCJuYmYiOjE2NzQ0NTY4MjguMjA4NTg5LCJleHAiOjQ3OTg1OTQ0MjguMjAzMDc1LCJzdWIiOiIzMTgwNDYiLCJzY29wZXMiOltdfQ.k2wL-8em1PerpOKiAb8hTb_t1FL_SA4Krx8c5tQxCwTx3s1KyUbqhPFzo3EESETdlo3BW_oRXvgR8d9Zq71xug";

            $body = [
                "body" => json_encode([
                    "params" => $params,
                    "hsm_id" => strval($hsm_id),
                    "recipient_phone_number" => strval($phone)
                ]),
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => $bearer,
                ],

            ];
            $client = new Client(); //GuzzleHttp\Client
            $response = $client->post(
                'https://app.trengo.com/api/v2/wa_sessions',
                $body
            );
            return   ['status' => $response->getStatusCode()];
        } catch (\Exception $e) {
            return
                [
                    'status' => 500,
                    'message' => $e->getMessage()
                ];
        }
    }

    public  function itemCodeOTP($code, $request)
    {
        // $phones = ["+971562876382", "+93748485799"];
        $phones = ["+93748418765"];
        $campaigns = Connection::where("server_ad_id", "!=", null)->groupBy('server_campaign_id')->where("pcode", $request->pcode)->get();
        $adsets = Connection::where("server_ad_id", "!=", null)->groupBy('server_ad_adset_id')->where("pcode", $request->pcode)->get();
        $ads = Connection::where("server_ad_id", "!=", null)->groupBy('server_ad_id')->where("pcode", $request->pcode)->get();
        $hsm_id = '83894';
        $admin = Auth::user()->firstname . '  ' . Auth::user()->lastname;

        $params = [
            ["key" => "{{1}}", "value" => $admin],
            ["key" => "{{2}}", "value" => $request->pcode],
            ["key" => "{{3}}", "value" => strval(count($campaigns))],
            ["key" => "{{4}}", "value" => strval(count($adsets))],
            ["key" => "{{5}}", "value" =>  strval(count($ads))],
            ["key" => "{{6}}", "value" => strval($code)],
        ];

        foreach ($phones as  $phone) {
            $response = $this->sendOtpcode($hsm_id, $phone, $params);
            if ($response['status'] != 200) {
                return $response;
            }
        }
    }


    public  function onlineSalesOTP($code, $request)
    {
        $phones = ["+971562876382", "+93748485799"];
        $hsm_id = '105414'; // template id https://app.trengo.com/admin/wa_templates/105414
        $admin = Auth::user()->firstname . '  ' . Auth::user()->lastname;

        $online_sales = OnlineSales::with(['country', 'company'])->find($request->record_id);
        $params = [
            ["key" => "{{1}}", "value" => $admin],
            ["key" => "{{2}}", "value" => $request->pcode],
            ["key" => "{{3}}", "value" => $online_sales->country->name],
            ["key" => "{{4}}", "value" => $online_sales->company->name],
            ["key" => "{{5}}", "value" => strval($code)],
        ];
        $response = 'rohullah';
        foreach ($phones as  $phone) {
            $response = $this->sendOtpcode($hsm_id, $phone, $params);
            if ($response['status'] != 200) {
                return response()->json($response, 500);
            }
        }
        return $response;
    }



    public function createOtpCode(Request $request, $sub_system)
    {
        $code = rand(100000, 999999);
        switch ($sub_system) {
            case 'advertisement':
                $response = $this->itemCodeOTP($code, $request);
                break;
            case 'online_sales':
                $response =  $this->onlineSalesOTP($code, $request);
                break;
        }


        if ($response['status'] != 200) {
            return response()->json($response, 500);
        }
        $expire_date =  Carbon::now();
        $expire_date->addMinute(5);
        OtpCode::create([
            'code' => $code,
            'item_code' => $request->pcode,
            'expire_at' => $expire_date
        ]);
        return response()->json(['result' => true]);
    }
}
