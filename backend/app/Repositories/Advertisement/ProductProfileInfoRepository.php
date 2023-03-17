<?php

namespace App\Repositories\Advertisement;

use App\Models\Advertisement\ProductProfileHistory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\Repository;
use App\Models\Advertisement\ProductProfileInfo;
use App\Repositories\Files\CloudinaryFileUtils;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

//use Your Model

/**
 * Class ProductProfileInfoRepository.
 */
class ProductProfileInfoRepository extends Repository
{

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $platformModel = new ProductProfileInfo();
            $attributes         = $request->only($platformModel->getFillable());
            $attributes['code'] = rand(1000000, 9999999999);
            $attributes['prod_new_product_source'] = json_encode($request->prod_new_product_source);
            $attributes['prod_import_source'] = json_encode($request->prod_import_source);
            $attributes['prod_image'] = json_encode([]);
            $result =  ProductProfileInfo::create($attributes);
            $storeFile =    CloudinaryFileUtils::storeFiles(
                $result,
                $request->prod_image
            );
            if (!$storeFile['result']) {
                DB::rollBack();
                return response()->json($storeFile, 500);
            }
            $this->initProductHistory($result);
            DB::commit();
            return response()->json($result->load('attachments'), Response::HTTP_CREATED);
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->getMessage());
        }
    }



    public function initProductHistory($profile)
    {
        $profile = $profile->toArray();
        $columns =  [
            'prod_source',
            'prod_sale_goal',
            'prod_style',
            'prod_section',
            'prod_work_type',
            'prod_import_source',
            'prod_cost',
            'prod_available_quantity',
            'prod_max_adver_cost',
            'prod_new_product_source',
            'prod_production_type',
        ];
        foreach ($columns as  $column) {
            $json_column = ["prod_new_product_source", "prod_import_source"];

            if (!in_array($column, $json_column)) {
                $data = [
                    'profile_id' => $profile['id'],
                    'column_name' => $column,
                    'changed_value' => $profile[$column],
                    'profit_type' => @$profile['type'],
                    'changed_by' => Auth::user()->id
                ];
                $history =    ProductProfileHistory::create($data);
            } else {
                foreach ($profile[$column] as $value) {
                    $data = [
                        'profile_id' => $profile['id'],
                        'column_name' => $column,
                        'changed_value' => $value,
                        'profit_type' => @$profile['type'],
                        'changed_by' => Auth::user()->id
                    ];
                    $history =    ProductProfileHistory::create($data);
                }
            }
        }
    }


    public function storeRules(): array
    {
        return [
            'item_code'  => ['required'],
            'prod_source'   => ['required'],
            'prod_sale_goal'   => ['required'],
            'prod_style'   => ['required'],
            'prod_section'   => ['required'],
            'prod_work_type'   => ['required'],
            'prod_import_source'   => ['required'],
            'prod_cost'   => ['required'],
            'prod_available_quantity'   => ['required'],
            'prod_max_adver_cost'   => ['required'],
            'prod_image'   => ['required'],
            'prod_new_product_source' => ['required'],
            'prod_production_type' => ['required'],
        ];
    }
}
