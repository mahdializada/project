<?php

namespace App\Repositories;

use App\Models\Advertisement\AdAccount;
use App\Models\Advertisement\Application;
use App\Models\Advertisement\Connection;
use App\Models\Advertisement\HistoryAd;
use App\Models\Advertisement\HistoryAdset;
use App\Models\Advertisement\HistoryCampaign;
use App\Models\Advertisement\Labelable;
use App\Models\Advertisement\Platform;
use App\Models\Advertisement\Project;
use App\Models\Company;
use App\Models\ContentLibrary\ContentLibraryMedia;
use App\Models\Country;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use App\Models\Label;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Models\SubSystem;
use App\Models\System;
use App\Models\LabelCategory;
use App\Models\OnlineSalesManagement\OnlineSales;
use App\Models\User;
use Carbon\Carbon;
use Nette\Utils\Json;

class LabelRepository extends Repository
{
    public function paginate(Request $request): JsonResponse
    {
        $key = $request->query->get('key');
        return $this->getLabelsAccordingToStatus($request, $key);
    }

    private function getLabelsAccordingToStatus($request, $key): JsonResponse
    {
        $system      = System::where('name', 'LIKE', '%' . $request->query->get('system_name') . '%')->first();
        $queryBuilder = new UriQueryBuilder(new Label(), $request);
        $queryBuilder->setWithTrashed();

        if ($key == "removed") {
            $queryBuilder->query->onlyTrashed();
            $queryBuilder->query = $queryBuilder->query->with($this->getRelations())->whereHas('subSystems', function ($q) use ($system) {
                return $q->where('sub_systems.system_id', $system->id);
            });
            $queryBuilder->query = $this->filterRecords($queryBuilder->query, $request);
            $queryBuilder->query = $this->searchLabel($queryBuilder->query, $request);
            $labelData           = $queryBuilder->build()->paginate()->getData();
            $this->getExtraData($labelData, $system, $request);
            return response()->json($labelData);
        }
        if ($key != "all") {
            $queryBuilder->query->where("status", $key);
        }


        $queryBuilder->query = $queryBuilder->query->with($this->getRelations())->whereHas('subSystems', function ($q) use ($system) {
            return $q->where('sub_systems.system_id', $system->id);
        });
        $queryBuilder->query = $this->filterRecords($queryBuilder->query, $request);
        $queryBuilder->query = $this->searchLabel($queryBuilder->query, $request);
        $labelData           = $queryBuilder->build()->paginate()->getData();
        $this->getExtraData($labelData, $system, $request);

        return response()->json($labelData);
    }

    private function getExtraData($extaData, $system, $request)
    {


        //  archive total
        $queryBuilder = new UriQueryBuilder(new Label(), $request);
        $queryBuilder->query = $queryBuilder->query->whereHas('subSystems', function ($q) use ($system) {
            return $q->where('sub_systems.system_id', $system->id);
        })->where("status", "archive");
        $queryBuilder->query = $this->filterLabel($queryBuilder->query, $request);
        $queryBuilder->query = $this->searchLabel($queryBuilder->query, $request);
        $archiveTotal  = $queryBuilder->query;
        $extaData->archiveTotal = $archiveTotal->count();

        // live total
        $queryBuilder = new UriQueryBuilder(new Label(), $request);
        $queryBuilder->query = $queryBuilder->query->whereHas('subSystems', function ($q) use ($system) {
            return $q->where('sub_systems.system_id', $system->id);
        })->where("status", "live");
        $queryBuilder->query = $this->filterLabel($queryBuilder->query, $request);
        $queryBuilder->query = $this->searchLabel($queryBuilder->query, $request);
        $archiveLive  = $queryBuilder->query;
        $extaData->liveTotal = $archiveLive->count();

        // all data count

        $queryBuilder = new UriQueryBuilder(new Label(), $request);
        $queryBuilder->setWithTrashed();
        $queryBuilder->query = $queryBuilder->query->whereHas('subSystems', function ($q) use ($system) {
            return $q->where('sub_systems.system_id', $system->id);
        });
        $queryBuilder->query = $this->filterLabel($queryBuilder->query, $request);
        $queryBuilder->query = $this->searchLabel($queryBuilder->query, $request);
        $archiveLive  = $queryBuilder->query;
        $extaData->allTotal = $archiveLive->count();

        // deleted total

        $queryBuilder = new UriQueryBuilder(new Label(), $request);
        $queryBuilder->query = $queryBuilder->query->whereHas('subSystems', function ($q) use ($system) {
            return $q->where('sub_systems.system_id', $system->id);
        })->onlyTrashed();
        $queryBuilder->query = $this->filterLabel($queryBuilder->query, $request);
        $queryBuilder->query = $this->searchLabel($queryBuilder->query, $request);
        $archiveLive  = $queryBuilder->query;
        $extaData->removedTotal = $archiveLive->count();


        return $extaData;
    }

    private function getRelations(): array
    {
        return [
            'subSystems',
            'labelCategory'
        ];
    }


    public function show(Request $request)
    {
        try {
            $label = Label::all();
            return $this->successResponse($label, Response::HTTP_OK);
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            foreach ($request->labels as $labelData) {
                $category_id = $labelData['category']['id'];
                if ($category_id == 'new') {
                    $add_category = LabelCategory::create(['title' => $labelData['category']['title']]);
                    $category_id = $add_category->id;
                }
                $attributes['code'] = rand(100000, 999999999);
                $attributes['label_category_id'] = $category_id;
                $attributes['label'] = $labelData['label_name'];
                $attributes['status'] = $labelData['status'] ? 'archive' : 'live';
                $attributes['color'] = $labelData['color'];
                $attributes['system'] = $request->slug;
                $attributes['title'] = $labelData['title'];
                $attributes['tabs'] = json_encode($request->tab);
                $label            = Label::create($attributes);
                $label->subSystems()->attach($request->sub_systems);
                $label->load('subSystems');
            }

            DB::commit();
            return $this->successResponse($label->load($this->getRelations()), Response::HTTP_CREATED);
        } catch (Exception $exception) {
            DB::rollback();
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function storeRules(): array
    {
        return [
            'labels'         => 'required|array',
            'sub_systems'    => 'required|array',
        ];
    }

    public function update(Request $request): JsonResponse
    {
        // return response()->json($request);
        try {
            DB::beginTransaction();
            $label         = Label::findOrFail($request->id);
            $attributes      = $request->only($label->getFillable());
            $attributes['label'] = $request->labels[0]['label_name'];
            $attributes['status'] = $request->labels[0]['status'] ? 'archive' : 'live';
            $attributes['color'] = $request->labels[0]['color'];
            $attributes['title'] = $request->labels[0]['title'];
            $attributes['label_category_id'] = $request->labels[0]['category']['id'];
            $attributes['tabs'] = $request->tab ? json_encode($request->tab) : [];
            $label->update($attributes);
            $label->subSystems()->sync($request->sub_systems);
            $label->load('subSystems');
            $label->load('labelCategory');
            DB::commit();
            return $this->successResponse($label, Response::HTTP_OK);
        } catch (Exception $exception) {
            DB::rollback();
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function updateRules(): array
    {
        return  [
            'labels'         => 'required|array',
            'sub_systems'    => 'required|array',

        ];
        // $request->validate($rules);
    }


    public function destroy(array $ids, $request)
    {
        $reasonId = $request->query->get('reasons');
        $categories_to_delete = $request->categories_to_delete;
        if ($categories_to_delete != 'Delete label') {

            $label_categories        = label::findOrFail($ids[0]);
            $idss[0] = $label_categories->label_category_id;
            $result = LabelCategory::findOrFail($label_categories->label_category_id)->forceDelete();
            if ($result)
                return $this->successResponse($result, Response::HTTP_OK);
            // return $this->destroyItems(LabelCategory::class, $idss, 'ReasonLabelCategory', $reasonId, 'label_id', null, [], '');
        } else {

            return $this->destroyItems(Label::class, $ids, 'reason_label', $reasonId, 'label_id', null, [], '');
        }
    }

    public function changeLabelStatus(Request $request)
    {
        return $this->changeStatus($request, Label::class, 'labels', 'reason_label', 'label_id');
    }
    /**
     * filter label function
     *
     * @param [type] $query
     * @param [type] $request
     *
     */
    public function filterLabel($query, $request)
    {


        if ($request->subsystem) {
            $query = $query->whereHas('subSystems', function ($query) use ($request) {
                $query->whereIn('sub_system_id', $request->subsystem);
            });
        }

        if ($request->title) {
            $query = $query->where('title', 'like', '%' . $request->title . '%');
        }
        if ($request->label_ids) {
            if (count($request->label_ids) > 0) {
                if ($request->include != null) {
                    if ($request->include == 0) {
                        $query = $query->whereIn('code', $request->label_ids);
                    } else if ($request->include == 1) {
                        $query = $query->whereNotIn('code', $request->label_ids);
                    }
                }
            }
        }
        if ($request->created_date) {
            if (count($request->created_date) == 2) {
                $start_date = $request->created_date[0];
                $end_date = $request->created_date[1];
            } else {
                $start_date = $request->created_date[0];
                $end_date = null;
            }
            if ($start_date != null) {
                $start_date_format = Carbon::createFromFormat('Y-m-d', $start_date);
            }
            if ($end_date != null) {
                $end_date_format = Carbon::createFromFormat('Y-m-d', $end_date);
            }
            if ($start_date == null) {
                $year = $end_date_format->format('Y');
                $month = $end_date_format->format('m');
                $day = '01';
                $start_date = $year . '-' . $month . '-' . $day;
                $start_date_format = Carbon::createFromFormat('Y-m-d', $start_date);
            }
            if ($end_date) {
                if ($end_date_format->gt($start_date_format)) {
                    $query = $query->whereBetween(DB::raw('DATE(created_at)'), array($start_date_format, $end_date_format));
                } else if ($end_date_format->lessThan($start_date_format)) {
                    $query = $query->whereBetween(DB::raw('DATE(created_at)'), array($end_date_format->subDay(), $start_date_format));
                } else {
                    $query = $query->whereDate('created_at', $start_date_format);
                }
            } else {
                $query = $query->whereDate('created_at', $start_date_format);
            }
        }
        return $query;
    }
    /**
     * search labels by iD and content
     *
     * @param [type] $query
     * @param [type] $request
     *
     */
    public function searchLabel($query, $request)
    {


        if ($request->content) {

            $query = $query->where('code', $request->content);
        } else if ($request->contentData != null) {
            $query =  $query->where('title', 'like', '%' . $request->contentData . '%')
                ->orWhere('status', 'like', '%' . $request->contentData . '%')
                ->orWhere('label', 'like', '%' . $request->contentData . '%');
        }
        return $query;
    }

    public function getLabelAccordingToCategory($request)
    {
        $subsystem     = SubSystem::where('name', 'LIKE', '%' . $request->query->get('subsystem_name') . '%')->first();
    }

    public function assignLabelToItems(Request $request)
    {

        // assign label to any Items you want
        try {
            // code..
            $model = '';
            switch ($request->model_name) {
                case 'ad_set':
                    $model = HistoryAdset::select("adset_id")->where('adset_id', $request->id)->first();

                    break;
                case 'ad':
                    $model = HistoryAd::select("ad_id")->where('ad_id', $request->id)->first();

                    break;
                case 'campaign':
                    $model = HistoryCampaign::select("campaign_id")->where('campaign_id', $request->id)->first();
                    break;
                case 'company':
                    $model = Company::find($request->id);
                    break;
                case 'ad_account':
                    $model = AdAccount::find($request->id);
                    break;
                case 'organization':
                    $model = Application::find($request->id);
                    break;
                case 'platform':
                    $model = Platform::find($request->id);
                    break;

                case 'country':
                    $model = Country::find($request->id);
                    break;
                case 'item_code':
                    if ($request->sub_system == 'advertisement') {
                        $model = Connection::wherePcode($request->id)->first();
                    } else {
                        $model = OnlineSales::whereProductCode($request->id)->first();
                    }
                    break;
                case 'sales_type':
                    if ($request->sub_system == 'advertisement') {
                        $model = HistoryAd::whereSalesType($request->id)->first();
                    } else {
                        $model = OnlineSales::whereSalesType($request->id)->first();
                    }
                    break;
                case 'project':

                    $model = Project::find($request->id);
                    break;
                case 'content_library':
                    $model = ContentLibraryMedia::find($request->id);
                    break;

                default:
                    # code...
                    break;
            }

            # code...
            $deleted_label = DB::table('labelables')->where('labelable_id', $request->id)->where('labelable_type', get_class($model))
                ->whereNotIn('label_id', $request->selected_labels)->whereCurrentStatus('active')->where('sub_system', $request->sub_system);
            $deleted_ids = clone $deleted_label;
            $deleted_ids = $deleted_ids->get()->pluck('label_id')->toArray();
            $deleted_label->update(['current_status' => null]);
            $fillable_data1 = ['created_by' => $request->user()->id, "created_at" => Carbon::now(), 'status' => 'inactive', 'current_status' => null, 'sub_system' => $request->sub_system];
            $fillable_data2 = ['created_by' => $request->user()->id, "created_at" => Carbon::now(), 'sub_system' => $request->sub_system];
            if ($request->model_name == "sales_type") {
                $fillable_data1['type'] = 'sales_type';
                $fillable_data2['type'] = 'sales_type';
                if ($request->sub_system != 'advertisement')
                    $model->SalesTypeLabels()->attach($deleted_ids, $fillable_data1);
                else $model->labels()->attach($deleted_ids, $fillable_data1);
            } else {
                $model->labels()->attach($deleted_ids, $fillable_data1);
            }
            $existIds =  DB::table('labelables')->where('labelable_id', $request->id)->where('labelable_type', get_class($model))->whereIn('label_id', $request->selected_labels)->whereCurrentStatus("active")->where('sub_system', $request->sub_system)->get()->pluck('label_id')->toArray();
            $new_array = array_diff($request->selected_labels, $existIds);

            if ($request->model_name == "sales_type" && $request->sub_system != 'advertisement') {
                $model->SalesTypeLabels()->attach($new_array, $fillable_data2);

                $model =   $model->load(['SalesTypeLabels' => function ($q) use ($request) {

                    return $q->whereCurrentStatus('active');
                }])->toArray();
                $model['labels'] = $model['sales_type_labels'];
                unset(
                    $model['sales_type_labels']
                );

                return response()->json($model);
            } else {
                $model->labels()->attach($new_array, $fillable_data2);

                $model->load(['labels' => function ($q) use ($request) {

                    return $q->whereCurrentStatus('active');
                }]);
                return response()->json($model);
            }
        } catch (\Throwable $th) {

            return response()->json(["error" => $th->getMessage()], 500);
        }
    }



    public function getLabelsHistory(Request $request)
    {
        # code...
        // $labels = Labelable::all()->groupBy(function ($date) {
        //     return \Carbon\Carbon::parse($date->created_at)->format('d-M-y');
        // });
        $query =  Labelable::where('labelable_id', $request->id)->with('label', 'creator:id,firstname,lastname,image');
        if ($request->start_date && $request->end_date)
            $query = $query->whereBetween(DB::raw('DATE(created_at)'), array($request->start_date, $request->end_date));
        else
            $query = $query->whereDate('created_at', date("Y-m-d"));
        $labels = $query->orderBy('created_at', 'desc')->get()->groupBy(function ($data) {
            return \Carbon\Carbon::parse($data->created_at)->format('d-M-y') . ',' . $data->created_by . ',' . $data->status;
        })->toArray();
        return response()->json($labels);
    }
}
