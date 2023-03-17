<?php

namespace App\Repositories\OnlineSalesManagement;

use App\Models\Advertisement\Connection;
use App\Models\Advertisement\ItemStatus;
use App\Models\Advertisement\Project;
use App\Models\CloudAttachment;
use App\Models\Company;
use App\Models\Country;
use App\Models\OnlineSalesManagement\OnlineSales;
use App\Models\OnlineSalesManagement\OnlineSalesNote;
use App\Models\OtpCode;
use App\Models\ProductManagement\ChangedRecord;
use App\Models\Reason;
use App\Models\Remark;
use App\Repositories\Files\CloudinaryFileUtils;
use App\Repositories\Repository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

//use Your Model

/**
 * Class OnlineSalesRepository.
 */
class OnlineSalesRepository extends Repository
{
    /**
     * @return string
     *  Return the model
     */

    public function paginate(Request $request)
    {


        $key                  = $request->query->get("key");
        if ($key == 'country' || $key == 'company' || $key == 'project')
            $key = $key . '_id';
        if ($key == 'item_code')
            $key = 'product_code';
        $query = new OnlineSales();

        $body =  json_decode($request->body);
        $start_date = Carbon::parse($body->start_date);
        $end_date = Carbon::parse($body->end_date);
        if ($start_date->gt($end_date)) {
            $range = [$end_date->toDateString(), $start_date->toDateString()];
        } else {
            $range =   [$start_date->toDateString(), $end_date->toDateString()];
        }
        $query = $query->whereBetween(DB::raw('DATE(created_at)'), $range);
        OnlineSales::setcustomPrimaryKey($key);
        $query = $query->with($this->getRelations($request->query->get("key")))->with('currentStatus');
        $query = $this->applySearch($query, $request);
        $query = $this->applyFilter($query, $request);

        $tabCount =  $this->getCount(clone $query);
        if ($key != 'product_code') {
            // $query = $query->select('country_id', 'status');
            $query = $this->getStatusColumn($query);
            $query = $query->orderBy("created_at", 'asc')->groupBy($key);
        }
        $body =  json_decode($request->body);

        $item_tabs = [];

        if (@$body->item_code_tab) {
            $result =  $this->filterAndCountByStatus($query, $body->item_code_tab);
            $item_tabs = $result['tabs'];
            $query = $result['query'];
        }
        $product         = $query->paginate($request->itemsPerPage);
        $data = $product->items();
        $total = $product->total();
        $result = [
            "data"      => $data,
            "total"     => $total,
            "tab_count" => $tabCount,
            "item_tabs" => $item_tabs
        ];
        return response()->json($result);
    }

    public function applySearch($query, $request)
    {
        $params =  json_decode($request->query->get("query"));

        if (@$params->code &&  @$params->code != '') {
            $tabs = ['country', 'company', 'project'];
            if (in_array($params->key, $tabs)) {
                $query = $query->whereHas($params->key, function ($q) use ($params) {
                    return $q->where('code', $params->code);
                });
            } else {
                $query =  $query->where('code', $params->code);
            }
        } else if (@$params->searchContent &&  @$params->searchContent != '') {
            $tabs = ['country', 'company', 'project'];
            if (in_array($params->key, $tabs)) {
                $query = $query->whereHas($params->key, function ($q) use ($params) {
                    return $q->where('code', 'LIKE', "%{$params->searchContent}%")->orWhere('name',  'LIKE', "%{$params->searchContent}%");
                });
            } else if ($params->key == 'sales_type') {
                $query = $query->where('code', 'LIKE', "%{$params->searchContent}%")->orWhere('sales_type',  'LIKE', "%{$params->searchContent}%");
            } else {
                $main_column = ['code', 'product_name', 'product_name_arabic', 'status', 'product_code', 'max_code',  'sales_type'];
                foreach ($main_column    as $key => $value) {
                    if ($key == 0) {
                        $query =  $query->where($value, 'LIKE', "%{$params->searchContent}%");
                    } else {
                        $query = $query->orWhere($value,  'LIKE', "%{$params->searchContent}%");
                    }
                }
                $child_column = ['state',  'prod_sales_stability', 'prod_source', 'prod_sale_goal', 'prod_style', 'prod_section', 'prod_new_product_source', 'prod_work_type', 'prod_import_source', 'prod_production_type', 'prod_cost', 'prod_available_quantity', 'prod_max_adver_cost', 'prod_profitability', 'prod_profit'];

                $query =  $query->orWhereHas('productInfo', function ($q) use ($child_column, $value, $params) {
                    foreach ($child_column    as $key => $value) {
                        if ($key == 0) {
                            $q = $q->where($value, 'LIKE', "%{$params->searchContent}%");
                        } else {
                            $q = $q->orWhere($value,  'LIKE', "%{$params->searchContent}%");
                        }
                    }
                    return $q;
                });
            }
        }

        return $query;
    }

    public function applyFilter($query, $request)
    {
        $params =  json_decode($request->body);

        foreach ($params as $key => $value) {
            $value = (array) $value;
            if (count($value) > 0) {
                switch ($key) {
                    case 'country':
                        $query = $query->whereIn('country_id', $value);
                        break;
                    case 'company':
                        $query = $query->whereIn('company_id', $value);
                        break;
                    case 'project':
                        $query = $query->whereIn('project_id', $value);
                        break;
                    case 'sales_type':
                        $query = $query->whereIn('sales_type', $value);
                        break;
                    case 'item_code':
                        $query = $query->whereIn('id', $value);
                        break;
                }
            }
            $online_column  = ['product_name', 'product_name_arabic',  'product_code', 'country_id', 'company_id', 'project_id', 'sales_type2'];
            if (in_array($key, $online_column)) {
                if ($key == 'sales_type2')
                    $key = 'sales_type';
                $query = $query->where($key, $value);
                $key = 'sales_type2';
            }
            $product_info = ['prod_sales_stability', 'prod_source', 'prod_sale_goal', 'prod_style', 'prod_section', 'prod_new_product_source', 'prod_work_type', 'prod_import_source', 'prod_production_type',  'prod_profitability'];
            if (in_array($key, $product_info)) {
                $query = $query->whereHas('ProductInfo', fn ($q) => $q->where($key, $value));
            }

            if ($key == 'ids') {
                $tab_key = $request->query->get("key");
                switch ($tab_key) {
                    case 'country':
                        $query = $query->whereHas('country', function ($q) use ($params, $value) {
                            $params->include == 1 ?  $q->whereIn('code', $value) : $q->whereNotIn('code', $value);
                        });
                        break;
                    case 'company':
                        $query = $query->whereHas('company', function ($q) use ($params, $value) {
                            $params->include == 1 ?  $q->whereIn('code', $value) : $q->whereNotIn('code', $value);
                        });
                        break;
                    case 'project':
                        $query = $query->whereHas('project', function ($q) use ($params, $value) {
                            $params->include == 1 ?  $q->whereIn('code', $value) : $q->whereNotIn('code', $value);
                        });
                        break;
                    default:
                        $query = $params->include == 1 ? $query->whereIn('code', $value) : $query->whereNotIn('code', $value);
                        break;
                }
            }
        }

        return $query;
    }
    public function filterAndCountByStatus($query, $current_tab)
    {
        $tabs = [
            'new_sales' => 0,
            "in_study" => 0,
            "content_creation" => 0,
            "store_creation" => 0,
            "in_source" => 0,
            "final_review" => 0,
            "ready_to_sale" => 0,
            "ready_to_sale" => 0,
            "remove" => 0,
            "cancel" => 0,
        ];

        foreach ($tabs as $key => $value) {
            $sub_query = clone $query;
            $tabs[$key] = $sub_query->whereStatus($key)->select('status')->get()->count();
        }
        $sub_query   =  clone $query;
        $tabs['all'] =  $sub_query->select('id')->get()->count();

        if ($current_tab != 'all') {
            $query = $query->whereStatus($current_tab);
        }

        return ['tabs' => $tabs, 'query' => $query];
    }

    public function getStatusColumn($query)
    {
        // $statuses = ['new_sales', 'in_source', 'in_study', 'store_creation', 'content_creation', 'final_review', 'ready_to_sale', 'cancel', 'remove'];

        $new_sales = DB::raw("sum(case when status = 'new_sales' then 1 else 0 end) as new_sales_count");
        $in_source = DB::raw("sum(case when status = 'in_source' then 1 else 0 end) as in_source_count");
        $in_study = DB::raw("sum(case when status = 'in_study' then 1 else 0 end) as in_study_count");
        $store_creation = DB::raw("sum(case when status = 'store_creation' then 1 else 0 end) as store_creation_count");
        $content_creation = DB::raw("sum(case when status = 'content_creation' then 1 else 0 end) as content_creation_count");
        $final_review = DB::raw("sum(case when status = 'final_review' then 1 else 0 end) as final_review_count");
        $ready_to_sale = DB::raw("sum(case when status = 'ready_to_sale' then 1 else 0 end) as ready_to_sale_count");
        $cancel = DB::raw("sum(case when status = 'cancel' then 1 else 0 end) as cancel_count");
        $remove = DB::raw("sum(case when status = 'remove' then 1 else 0 end) as remove_count");
        $totalProduct = DB::raw("count(id) as total_products");
        $query = $query->select(
            '*',
            $totalProduct,
            $new_sales,
            $in_source,
            $in_study,
            $store_creation,
            $content_creation,
            $final_review,
            $ready_to_sale,
            $cancel,
            $remove,
        );
        return $query;
    }

    public function getCount($query)
    {
        $count = [];
        $country = clone $query;
        $company = clone $query;
        $project = clone $query;
        $sales_type = clone $query;
        $item_code = clone $query;
        $count['country']    = $country->groupBy('country_id')->get()->count();
        $count['company']    = $company->groupBy('company_id')->get()->count();
        $count['project']    = $project->groupBy('project_id')->get()->count();
        $count['sales_type'] = $sales_type->groupBy('sales_type')->get()->count();
        $count['item_code']  = $item_code->get()->count();
        return $count;
    }
    public function Store(Request $request)
    {
        try {
            DB::beginTransaction();
            $res = [];
            foreach ($request->products as $value) {
                $attr['code'] = rand(100000, 999999999);
                $attr['product_name'] = $value['product_name'];
                $attr['product_name_arabic'] = $value['product_name_arabic'];
                $attr['product_code'] = $value['product_code'];
                $attr['country_id'] = $request->country_id;
                $attr['company_id'] = $request->company_id;
                $attr['project_id'] = $request->project_id;
                $attr['sales_type'] = $request->sales_type;
                if (!$value['isDefaultProductCode']) {
                    $online_store = OnlineSales::withTrashed();
                    $online_store->update(['max_code' => null]);
                    $attr['max_code'] =  $value['product_code'];
                } else {
                    $attr['max_code'] =  null;
                }
                OnlineSales::create($attr);
                ItemStatus::create([
                    'item_code' => $value['product_code'],
                    'country_id' => $request->country_id,
                    'item_status' => 'new_sales',
                    'color' => '#007f35 !important',
                    'created_by' => auth()->user()->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'end_date' => null,
                ]);
            }

            DB::commit();
            return response()->json($res, Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json($th->getMessage());
        }
    }
    public function update()
    {
    }
    public function storeRules(): array
    {
        return [
            'products.*.product_name'   => 'required',
            'products.*.product_name_arabic'   => 'required',
            'products.*.product_code'   => ['required', 'unique:online_sales,product_code'],
            'country_id' => 'required',
            'company_id' => 'required',
            'project_id' => 'required',
            'sales_type' => 'required',
        ];
    }

    public function updateRules(): array
    {
        return [];
    }
    private function getRelations($key): array
    {
        $relation = [];

        switch ($key) {
            case 'country':
                $relation = ['country' => function ($query) {
                    $query->select(['name', 'id', 'iso2', 'code'])->with('labels', function ($sub_query) {
                        $sub_query->whereCurrentStatus('active')->where('sub_system', 'online_sales');
                    });
                }];
                break;
            case 'company':

                $relation = ['company' => function ($query) {
                    $query->select(['name', 'id', 'logo', 'code'])->with('labels', function ($sub_query) {
                        $sub_query->whereCurrentStatus('active')->where('sub_system', 'online_sales');
                    });
                }];
                break;

            case 'project':
                $relation = ['project' => function ($query) {
                    $query->select(['name', 'id', 'code'])->with('labels', function ($sub_query) {
                        $sub_query->whereCurrentStatus('active')->where('sub_system', 'online_sales');
                    });
                }];
                break;
            case 'sales_type':
                $relation = ['SalesTypeLabels' => function ($query) {
                    $query->whereCurrentStatus('active')->whereType('sales_type')->where('sub_system', 'online_sales');
                }];
                break;
            case 'item_code':
                $relation = ['ProductInfo.attachments', 'company:id,logo,name', 'country:id,iso2', 'itemStatus', 'labels' => function ($query) {
                    $query->whereCurrentStatus('active')->whereType(null)->where('sub_system', 'advertisement');
                }];
                break;


            default:
                $relation = [];

                break;
        }
        return $relation;
    }

    public function fetchHistoryStatus(Request $request, $item_code)
    {
        $start_date = Carbon::parse($request->start_date);
        $end_date = Carbon::parse($request->end_date);
        if ($start_date->gt($end_date)) {
            $range = [$end_date->toDateString(), $start_date->toDateString()];
        } else {
            $range =   [$start_date->toDateString(), $end_date->toDateString()];
        }
        $result = ItemStatus::where('item_code', $item_code)->with('user:id,firstname,lastname,image')->whereBetween(DB::raw('DATE(created_at)'), $range)->orderBy('created_at', 'desc')->get();
        return response()->json($result, Response::HTTP_OK);
    }
    public function fetchNote(Request $request, $product_code)
    {
        $start_date = Carbon::parse($request->start_date);
        $end_date = Carbon::parse($request->end_date);
        if ($start_date->gt($end_date)) {
            $range = [$end_date->toDateString(), $start_date->toDateString()];
        } else {
            $range =   [$start_date->toDateString(), $end_date->toDateString()];
        }
        $result = OnlineSalesNote::with($this->getNoteRelations())->where('product_code', $product_code)
            ->whereBetween(DB::raw('DATE(created_at)'), $range)
            ->orderBy('created_at', 'desc')->get();
        return response()->json($result, Response::HTTP_OK);
    }
    public function getNoteRelations()
    {
        return [
            'attachments:attachmentable_id,name,path,file_type',
            'user:id,firstname,lastname,image'
        ];
    }
    public function noteRules()
    {
        return [
            'name'   => 'required',

            'product_code' => 'required',
        ];
    }
    public function storeNote(Request $request)
    {
        try {
            $model = OnlineSalesNote::create([
                'name' => $request->name,
                'note' => $request->note,
                'product_code' => $request->product_code,
                'created_by' => auth()->user()->id,
            ]);
            if ($request->hasFile('voice')) {
                $path = [];
                foreach ($request->file('voice') as $file) {
                    $result  =  CloudinaryFileUtils::uploadFile($file, 'online-sales');
                    $path[] =    $result->original['path'];
                }
                CloudinaryFileUtils::storeFiles(
                    $model,
                    $path
                );
            }
            if ($request->images) {
                CloudinaryFileUtils::storeFiles(
                    $model,
                    explode(',', $request->images)
                );
            }
            return response()->json($model->load($this->getNoteRelations()), Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage());
        }
    }
    public function updateNote(Request $request)
    {
        try {
            $model = OnlineSalesNote::findOrFail($request->id);
            $model->update([
                'name' => $request->name,
                'note' => $request->note,
                'created_by' => auth()->user()->id,
            ]);
            $path = [];
            if ($request->hasFile('voice')) {
                foreach ($request->file('voice') as $file) {
                    $result  =  CloudinaryFileUtils::uploadFile($file, 'online-sales');
                    $path[] =    $result->original['path'];
                }
            }
            if ($request->images) {
                foreach (explode(',', $request->images) as $value) {
                    $path[] = $value;
                }
            }
            if ($request->prevVoice)
                foreach ($request->prevVoice as $value) {
                    $path[] = $value;
                }
            CloudinaryFileUtils::updateFiles($model, $request->id, $path);
            return response()->json($model->load($this->getNoteRelations())->refresh(), Response::HTTP_OK);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
    public function deleteNote(Request $request, $id)
    {
        try {
            $delete = OnlineSalesNote::findOrFail($id)->delete();
            if ($delete && $request->path)
                CloudinaryFileUtils::multiDeleteByPath($request->path);
            return response()->json(true, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage());
        }
    }
    public function destroy()
    {
    }

    public function changeRecordStatus(Request $request)
    {
        try {
            $column = $request->column;
            $id = $request->id;
            $type =    get_class(new OnlineSales());
            $result =  ChangedRecord::where(["column_name" => $column, "changeable_type" => $type, 'changeable_id' => $id])->delete();
            if ($result == 0) {
                $data = ['user_id' => $request->user()->id, "column_name" => $column, "changes" => 'active', "changeable_type" => $type, 'changeable_id' => $id];
                $result =   ChangedRecord::create($data);
            }
            return response()->json($result, 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json($th->getMessage(), 500);
        }
    }

    public function deleteProduct($request)
    {
        try {
            //code...

            $otp_code =   OtpCode::where('code', $request->otp_code)->where('item_code', $request->pcode)->first();
            if (!$otp_code) {
                return response()->json('Invalid OTP Code', 500);
            }

            $date =  Carbon::now();
            $isExpired =   OtpCode::where('code', $request->otp_code)->where('expire_at', "<", $date)->first();
            if ($isExpired) {
                return response()->json('OTP Code Expired', 500);
            }

            $online_sales =  OnlineSales::find($request->record_id);
            $this->deleteCCPS($online_sales); // delete country, company, project and sales type when any of them is one record
            $connection = Connection::wherePcode($request->pcode)->first();
            if (!$connection) {
                $online_sales->allItemStatus()->delete();
                $this->deleteAttachment($online_sales, $request->pcode);
            }
            $online_sales->labels()->detach();
            $online_sales->remarks()->delete();
            $online_sales->setcustomPrimaryKey('product_code');
            $online_sales->currentStatus()->delete();
            $online_sales->reasonables('product_code')->detach();
            if ($online_sales->forceDelete()) {
                $date =  Carbon::now();
                OtpCode::where('code', $request->otp_code)->delete();
                OtpCode::where('expire_at', "<", $date)->delete();
            }
            return response()->json(true, 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json($th->getMessage(), 500);
        }
    }
    public function deleteAttachment($model, $pcode)
    {
        $note = $model->onlineSalesNote()->get('id')->toArray();
        $model->onlineSalesNote()->delete();
        try {
            $ids = [];
            foreach ($note as $n) {
                $ids[] = $n['id'];
            }
            $ids[] = $pcode;
            $allPath = CloudAttachment::whereIn('attachmentable_id', $ids)->get('path')->toArray();
            $path = [];
            foreach ($allPath as $d) {
                $path[] = $d['path'];
            }
            if ($path) {
                $deleteCloudFile = CloudinaryFileUtils::multiDeleteByPath($path);
                if ($deleteCloudFile)
                    $model->ProductInfo()->delete();
            }
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
    // delete country, company, project and sales type when any of them is one record
    public function deleteCCPS($model)
    {
        if (OnlineSales::whereCountryId($model->country_id)->count() == 1) {
            $country = Country::whereHas('onlineSales')->find($model->country_id);
            $country->labels()->detach();
            $country->remarks()->delete();
        }
        if (OnlineSales::whereCompanyId($model->company_id)->count() == 1) {
            $company = Company::whereHas('onlineSales')->find($model->company_id);
            $company->labels()->detach();
            $company->remarks()->delete();
        }
        if (OnlineSales::whereProjectId($model->project_id)->count() == 1) {
            $project = Project::whereHas('onlineSales')->find($model->project_id);
            $project->labels()->detach();
            $project->remarks()->delete();
        }
        if (OnlineSales::whereSalesType($model->sales_type)->count() == 1) {
            $salesType = OnlineSales::whereSalesType($model->sales_type)->first();
            $salesType->SalesTypeLabels()->detach();
            Remark::whereRemarkableType(get_class($salesType))->whereType('sales_type')->delete();
        }
    }
}
