<?php

namespace App\Http\Controllers\OnlineSalesManagement;

use App\Http\Controllers\Controller;
use App\Models\Advertisement\ItemStatus;
use App\Models\Advertisement\Project;
use App\Models\CloudAttachment;
use App\Models\Company;
use App\Models\Country;
use App\Models\OnlineSalesManagement\OnlineSales;
use App\Repositories\Files\CloudinaryFileUtils;
use Illuminate\Http\Request;
use App\Repositories\OnlineSalesManagement\OnlineSalesRepository;
use App\Repositories\OnlineSalesManagement\OnlineSalesUtils;
use Carbon\Carbon;
use Illuminate\Http\Response;

use Illuminate\Support\Facades\Http;

class OnlineSalesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private $repository;

    public function __construct()
    {
        $this->repository = new OnlineSalesRepository();
    }
    public function index(Request $request)
    {

        return  $this->repository->paginate($request);
    }
    public function store(Request $request)
    {
        $request->validate($this->repository->storeRules());
        return $this->repository->store($request);
    }

    public function lastItemCode()
    {
        try {
            $OnlineSales = new OnlineSales();
            $max_code = $OnlineSales->withTrashed()->whereNotNull('max_code')->first('max_code');
            $final = $this->findLastAndUniqueCode($max_code);
            return $final;
        } catch (\Throwable $th) {
            return response()->json($th->getMessage());
        }
    }
    public function findLastAndUniqueCode($max_code)
    {

        $final = '';
        if ($max_code) {
            $text = substr($max_code->max_code, 0, 2);
            $code = substr($max_code->max_code, 2);
            if ($code == 99) {
                $text++;
                $code = 1;
            } else {
                $code++;
            }
            $final = $text . $code;
        } else {
            $final = 'SF1';
        }
        $OnlineSales = new OnlineSales();
        $checkCode = $OnlineSales->withTrashed()->where('product_code', $final)->first('product_code as max_code');
        if ($checkCode)
            return $this->findLastAndUniqueCode($checkCode);
        return $final;
    }
    public function incrementItem($product_code)
    {
        $OnlineSales = new OnlineSales();
        $checkCode = $OnlineSales->where('product_code', $product_code)->first('product_code as max_code');
        if ($checkCode)
            $product_code =  $this->findLastAndUniqueCode($checkCode);
        return $product_code;
    }
    public function checkItemUnique($product_code)
    {
        $OnlineSales = new OnlineSales();
        $checkCode = $OnlineSales->where('product_code', $product_code)->first('product_code');
        return $checkCode ? 'exist' : 'not_exist';
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\OnlineSalesManagement\OnlineSales  $OnlineSales
     * @return \Illuminate\Http\Response
     */
    public function show(OnlineSales $OnlineSales)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\OnlineSalesManagement\OnlineSales  $OnlineSales
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return $this->repository->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\OnlineSalesManagement\OnlineSales  $OnlineSales
     * @return \Illuminate\Http\Response
     */

    public function fetchNote(Request $request, $product_code)
    {
        return $this->repository->fetchNote($request, $product_code);
    }
    public function fetchHistoryStatus(Request $request, $item_code)
    {
        return $this->repository->fetchHistoryStatus($request, $item_code);
    }
    public function storeNote(Request $request)
    {
        $request->validate($this->repository->noteRules());
        if ($request->id)
            return $this->repository->updateNote($request);
        return $this->repository->storeNote($request);
    }
    public function deleteNote(Request $request, $id)
    {
        return $this->repository->deleteNote($request, $id);
    }
    public function destroy(OnlineSales $OnlineSales)
    {
        //
    }
    public function fetchFilterItems(String $filter_type)
    {
        $response = ["type" => $filter_type];
        switch ($filter_type) {
            case 'countries':
                $response["items"] = Country::whereHas("onlineSales")->select(["id", "name", "iso2"])->get();
                break;
            case 'companies':
                $response["items"] = Company::whereHas("onlineSales")->select(["id", "name", "logo"])->get();
                break;
            case 'projects':
                $response["items"] = Project::whereHas("onlineSales")->select(["id", "name"])->get();
                break;
            case 'products':
                $response["items"] = OnlineSales::select(["id", "product_name", 'product_name_arabic', 'product_code'])->get();
                break;
            default:
                return response()->json(["not_allowed_tab" => true], 404);
        }
        return response()->json($response);
    }

    public function changeStatus(Request $request)
    {
        return $this->repository->changeRecordStatus($request);
    }
    public function changeItemStatus(Request $request)
    {
        try {
            date_default_timezone_set("Asia/Dubai");
            ItemStatus::where('item_code', $request->item_code)->where('country_id', $request->country_id)->where('isActive', true)->update(['isActive' => false, 'end_date' => date('Y-m-d H:i:s')]);
            if (OnlineSales::where('product_code', $request->item_code)->first()) {
                if($request->dataAddedToHaneefSys)
                    OnlineSales::where('product_code', $request->item_code)->update(['status' => $request->item_status['status'],'page_link'=>$request->dataAddedToHaneefSys['page_link']]);
                else
                    OnlineSales::where('product_code', $request->item_code)->update(['status' => $request->item_status['status']]);
            }
            ItemStatus::create([
                'item_code' => $request->item_code,
                'country_id' => $request->country_id,
                'item_status' => $request->item_status['status'],
                'color' => $request->item_status['color'],
                'created_by' => auth()->user()->id,
                'created_at' => date('Y-m-d H:i:s'),
                'end_date' => null,
            ]);
            if($request->dataAddedToHaneefSys){
                $value = $request->dataAddedToHaneefSys;
            switch($value['sales_type']){
                case 'Quick Card Sales':
                    $type = 2;
                    break;
                case 'WhatsApp Sales':
                    $type = 1;
                    break;
                default:
                    $type = 0;
                    break;
            }
               $response = Http::post('https://api.teebalhoor.net/public/api/add-product', [
                'pcode' => $value['pcode'],
                'pname' => $value['pname'],
                'sales_type' => $type,
                'company' => strtolower($value['company']),
                'page_link' => $value['page_link'],
            ]);
            if ($response->ok()) {
                // Request was successful, do something with the response
                return response()->json($response->json(), Response::HTTP_CREATED);
            } else {
                // Request failed, handle the error
                return response()->json($response->status(), Response::HTTP_CREATED);
            }
            }
            return response()->json(true, Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public  function getOnlineSalesProfileInfo(Request $request)
    {

        $start_date = Carbon::parse($request->start_date);
        $end_date = Carbon::parse($request->end_date);
        if ($start_date->gt($end_date)) {
            $request->merge([
                "start_date" => $end_date->toDateString(),
                "end_date" => $start_date->toDateString(),
            ]);
        }
        return  OnlineSalesUtils::ExtraInfo($request);
    }

    public function deleteProduct(Request $request)
    {
        return $this->repository->deleteProduct($request);
    }
}
