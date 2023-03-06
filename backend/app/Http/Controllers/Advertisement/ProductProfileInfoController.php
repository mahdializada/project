<?php

namespace App\Http\Controllers\Advertisement;

use App\Http\Controllers\Controller;
use App\Models\Advertisement\ProductProfileHistory;
use App\Models\Advertisement\ProductProfileInfo;
use App\Models\CloudinaryTempFile;
use App\Models\Company;
use App\Models\Country;
use App\Models\TempFile;
use Illuminate\Http\Request;
use App\Repositories\Advertisement\ProductProfileInfoRepository;
use App\Repositories\Files\CloudinaryFileUtils;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;


class ProductProfileInfoController extends Controller
{
    private ProductProfileInfoRepository $repository;
    public function __construct()
    {
        $this->repository = new ProductProfileInfoRepository();
    }

    public function store(Request $request)
    {
        $request->validate($this->repository->storeRules());
        return $this->repository->store($request);
    }
    public function getProductInfo(Request $request)
    {
        # code...
        $profile_data =  ProductProfileInfo::with(['attachments', 'connections' => function ($query) {
            return $query->select(['pcode', 'landing_page_link'])->groupBy('landing_page_link');
        }])->whereItemCode($request->item_code)->first();
        return response()->json($profile_data);
    }


    public function getProductHistory(Request $request)
    {
        # code...
        $start_date = Carbon::parse($request->start_date);
        $end_date = Carbon::parse($request->end_date);
        if ($start_date->gt($end_date)) {
            $request->merge([
                "start_date" => $end_date->toDateString(),
                "end_date" => $start_date->toDateString(),
            ]);
        }
        $profile_data =  ProductProfileHistory::with('changedBy:id,firstname,lastname,image')
            ->whereBetween(DB::raw('DATE(created_at)'), [$request->start_date, $request->end_date])
            ->where(['profile_id' => $request->id, "column_name" => $request->column])->orderBy('created_at', 'desc')->get();
        return response()->json($profile_data);
    }


    public function getProductGraphInfo(Request $request)
    {
        # code...
        $start_date = Carbon::parse($request->start_date);
        $end_date = Carbon::parse($request->end_date);
        if ($start_date->gt($end_date)) {
            $request->merge([
                "start_date" => $end_date->toDateString(),
                "end_date" => $start_date->toDateString(),
            ]);
        }
        $profile_data =  ProductProfileHistory::with('changedBy:id,firstname,lastname,image')
            ->whereBetween(DB::raw('DATE(created_at)'), [$request->start_date, $request->end_date])
            ->where(['profile_id' => $request->id, "column_name" => $request->column])->orderBy('created_at')->get()->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            });
        return response()->json($profile_data);
    }



    public function updateProductProperty(Request $request, $id)
    {

        try {
            DB::beginTransaction();
            $profile_info = ProductProfileInfo::find($id);
            $json_column = ["prod_new_product_source", "prod_import_source"];

            if (in_array($request->column, $json_column)) {
                $json_data = $profile_info[$request->column];
                if (in_array($request->value, $json_data)) {
                    $temp_aray = [];
                    foreach ($json_data as $value) {
                        if ($value != $request->value) {
                            $temp_aray[] = $value;
                        }
                    }
                    $json_data = $temp_aray;
                } else {
                    array_push($json_data, $request->value);
                }
                $profile_info->update([$request->column =>  json_encode($json_data)]);
            } else {
                if ($request->column != 'prod_profits') {
                    $profile_info->update([$request->column => $request->value]);
                } else {
                    $profile_info->update(['prod_profit' => $request->value, "prod_profitability" => $request->type]);
                }
            }

            $data = [
                'profile_id' => $id,
                'column_name' => $request->column == 'prod_profits' ? 'prod_profit' : $request->column,
                'changed_value' => $request->value,
                'profit_type' => @$request->type,
                'changed_by' => Auth::user()->id
            ];
            $history =    ProductProfileHistory::create($data);
            DB::commit();
            return response()->json($profile_info);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json($th->getMessage(), 400);
        }
    }

    public function updateProductImage(Request $request)
    {
        try {
            DB::beginTransaction();
            $profile = ProductProfileInfo::where('item_code', '=', $request->item_code)->first();
            $result =    CloudinaryFileUtils::updateFiles($profile, $request->item_code, $request->file);

            if (!$result['result']) {
                DB::rollBack();
                return response()->json($result, 500);
            }
            DB::commit();
            return response()->json($profile->load(['attachments']), 200);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json($th->getMessage(), 500);
        }
    }
}
