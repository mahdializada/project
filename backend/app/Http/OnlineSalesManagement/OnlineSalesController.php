<?php

namespace App\Http\Controllers\OnlineSalesManagement;

use App\Http\Controllers\Controller;
use App\Models\Advertisement\Project;
use App\Models\Company;
use App\Models\Country;
use App\Models\OnlineSalesManagement\OnlineSales;

use Illuminate\Http\Request;
use App\Repositories\OnlineSalesManagement\OnlineSalesRepository;
use App\Repositories\OnlineSalesManagement\OnlineSalesUtils;
use Carbon\Carbon;

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
            $max_code = $OnlineSales->whereNotNull('max_code')->first('max_code');
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
        $checkCode = $OnlineSales->where('product_code', $final)->first('product_code');
        if ($checkCode)
            return $this->findLastAndUniqueCode($checkCode);
        return $final;
    }
    public function incrementItem($product_code)
    {
        $OnlineSales = new OnlineSales();
        $checkCode = $OnlineSales->where('product_code', $product_code)->first('product_code');
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
}
