<?php

namespace App\Repositories\SingleSales;

use App\Models\Reason;
use App\Models\SingleSales\ActionClass;
use App\Repositories\Repository;
use Illuminate\Http\Request;
use App\Models\SingleSales\Action;
use App\Models\SingleSales\Ipa;
use App\Models\SingleSales\Ispp;
use App\Models\TempFile;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use Exception;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ActionRepository extends Repository
{
    private $sspAction;

    public function paginate(Request $request)
    {
        $key = $request->query->get("key");
        $queryBuilder = new UriQueryBuilder(new Action(), $request);
        $queryBuilder->setRelations($this->getRelations());
        $searchInColumns = [
            'company_id',
            'actionable_type',
            'actionable_id',
            'work_priority',
            'status',
            'action_note',
        ];

        $queryBuilder->query    = $this->filterRecords($queryBuilder->query, $request, $searchInColumns);
        $statusQuery            = clone  $queryBuilder->query;
        $queryBuilder->query    = $this->fetchDataAccordingToStatus($queryBuilder->query, $key);

        $queryBuilder->query->orderBy("created_at", 'desc');
        $action         = $queryBuilder->build()->paginate()->getData();
        $extraData = [
            'allTotal',
            'removedTotal',
            'pendingTotal,status,pending',
            'inProgressTotal,status,in progress',
            'doneTotal,status,done',
            'archivedTotal,status,archived',
            'failedTotal,status,failed',
            'notMatchingTotal,status,not matching',
            'cancelledTotal,status,cancelled',
            'inHoldTotal,status,in hold',
        ];
        $action = $this->getStatusCount($statusQuery, $action, $extraData);
        return response()->json($action);
    }
    public function store(Request $request)
    {
        $dependableModel = Ipa::class;
        if ($request->get('type')=='ispp'){
            $dependableModel =  Ispp::class;
        }
        try {
            $actionModel = new Action();
            DB::beginTransaction();
            $attributes         = $request->only($actionModel->getFillable());
            $attributes['code'] = rand(100000, 9999999999);
            $attributes['created_by'] = Auth::user()->id;
            $attributes['goals'] = json_encode($request->goals);
            $attributes['dependable_type'] = $dependableModel;
            $action =  $actionModel->create($attributes);
            $action->refresh();
            $data = [];
            $data[] = ["value" => $request->value, "statement" => json_encode($request->statements)];
            // $actionClass = new ActionClass();
            // $actionClass->saveUploadedFile($action->classes()->createMany($data), $request->attachment);
            // $tempFile = new TempFile();
            // $tempFile->deleteRec($request->attachment);
            DB::commit();
            return response()->json(['result' => true, 'action' => $action->load($this->getRelations())->refresh()], Response::HTTP_CREATED);
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->getMessage());
        }
    }


    public function update(Request $request, $id)
    {

        $dependableModel = Ipa::class;
        if ($request->get('type')=='ispp'){
            $dependableModel =  Ispp::class;
        }
        $actionModel = Action::findOrFail($id);
        $attributes = $request->only($actionModel->getFillable());
        $attributes['dependable_type'] = $dependableModel;

        try {

            DB::beginTransaction();
            $actionModel->update($attributes);
            $actionModel->classes()->update(["value" => $request->value, "statement" => json_encode($request->statements)]);
            $actionClass = new ActionClass();
            $actionClass->saveUploadedFile($actionModel->classes()->get(), $request->attachment);
            DB::commit();
            return response()->json(['result' => true, 'actions' => $actionModel->load($this->getRelations())->refresh()], Response::HTTP_CREATED);
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->getMessage());
        }
    }
    public function getRelations()
    {
        return ['companies'];
    }
    public function destroy($ids, $reason_ids)
    {

        try {
            //code...
            DB::beginTransaction();
            $actions = Action::withTrashed()->whereIn('id', $ids)->get();
            $reasons = Reason::whereIn('id', $reason_ids)->get();
            // ADD REASON
            foreach ($actions as $action) {
                if (!$action->trashed()) {
                    foreach ($reasons as $reason) {
                        $action->reasons()->save($reason);
                    }
                    $action->reasons()->syncWithoutDetaching($reasons);
                    $action->delete();
                } else {
                    $action->reasons()->detach();
                    $action->forceDelete();
                }
            }

            DB::commit();
            return response()->json(['result' => true], Response::HTTP_OK);
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return $th;
        }
    }


    public function storeRules()
    {
        # code...
        return [];
        return [

            'goals' => 'required',
            'section' => 'required',
            'actions' => 'nullable',
            'priority' => 'nullable',
            'action_classes' => 'nullable|array',
            'action_classes_val_info' => 'nullable|array',
            'created_by' => 'nullable',
            'done_by' => 'nullable',
            'done_date' => 'nullable',
            'status' => 'nullable',

        ];
    }
    public function updateRules()
    {
        # code...
        return [];
        return [

            'goals' => 'required',
            'section' => 'required',
            'actions' => 'nullable',
            'priority' => 'nullable',
            'action_classes' => 'nullable|array',
            'action_classes_val_info' => 'nullable|array',
            'created_by' => 'nullable',
            'done_by' => 'nullable',
            'done_date' => 'nullable',
            'status' => 'nullable',

        ];
    }
    public function search(Request $request)
    {
        $queryBuilder = new UriQueryBuilder(new Action(), $request);
        $data = $this->searchCode($queryBuilder->query, $request, [], false);
        return $data ? response()->json($data->load($this->getRelations()), 200) : response()->json(['result' => false], Response::HTTP_NO_CONTENT);
    }


    public function changeActionStatus(Request $request)
    {
        $this->statusValidations($request);
        try {
            DB::beginTransaction();
            $itemIds = $request->input('item_ids');
            $noReasonTabs = array_map('trim', explode(',', $request->input('no_reason_tabs')));
            $noReasonOperations = array_map('trim', explode(',', $request->input('no_reason_operations')));
            $newStatus = $request->input('status');
            $reasons = $request->input('reasons');
            $boradcasetData = [];
            foreach ($itemIds as $id) {
                $item = Action::withTrashed()->findOrFail($id);
                if ($newStatus == 'done') {
                    $item['done_by'] = Auth::user()->id;
                    $item['done_date'] = Carbon::now();
                }
                if ($item->status == 'removed' || $newStatus == 'restore') {    // Restore if current status is removed
                    $item->restore();
                } else if ($newStatus != 'restore') {
                    $item->status = $newStatus;
                }
                if (!in_array($item->status, $noReasonTabs) && !in_array($newStatus, $noReasonOperations) && gettype($reasons) === 'array') {
                    $item->reasons()->syncWithoutDetaching($reasons);
                }
                $item->save();
            }
            DB::commit();
            return $this->successResponse(null, Response::HTTP_OK);
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->getMessage());
        }
    }
    private function statusValidations($request)
    {
        $request->validate([
            'type' => ['required', 'string'],
            'status' => ['string'],
            'item_ids' => ['required'],
            'reasons' => ['required_if:type,hasReason', 'array'],
        ]);
    }
}
