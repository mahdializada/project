<?php

namespace App\Http\Controllers\CrmOrder;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->validationRules($request));
        return self::storeProduct($request->all());
    }

    public function storeProduct($body)
    {
        try {
            $token = null;

            $domain = self::getDomain($body);
            $url = $domain[0] . "public/api/add-crm-order";
            // $url = "http://192.168.3.27:8000/api/add-crm-order";

            $response = Http::withToken($domain[1])->post($url, $body);
            return $response;
            if ($response) {
                return $response->json();
            }
        } catch (\Throwable $th) {
            return $th->getMessage();
            throw new Exception($th->getMessage(), 9000);
        }
    }
    public function getDomain($body)
    {
        $domain = "https://api.teebalhoor.net/";
        $token = "1236|zDPnJxH4v9hf3fcgHHzVNhDbETG6X9AU7KmslMFi";
        if (array_key_exists('country', $body)) {
            switch ($body['country']) {
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
        }
        return [$domain, $token];
    }
    public function validationRules($request): array
    {

        $rules = [
            'pcode' => ['required', 'array'],
            'qty' => ['required', 'array'],
            // 'delay' => Rule::requiredIf(fn () => $request->status == 5),
            'project' => ['required'],
            'ad_id' => ['required'],
            'source' => ['required'],
            'price' => ['required'],
            'status' => ['required', "in:1,5"],
            // 'celebrity_code' => Rule::requiredIf(fn () => $request->source == 6),
            // 'landing_link' => [Rule::requiredIf(fn () => $request->source == 7)],
            // 'solution' => [Rule::requiredIf(fn () => $request->source == 3)],
            'notes' => ['required', 'min:2'],
            // "string", "min:2"
        ];


        return $rules;
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function fetchItems($type, $id, Request $request)
    {

        $result = ["type" => $type];
        switch ($type) {
            case "projects":
                $result["items"] = self::getProjects($request);
                return  response()->json($result);
            case "addresses":
                $result["items"] = self::getAddresse($id);
                return  response()->json($result);
            case "products":
                $result["items"] = self::getProducts();
                return  response()->json($result);

            default:
                return response()->json([], 403);
        }
    }

    public function getProjects($request)
    {
        try {

            $domain = self::getDomain($request->all());
            $url = $domain[0] . "public/api/projects";

            $response = Http::withToken($domain[1])->get($url);
            if ($response->successful()) {
                return $response->json()["data"];
            }
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage(), 9000);
        }
    }

    public function getAddresse($id)
    {
        try {
            $token = "1236|zDPnJxH4v9hf3fcgHHzVNhDbETG6X9AU7KmslMFi";
            $url = "https://api.teebalhoor.net/public/api/get-previous-orders/$id";

            $response = Http::with($token)->get($url);
            if ($response->successful()) {
                return $response->json()["data"];
            }
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage(), 9000);
        }
    }
    public function getProducts()
    {
        try {
            $token = null;

            $url = "https://api.teebalhoor.net/public/products";

            $response = Http::get($url);
            if ($response) {
                return $response->json();
            }
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage(), 9000);
        }
    }
    public function getCompanyProducts(Request $request)
    {

        try {
            $company = $request->company;
            $company_name = $request->companyName;
            if ($request->api_name) {
                $api_name = $request->api_name;
            } else {
                $api_name = '';
            }
            $domain = self::getDomain($request->all());

            $url = $request->country == "Iraq" ? $domain[0] . "public/products?with_landing_link=true&company_name=$company_name&page_type=0" : $domain[0] . "public/products2?company_name=$api_name";
            $response = Http::withToken($domain[1])->get($url);
            return $response->json();
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage(), 9000);
        }
    }

    public function getOrders()
    {
        try {
            $header = ["Access-Token" => "9bcc647d477dca55ed8650b0491156d5bd9a2342", "Content-Type" => "application/json"];

            $url = "https://business-api.tiktok.com/open_api/v1.3/page/lead/task/download/?advertiser_id=6854883353850544133&task_id=7171747813737791746";

            $response = Http::withHeaders($header)->get($url);
            if ($response) {
                return $response;
            }
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage(), 9000);
        }
    }
}
