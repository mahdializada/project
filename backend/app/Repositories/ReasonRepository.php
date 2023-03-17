<?php

namespace App\Repositories;

use App\Models\Advertisement\AdAccount;
use App\Models\Advertisement\Application;
use App\Models\Advertisement\Connection;
use App\Models\Advertisement\HistoryAd;
use App\Models\Advertisement\HistoryAdset;
use App\Models\Advertisement\HistoryCampaign;
use App\Models\Advertisement\Platform;
use App\Models\Advertisement\Project;
use App\Models\Advertisement\Reasonable;
use App\Models\Company;
use App\Models\Country;
use App\Models\OnlineSalesManagement\OnlineSales;
use App\Models\Reason;
use App\Models\ReasonSubSystem;
use App\Models\ReasonType;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Models\SubSystem;
use App\Models\System;
use App\Repositories\OnlineSalesManagement\OnlineSalesRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ReasonRepository extends Repository
{
    public function paginate(Request $request): JsonResponse
    {
        $system  = System::where('name', 'LIKE', '%' . $request->slug . '%')->first();
        // return $system->id;
        $queryBuilder = new UriQueryBuilder(new Reason(), $request);
        $queryBuilder->setRelations($this->getRelations());
        $queryBuilder->query->where("system_id", $system->id);
        // return reason that has no event
        if ($request->no_system) {
            $queryBuilder->query->WhereDoesntHave("subSystems");
        }
        $searchInColumns    = ['whereHasOne,system,name', 'title', 'created_at'];
        $queryBuilder->query = $this->filterRecords($queryBuilder->query, $request, $searchInColumns, 'id');
        $reasonData   = $queryBuilder->build()->paginate()->getData();
        return response()->json($reasonData);
    }

    public function fetchReasonAccordingToSystemEvent(Request $request): JsonResponse
    {

        $system       = System::where('name', 'LIKE', '%' . $request->slug . '%')->first();
        // return $system->id;
        $queryBuilder = new UriQueryBuilder(new Reason(), $request);
        $queryBuilder->setRelations($this->getRelations());
        $queryBuilder->query->where("system_id", $system->id);
        // return reason that has no event
        if ($request->no_system) {
            $queryBuilder->query->WhereDoesntHave("subSystems");
        }
        $reasonData   = $queryBuilder->build()->paginate()->getData();
        return response()->json($reasonData);
    }


    private function getRelations(): array
    {
        return [
            'system',
        ];
    }

    public function show(Request $request)
    {

        try {
            // $filleredArray = [];
            $sub_system = SubSystem::where('name', $request->sub_system)->first();
            if (!$sub_system) {
                return response()->json(["result" => false, 'reasons' => []], Response::HTTP_NOT_FOUND);
            }

            $reasons = ReasonSubSystem::join('reasons', 'reason_sub_system.reason_id', '=', 'reasons.id')
                // ->join('reason_types', 'reason_sub_system.id', '=', 'reason_types.reason_sub_system_id')
                ->where(['sub_system_id' =>  $sub_system->id, 'reason_sub_system.type' => $request->type])->get();

            // if ($request->tab_name) {
            //     foreach ($reasons as $reason) {
            //         $reason->tabs = json_decode($reason->tabs);
            //         if (collect($reason->tabs)->contains($request->tab_name)) {
            //             array_push($filleredArray, $reason);
            //         }
            //     }

            //     return response()->json(['reasons' => $filleredArray], Response::HTTP_OK);
            // } else {
            return response()->json(['reasons' => $reasons], Response::HTTP_OK);
            // }
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $system            = System::where('name', 'LIKE', '%' . $request->input('slug') . '%')->first();
            // $code   = rand(100000, 9999999999);
            foreach ($request->titles as $newTitle) {
                Reason::create(
                    [
                        "title" => $newTitle,
                        "system_id" => $system->id,
                        "code" => rand(100000, 9999999999),
                        // "tabs" =>  json_encode($request->selectedTabSystems)
                    ]
                );
            }
            return response()->json(Response::HTTP_CREATED);
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function storeRules(): array
    {
        return [
            'titles'         => 'required|array',
            'titles.*'         => 'min:2|unique:reasons,title',
        ];
    }

    public function update(Request $request): JsonResponse
    {
        try {
            $reason           = Reason::findOrFail($request->id);
            $reason->title    = $request->title;
            $reason->save();
            return response()->json(["result" => true], Response::HTTP_CREATED);
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function updateRules(Request $request)
    {
        $rules = [
            'title'         => 'required|min:2|unique:reasons,title,' . $request->id,
        ];
        $request->validate($rules);
    }

    /**
     * search reason by id
     *
     * @param [type] $queryBuilder
     * @param [type] $request
     *
     */
    public function search($request)
    {


        $system  = System::where('name', 'LIKE', '%' . $request->slug . '%')->first();

        $queryBuilder = new UriQueryBuilder(new Reason(), $request);
        $queryBuilder->setRelations($this->getRelations());
        $queryBuilder->query->where("system_id", $system->id);
        $queryBuilder->query->where('code', $request->code)->first();
        $data   = $queryBuilder->build()->paginate()->getData();
        return $data ? response()->json($data, 200) : response()->json(['result' => false], Response::HTTP_NO_CONTENT);
    }

    public function assignReason(Request $request)
    {

        try {
            if ($request->multipleItems) {
                return  $this->multipeAssignReason($request);
            }
            DB::beginTransaction();
            $model = '';
            $model = $this->getModel($request, $request->id);
            $uuid = Str::uuid();

            if ($request->model == 'online_sales') {
                $online_sales = new OnlineSalesRepository();
                $response =  $online_sales->changeRecordStatus($request);
                $status = $response->getStatusCode();
                if ($status == 500) {
                    DB::rollBack();
                    return response()->json(['error' => json_decode($response->getContent(), true)], 500);
                }
                $model = $model->reasonables($request->primary_key);
                $model->attach($request->reason_ids, ['created_by' => Auth::user()->id, "status" => $request->status, 'uuid' => $uuid, 'tab' => @$request->tab ?? 'all', 'created_at' => Carbon::now()]);
            } else {
                $model->advertisement_status = $request->status;
                $model->save();
                $model = $request->model == "sales_type" ? $model->salesTypeReasonable() : $model->reasonables();
                $model->attach($request->reason_ids, ['created_by' => Auth::user()->id, "status" => $request->status, 'uuid' => $uuid, 'tab' => @$request->tab ?? 'all', 'created_at' => Carbon::now()]);
            }
            DB::commit();
            return response()->json(['result' => true], Response::HTTP_OK);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }


    public function reasonHistory(Request $request)
    {

        switch ($request->model) {
            case 'company':
                $model = 'App\Models\Company';
                break;
            case 'organization':
                $model = 'App\Models\Advertisement\Application';
                break;
            case 'platform':
                $model = 'App\Models\Advertisement\Platform';
                break;
            case 'country':
                $model = 'App\Models\Country';
                break;
            case 'project':
                $model = 'App\Models\Advertisement\Project';
                break;
            case 'ad_set':
                $model = 'App\Models\Advertisement\HistoryAdset';
                break;
            case 'ad':
                $model = 'App\Models\Advertisement\HistoryAd';
                break;
            case 'campaign':
                $model = 'App\Models\Advertisement\HistoryCampaign';
                break;
            case 'ad_account':
                $model = 'App\Models\Advertisement\AdAccount';
                break;
            case 'item_code':
                $model = 'App\Models\Advertisement\Connection';
                break;
            case 'sales_type':
                $model = 'App\Models\Advertisement\Connection';
                break;


            default:
                $model = 'App\Models\Country';
                break;
        }


        $query = Reasonable::where('reasonable_id', $request->id)->where('reasonable_type', $model)
            ->with([
                'reasons',
                'reasonable', 'creator:id,firstname,lastname,image'
            ]);
        if ($request->start_date && $request->end_date)
            $query = $query->whereBetween(DB::raw('DATE(created_at)'), array($request->start_date, $request->end_date));
        else
            $query = $query->whereDate('created_at', date("Y-m-d"));

        $data = $query
            ->orderBy('created_at', 'desc')
            ->get();

        return $data;
        // return response()->json(['data' => $data, 'groupedData' => $grouped], Response::HTTP_OK);
    }

    public function multipeAssignReason($request)
    {
        try {
            DB::beginTransaction();
            foreach ($request->multipleItems as $key => $value) {
                $model = '';
                $model = $this->getModel($request, $value['id']);
                $uuid = Str::uuid();
                $model->advertisement_status = $request->status;
                $model->save();
                $model = $request->model == "sales_type" ? $model->salesTypeReasonable() : $model->reasonables();
                $model->attach($value['reasons'], ['created_by' => Auth::user()->id, "status" => $request->status, 'uuid' => $uuid, 'tab' => $request->model, 'created_at' => Carbon::now()]);
            }
            DB::commit();
            return response()->json(['result' => true], Response::HTTP_OK);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()], 400);
        }
    }
    public function getModel($request, $id)
    {
        $model = $request->model;
        $status = $request->status;
        switch ($model) {
            case 'ad_set':
                $model = HistoryAdset::select("adset_id")->where('adset_id', $id)->first();
                break;
            case 'ad':
                $model = HistoryAd::select("ad_id")->where('ad_id', $id)->first();
                break;
            case 'campaign':
                $model = HistoryCampaign::select("campaign_id")->where('campaign_id', $id)->first();
                break;
            case 'company':
                $model = Company::find($id);
                break;
            case 'ad_account':
                $model = AdAccount::find($id);
                break;
            case 'organization':
                $model = Application::find($id);
                break;
            case 'platform':
                $model = Platform::find($id);
                break;
            case 'company':
                $model = Company::find($id);
                break;
            case 'country':
                $model = Country::find($id);
                break;
            case 'project':
                $model = Project::find($id);
                break;
            case 'item_code':
                $model = Connection::find($id);
                Connection::where('pcode',  $model->pcode)
                    ->update(['advertisement_status' => $status]);
                break;
            case 'sales_type':
                $model = Connection::find($id);
                Connection::where('sales_type_status',  $model->sales_type_status)
                    ->update(['advertisement_status' => $status]);
                break;
            case 'online_sales':
                $model = OnlineSales::where($request->primary_key, $id)->first();
                break;

            default:
                break;
        }
        return $model;
    }
}
