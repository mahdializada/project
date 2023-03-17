<?php


namespace App\Repositories;

use Exception;
use Carbon\Carbon;
use App\Models\File;
use App\Models\User;
use App\Models\Folder;
use App\Events\Updated;
use App\Models\Company;

use App\Models\Template;
use App\Models\SubSystem;
use App\Traits\FileTrait;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\DesignRequest;
use Illuminate\Http\Response;
use App\Events\SendNotification;
use Illuminate\Http\JsonResponse;
use App\Models\DesignRequestOrder;
use App\Models\StatusTimeConsumed;
use Illuminate\Support\Facades\DB;
use App\Models\DesignRequestReject;
use App\Models\DesignRequestHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\DesignRequestOrderUser;
use App\Models\DesignRequestRejectReason;
use Illuminate\Database\Eloquent\Builder;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use Intervention\Image\ImageManagerStatic as Image;

class DesignRequestRepository extends Repository
{

    use FileTrait;

    public function paginate(Request $request, $onlyAssigned): JsonResponse
    {
        $key = $request->query->get("key");

        $queryBuilder = new UriQueryBuilder(new DesignRequest(), $request);
        $queryBuilder->setRelations($this->getRelations());
        $queryBuilder->setWithTrashed();
        $queryBuilder->filterWithAuthCompany(false);


        $searchInColumns = ['product_code', 'whereHasOne,company,name', 'whereHasOne,template,name', 'product_name', 'type', 'priority', 'status', 'storyboard_reject_count', 'design_reject_count', 'waiting_end_date', 'completed_date', 'updated_at', 'created_at'];

        $queryBuilder->query = $this->filterRecords($queryBuilder->query, $request, $searchInColumns);
        $statusQuery = clone $queryBuilder->query;
        $customColumn = ['assigned, whereHasThree, designRequestOrder, designRequestOrderUser, user,', 'not assigned, whereDoesntHave, designRequestOrder.designRequestOrderUser'];

        $queryBuilder->query = $this->fetchDataAccordingToStatus($queryBuilder->query, $key, $customColumn);

        $queryBuilder->query->orderBy("created_at", 'desc');
        $designRequest = $queryBuilder->build()->paginate()->getData();
        $extraData = [
            'allTotal',
            'removedTotal',
            'waitingTotal, status,waiting',
            'instorybordTotal, status, in storyboard',
            'storybordreviewTotal, status, storyboard review',
            'storyboardRejectTotal, status, storyboard rejected',
            'indesignTotal, status, in design',
            'designReviewTotal, status, design review',
            'designRejectedTotal, status, design rejected',
            'inprogrammingTotal, status, in programming',
            'cancelledTotal, status, cancelled',
            'completedTotal, status, completed',
            'assignedTotal, whereHasThree, designRequestOrder, designRequestOrderUser, user',
            'notAssignedTotal, whereDoesntHave, designRequestOrder.designRequestOrderUser',
        ];
        $designRequest = $this->getStatusCount($statusQuery, $designRequest, $extraData);
        return response()->json($designRequest);
    }


    //return only product code and product name
    public function getProductCode()
    {

        $response = Http::get("https://api.teebalhoor.net/public/products");
        $teebAlhoorProducts = $response->json();
        $products = DB::table("design_requests")->select(['product_code as pcode', "product_name as name"])->groupBy("product_code")->get()->toArray();
        $products = array_merge($products, $teebAlhoorProducts ?? []);
        return response()->json($products);
    }

    public function getTemplate($type)
    {
        $templates = Template::select(["id", "name", "code"])->where("type", $type)->get();
        return response()->json($templates);
    }


    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $numberOfRecords = (int)$request->number_of_records;
            for ($i = 0; $i < $numberOfRecords; $i++) {
                $designRequest = new DesignRequest();
                $attributes = $request->only($designRequest->getFillable());
                $attributes["created_by"] = $request->user()->id;
                $attributes["updated_by"] = $request->user()->id;
                $attributes["code"] = $this->randomDesignCode();
                if (isset($request->type)) {
                    $template = $request->input("template");
                    if (is_array($template) && array_key_exists("id", $template)) {
                        $attributes["template_id"] = $template["id"];
                    } else if ($template) {
                        $template = Template::create(["name" => $template, "created_by" => $request->user()->id]);
                        $attributes["template_id"] = $template->id;
                    }
                }
                $designRequest = $designRequest->newQuery()->create($attributes);
                $this->storeDesignRequestOrder($designRequest, $request);
                broadcast(new Updated('design-request', $designRequest->id, 'created'));
            }
            DB::commit();
            return $this->successResponse($designRequest, Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            DB::rollback();
            return $this->errorResponse($th->getMessage());
        }
    }


    public function unAssign(Request $request)
    {
        try {
            DB::beginTransaction();
            $orderIds = $request->input("orderIds");
            DesignRequestOrderUser::whereIn("design_request_order_id", $orderIds)->delete();
            DB::commit();
            return $this->successResponse(null, Response::HTTP_OK);
        } catch (\Throwable $th) {
            DB::rollback();
            return $this->errorResponse($th->getMessage());
        }
    }


    public function search($request): JsonResponse
    {
        $queryBuilder = new UriQueryBuilder(new DesignRequest(), $request);

        $data = $this->searchCode($queryBuilder->query, $request, $this->getRelations());
        return $data ? response()->json($data, 200) : response()->json(['result' => false], Response::HTTP_NO_CONTENT);
    }


    public function assign(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'user_ids' => ['array', 'required'],
                'user_ids.*' => ['exists:users,id'],
                'design_request_ids.*' => ['exists:design_requests,id']
            ]);
            $data = $request->all();
            $arr = [];
            $notification = Notification::where('title', 'new_task')->first();
            $design_requests = DesignRequest::with($this->getRelations())->whereIn('id', $data['design_request_ids'])->get();
            $design_reqs_with_product_name_and_code = "";
            $design_reqs_order_ids = "";
            //1. assign design request to the selected users
            foreach ($design_requests as $req) {
                $design_req_order = DesignRequestOrder::whereDesignRequestId($req->id)->first();
                foreach ($data['user_ids'] as $user_id) {
                    $arr[$user_id] = [
                        'task_prograss' => (in_array($req->status, ['design review', 'storyboard review']) ? 100 : 0),
                        'created_by' => $request->user()->id,
                        'updated_by' => $request->user()->id
                    ];
                    //2. create a history of assigned user and design request,
                    $history_data = [
                        'user_id' => $user_id,
                        'design_request_id' => $req->id,
                        'status' => $req->status,
                        'prev_status' => $req->prev_status,
                    ];
                    $old_design_req_history = DesignRequestHistory::where($history_data)->exists();
                    if ($old_design_req_history) {
                        DesignRequestHistory::where($history_data)->update($history_data);
                    } else {
                        $new_design_req_history = $this->prepareDesignRequestHistory($req, $design_req_order, $user_id);
                        DesignRequestHistory::create($new_design_req_history);
                    }
                }
                $design_req_order->users()->syncWithoutDetaching($arr);
                $design_reqs_with_product_name_and_code = (empty($design_reqs_with_product_name_and_code) ?
                    $design_reqs_with_product_name_and_code : $design_reqs_with_product_name_and_code . ', ') . "$req->product_name ($req->product_code)";
                $design_reqs_order_ids = (empty($design_reqs_order_ids) ? $design_reqs_order_ids : $design_reqs_order_ids . ', ') . $req->code;
            }
            foreach ($data['user_ids'] as $user_id) {

                broadcast(new SendNotification(
                    $user_id,

                    $notification->id,
                    [],
                    [$request->user()->firstname . " " . $request->user()->lastname, $design_reqs_with_product_name_and_code],
                    [
                        'system' => 'My Order',
                        'order_id' => $design_reqs_order_ids
                    ]
                ));
            }
            broadcast(new Updated(
                'design-request',
                ["design_request_ids" =>
                $design_requests->pluck('code')],
                'multiple-assignment'
            ));
            DB::commit();
            return $this->successResponse(null, Response::HTTP_CREATED);
        } catch (Exception $exception) {
            DB::rollback();
            return $this->errorResponse($exception->getMessage());
        }
    }

    function prepareDesignRequestHistory($design_req, $design_req_order, $user_id)
    {
        $design_req_history = array_merge($design_req->toArray(), $design_req_order->toArray());
        unset($design_req_history["id"]);
        unset($design_req_history["design_request_order"]);
        unset($design_req_history["design_request_order_id"]);
        unset($design_req_history["design_request_order"]);
        unset($design_req_history["percentage"]);
        unset($design_req_history["file_url"]);
        unset($design_req_history["delted_at"]);
        unset($design_req_history["user"]);
        unset($design_req_history["status_times"]);
        unset($design_req_history["company"]);
        unset($design_req_history["template"]);
        //1. attach the items
        $design_req_history["created_at"] = Carbon::now();
        $design_req_history["updated_at"] = Carbon::now();
        $design_req_history['user_id'] = $user_id;
        $design_req_history["deleted_at"] = Carbon::now();
        $design_req_history["code"] = rand(100000, 9999999);
        return $design_req_history;
    }

    public function storeDesignRequestOrder(DesignRequest $designRequest, Request $request)
    {
        // in case of (if design request exists)
        if (DesignRequestOrder::where("design_request_id", $designRequest->id)->exists()) {
            DesignRequestOrder::where("design_request_id", $designRequest->id)->delete();
        }
        $designRequestOrder = new DesignRequestOrder();
        $attributes = $request->only($designRequestOrder->getFillable());
        $attributes["design_request_id"] = $designRequest->id;
        $attributes["created_by"] = $request->user()->id;
        $attributes["updated_by"] = $request->user()->id;
        DesignRequestOrder::create($attributes);
    }

    public function storeRules(): array
    {
        return [
            'product_code' => ['required'],
            'product_name' => ['required'],
            // 'template' => ['required'],
            "priority" => ["required", "string"],
            'company_id' => ['required', 'uuid', 'exists:companies,id'],
            "order_type" => ["required", "string"],
            "sales_note" => ["required"],
            // "storyboard_note" => ["nullable"],
            // "design_note" => ["nullable"],
            // "design_link" => ["nullable"],
            // "landing_page_link" => ["nullable"],
        ];
    }

    public function update(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $designRequest = DesignRequest::findOrFail($request->input("id"));
            $attributes = $request->only($designRequest->getFillable());
            $attributes['company_id'] = $designRequest->company_id;
            $attributes['product_code'] = $designRequest->product_code;
            $attributes['product_name'] = $designRequest->product_name;
            $attributes['created_by'] = $designRequest->created_by;
            $attributes['updated_by'] = $request->user()->id;
            $designRequest->update($attributes);

            if (DesignRequestOrder::where("design_request_id", $designRequest->id)->exists()) {
                $designRequestOrder = DesignRequestOrder::where('design_request_id', $designRequest->id)->first();
                $data = $request->input("design_request_order");
                if ($data["order_type"]) {
                    $data["updated_by"] = $request->user()->id;
                    $designRequestOrder->update($data);
                }
            } else {
                if (DesignRequestOrder::where("design_request_id", $designRequest->id)->exists()) {
                    DesignRequestOrder::where("design_request_id", $designRequest->id)->delete();
                }
                $designRequestOrder = $request->input("design_request_order");
                if ($designRequestOrder["order_type"]) {
                    $designRequestOrder["design_request_id"] = $designRequest->id;
                    $designRequestOrder["created_by"] = $request->user()->id;
                    $designRequestOrder["updated_by"] = $request->user()->id;
                    DesignRequestOrder::create($designRequestOrder);
                }
            }
            DB::commit();
            broadcast(new Updated('design-request', $designRequest->id, 'updated'));
            return $this->successResponse(null, Response::HTTP_CREATED);
        } catch (Exception $exception) {
            DB::rollback();
            return $this->errorResponse($exception->getMessage());
        }
    }

    // *HM*   Update Columns in desing request order *HM *
    public function updateColumn(Request $request)
    {
        $allowedColumns = ["sales_note", "storyboard_note", "design_note", "design_link", "landing_page_link", "file_url"];
        $filePath = null;
        try {
            $selectedColumn = $request->query->get("update-column");
            if (in_array($selectedColumn, $allowedColumns)) {
                $desingRequestOrderId = $request->input("id");
                if ($selectedColumn == "file_url") {
                    $designRequestOrder = DesignRequestOrder::find($desingRequestOrderId);
                    $prevFileUrl = $designRequestOrder->getRawOriginal("file_url");
                    // AB file ------- upload must be optimized
                    $file = $request->file("columnValue");
                    $company_id = $designRequestOrder->designRequest->company_id;
                    $sub_system_folder = Folder::where('name', 'Design Request')
                        ->where('company_id', $company_id)
                        ->where('parent_id', null)
                        ->first();
                    $parent = Folder::where('name', 'Attachments')
                        ->where('company_id', $company_id)
                        ->where('parent_id', $sub_system_folder->id)
                        ->first();

                    $this->prefix = 'file-management/' . $company_id . '/Design Request/Attachments';

                    $newName = $this->nameNumber($parent->id, $file->getClientOriginalName());
                    $imageType = $file->getMimeType();
                    $image = null;
                    $path_new = null;
                    if (in_array($imageType, $this->fileType)) {
                        $image = Image::make($file)->fit(350, 250, function ($constraint) {
                            $constraint->upsize();
                        });
                        $path_new = $this->storeImageAsFile($image, '350x250' . $newName);
                    }
                    if ($prevFileUrl) {
                        $this->deleteFile($prevFileUrl);
                        $file_delete = File::where('path', $prevFileUrl)
                            ->where('company_id', $company_id)
                            ->where('parent_id', $parent->id)->first();
                        if ($file_delete) {
                            $file_delete->forceDelete();
                            broadcast(
                                new Updated(
                                    'file_management',
                                    [
                                        "file_ids" => [$file_delete->id],
                                        "folder_ids" => [],
                                        "parent_id" => $parent->id
                                    ],
                                    'soft-deleted'
                                )
                            );
                        }
                    }
                    $filePath = $this->storeFileAs($file, $newName);

                    $newFile = File::create([
                        'name' => $newName,
                        'type' => $file->getClientOriginalExtension(),
                        'mime_type' => $file->getMimeType(),
                        'path' => $filePath,
                        "size" => $file->getSize(),
                        "thumbnail_path" => in_array($imageType, $this->iconTpye) ? null : $path_new,
                        'parent_id' => $parent->id,
                        'sub_system_id' => $sub_system_folder->sub_system_id,
                        'company_id' => $parent->company_id,
                        'created_by' => $request->user()->id
                    ]);
                    broadcast(
                        new Updated(
                            'file_management',
                            [
                                "id" => $newFile->id,
                                "parent_id" => $newFile->parent_id,
                                "type" => "file"
                            ],
                            'created'
                        )
                    );
                    //end AB file upload

                    $designRequestOrder->file_url = $filePath;
                    $designRequestOrder->save();
                    return $this->successResponse($designRequestOrder->file_url);
                }
                $columnValue = $request->input("columnValue");
                DesignRequestOrder::where("id", $desingRequestOrderId)->update([
                    $selectedColumn => $columnValue
                ]);
                return $this->successResponse(null);
            }
            return response()->json("your are not allowed to do this operation", 400);
        } catch (\Throwable $th) {
            if ($filePath) {
                $this->deleteFile($filePath);
            }
            return response()->json("your are not allowed to do this operation" . $th->getMessage(), 400);
        }
    }

    public function show(Request $request, $id)
    {
        if ($request->query->has('with_code')) {
            $design_request = DesignRequest::with($this->getRelations())
                ->where('code', reverseUniqueCode($id))->first();
            if ($design_request) {
                return response()->json($design_request, Response::HTTP_OK);
            } else {
                return response()->json(["message" => "not found"], Response::HTTP_NOT_FOUND);
            }
        } else {
            $design_request = DesignRequest::withTrashed()->with($this->getRelations())->find($id);
            return response()->json($design_request, Response::HTTP_OK);
        }
    }

    public function updateRules(Request $request)
    {
        $rules = [
            "id" => ['required', 'exists:design_requests,id'],
            'product_code' => ['required'],
            'product_name' => ['required'],
            'priority' => ['required'],
            'template' => ['required'],
            'company_id' => ['required', 'uuid', 'exists:companies,id'],
            "design_request_order.order_type" => ["string"],
            "design_request_order.sales_note" => ["nullable"],
            "design_request_order.storyboard_note" => ["nullable"],
            "design_request_order.design_note" => ["nullable"],
            "design_request_order.design_link" => ["nullable"],
            "design_request_order.landing_page_link" => ["nullable"],
        ];

        $request->validate($rules);
    }


    private function getRelations(): array
    {

        return [
            'designRequestOrder',
            'template',
            "designRequestOrder.designRequestOrderUser.user:id,firstname,lastname,image",
            "company:id,name",
            "user:id,firstname,lastname",
            'statusTimes',
            'label:id,label',
            "files" => function ($query) {
                $query->where("type", "file")->select([
                    "id", "design_request_id", "created_by", "name", "type", "size", "updated_at",
                ]);
            },
            'files.createdBy:id,firstname,lastname',
        ];
    }


    private function randomDesignCode(): int
    {
        return rand(10000, 8999999999);
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

    public function changeDesignRequestStatus(Request $request)
    {
        $this->statusValidations($request);
        try {
            DB::beginTransaction();

            $type = $request->input('type');
            $itemIds = $request->input('item_ids');
            $noReasonTabs = array_map('trim', explode(',', $request->input('no_reason_tabs')));
            $noReasonOperations = array_map('trim', explode(',', $request->input('no_reason_operations')));

            $boradcasetData = [];
            $notifiables = [];
            foreach ($itemIds as $id) {
                $item = DesignRequest::withTrashed()->findOrFail($id);
                $currentStatus = $item->status;
                $newStatus = $request->input('status');
                $prevStatus = $item->prev_status;
                if ($newStatus == 'in programming') {
                    if ($item->type != 'landing page design')
                        $newStatus = 'completed';
                }

                $currentItem = new \stdClass;
                $currentItem->item = $item;
                $currentItem->currentStatus = $currentStatus;
                $currentItem->newStatus = $newStatus;
                $temp = $currentItem;
                if ($temp->newStatus == 'restore') {
                    $temp->newStatus = $prevStatus;
                }
                $boradcasetData[] = $temp;

                $notify = new \stdClass;
                $notify->item = $item;


                $isPossible = $this->checkDesignPossibility($currentStatus, $newStatus);

                if ($isPossible) {
                    if ($currentStatus == 'removed') {    // Restore if current status is removed
                        $item->restore();
                    }
                    if ($newStatus != 'restore') {
                        $item->status = $newStatus;
                        $item->prev_status = $currentStatus;
                    } else {
                        $item->status = $prevStatus;
                        $item->prev_status = $currentStatus;
                    }
                    $item->save();


                    if (!in_array($currentStatus, $noReasonTabs) && !in_array($newStatus, $noReasonOperations)) {
                        if (gettype($request->input('reasons')) === 'array') {
                            $reject = DesignRequestReject::create([
                                'status' => $newStatus,
                                'design_request_id' => $item->id,
                                'changed_by' => Auth::id(),
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now(),
                            ]);
                            foreach ($request->input('reasons') as $reason) {
                                DesignRequestRejectReason::create([
                                    'reason_id' => $reason,
                                    'design_request_reject_id' => $reject->id
                                ]);
                            }
                        }
                    }

                    if ($currentStatus != 'waiting' && $currentStatus != 'removed') {
                        // Check for note
                        $order = DesignRequestOrder::where('design_request_id', $item->id)->first();
                        if (!in_array($newStatus, ['cancelled', 'removed'])) {
                            if (in_array($newStatus, ['storyboard review', 'storyboard rejected', 'in design'])) {
                                $storyNoteCount = $this->countStringLength($order->storyboard_note);
                                if ($storyNoteCount < 5) {
                                    DB::rollBack();
                                    return response()->json(["result" => false, "code" => 1203, "message" => "User storyboard note is empty."], Response::HTTP_BAD_REQUEST);
                                }
                            } else if (in_array($newStatus, ['design review', 'design rejected', 'in programming', 'completed'])) {
                                $designNoteCount = $this->countStringLength($order->design_note);
                                if ($designNoteCount < 10 || $order->designRequest->has_file) {
                                    DB::rollBack();
                                    $code = 1200;
                                    $message = '';
                                    if ($designNoteCount < 10 && $order->designRequest->has_file) {
                                        $code = 1201;
                                        $message = 'Design note and design files are empty';
                                    }
                                    if ($designNoteCount < 10) {
                                        $code = 1200;
                                        $message = 'Design note is empty.';
                                    } else if ($order->designRequest->has_file) {
                                        $code = 1202;
                                        $message = 'Design files are empty.';
                                    }
                                    return response()->json(["result" => false, "code" => $code, "message" => $message], Response::HTTP_BAD_REQUEST);
                                }
                            }
                        }


                        $designRequestOrder = DesignRequestOrder::where('design_request_id', $item->id)->first()->toArray();

                        $assignedUsers = DB::table('design_request_order_user')->where('design_request_order_id', $designRequestOrder["id"])->get()->toArray();
                        $designRequest = DesignRequest::where("id", $designRequestOrder["design_request_id"])->first()->toArray();
                        $notify->userIds = $assignedUsers;

                        if ($currentStatus === 'cancelled' && $newStatus == 'in storyboard') {
                            $item->status = $newStatus;
                            $item->prev_status = $currentStatus;
                        } else if (!in_array($newStatus, ['cancelled', 'removed']) && count($assignedUsers) === 0 && !in_array($currentStatus, ['storyboard rejected', 'design rejected'])) {
                            DB::rollBack();
                            return response()->json(["result" => false, "code" => 1400, "message" => "No user has been assigned."], Response::HTTP_BAD_REQUEST);
                        }

                        unset($designRequest["id"]);
                        $designRequestOrderCopy = $designRequestOrder;
                        unset($designRequestOrderCopy["id"]);

                        foreach ($assignedUsers as $designOrder) {
                            if ($designOrder->task_prograss != 100 && !in_array($newStatus, ['cancelled', 'removed'])) {
                                DB::rollBack();
                                return response()->json(["result" => false, "code" => 1600, "message" => "User task not done."], Response::HTTP_BAD_REQUEST);
                            }

                            // $designOrder = (array) $designOrder;
                            // unset($designOrder["id"]);
                            // unset($designOrder["design_request_order"]);
                            // unset($designOrder["design_request_order_id"]);
                            // $data  =  array_merge($designOrder, $designRequest, $designRequestOrderCopy);
                            // $data["created_at"] = Carbon::now();
                            // $data["updated_at"] = Carbon::now();
                            // $data["deleted_at"] = Carbon::now();
                            // unset($data["design_request_order"]);
                            // unset($data["percentage"]);
                            // unset($data["file_url"]);
                            // unset($data["delted_at"]);
                            // $data["status"] = $newStatus;
                            // $data["prev_status"] = $currentStatus;

                            // $isExits = DB::table('design_request_history')
                            //     ->where("design_request_id", $data["design_request_id"])
                            //     ->where("user_id", $data["user_id"])
                            //     ->where("prev_status", $data["prev_status"])
                            //     ->exists();
                            // if ($isExits) {
                            //     DB::table('design_request_history')
                            //         ->where("design_request_id", $data["design_request_id"])
                            //         ->where("user_id", $data["user_id"])
                            //         ->where("prev_status", $newStatus)
                            //         ->update($data);
                            // } else {
                            //     DB::table('design_request_history')->insert($data);
                            // }
                        }

                        DB::table('design_request_order_user')->where('design_request_order_id', $designRequestOrder["id"])->delete();


                        if (($newStatus == 'in storyboard' && $currentStatus == 'storyboard rejected') || ($newStatus == 'in design' && $currentStatus == 'design rejected')) {
                            $oldUsers = DB::table('design_request_history')->where(['prev_status' => $newStatus, 'design_request_id' => $item->id])->pluck('user_id');
                            foreach ($oldUsers as $user) {
                                $reject = DesignRequestOrderUser::create([
                                    'user_id' => $user,
                                    'design_request_order_id' => $designRequestOrder["id"],
                                    'created_by' => Auth::id(),
                                    'updated_by' => Auth::id(),
                                    'created_at' => Carbon::now(),
                                    'updated_at' => Carbon::now(),
                                ]);
                            }
                        }
                    }


                    $notify->type = 'sample';
                    if (($currentStatus == 'storyboard review' && $newStatus == 'in design') || $currentStatus == 'design review' && $newStatus == 'in programming')
                        $notify->type = 'approved';
                    if (($currentStatus == 'storyboard review' && $newStatus == 'storyboard rejected') || $currentStatus == 'design review' && $newStatus == 'design rejected')
                        $notify->type = 'rejected';

                    if (in_array($notify->type, ['rejected', 'approved']))
                        $notifiables[] = $notify;


                    if ($newStatus === 'completed') {
                        $item->completed_date = Carbon::now();
                        $item->save();
                        $prevStatusTime = StatusTimeConsumed::whereNull('end_date')->where('status', $currentStatus)->where('design_request_id', $item->id)->first();
                        if ($prevStatusTime) {
                            $prevStatusTime->end_date = Carbon::now();
                            $prevStatusTime->save();
                        }
                    } else {
                        $prevStatusTime = StatusTimeConsumed::whereNull('end_date')->where('status', $currentStatus)->where('design_request_id', $item->id)->first();
                        if ($prevStatusTime) {
                            $prevStatusTime->end_date = Carbon::now();
                            $prevStatusTime->save();
                        }
                        $storyboard_stage = str_contains($newStatus, 'storyboard') ? intVal($item->storyboard_reject_count) + 1 : 0;
                        $design_stage = str_contains($newStatus, 'design') ? intVal($item->design_reject_count) + 1 : 0;
                        $statusTime = StatusTimeConsumed::create([
                            'design_request_id' => $item->id,
                            'status' => $newStatus,
                            'storyboard_stage' => $storyboard_stage,
                            'design_stage' => $design_stage,
                            'created_at' => Carbon::now(),
                        ]);
                    }
                    if ($currentStatus == 'waiting') {
                        $item->waiting_end_date = Carbon::now();
                    }
                    if ($newStatus == 'storyboard rejected') {
                        $item->storyboard_reject_count += 1;
                    }
                    if ($newStatus == 'design rejected') {
                        $item->design_reject_count += 1;
                    }
                    $item->save();
                } else {
                    DB::rollBack();
                    return $this->errorResponse(["Invalid"], Response::HTTP_NOT_ACCEPTABLE);
                }
            }
            foreach ($boradcasetData as $broadcaset) {
                broadcast(new Updated('design-request', [
                    "item" => $broadcaset->item->id,
                    "current_status" => $broadcaset->currentStatus,
                    "new_status" => $broadcaset->newStatus,
                ], 'status-changed'));
            }

            $this->sendNotification($notifiables);
            DB::commit();
            return $this->successResponse(null, Response::HTTP_OK);
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->getMessage());
        }
    }

    private function sendNotification($notifiables)
    {
        $firstname = Auth::user()->firstname;
        $lastname = Auth::user()->lastname;

        foreach ($notifiables as $notify) {
            // return response()->json($notify->type);
            $item = $notify->item;
            $notification = Notification::where('title', $notify->type)->first();
            foreach ($notify->userIds as $user) {
                broadcast(new SendNotification(
                    $user->user_id,
                    $notification->id,
                    [],
                    [$firstname . " " . $lastname, "$item->product_name ($item->product_code)"],
                    []
                ));
            }
        }
    }

    private function countStringLength($string): int
    {
        if ($string) {
            $string = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $string);
            $string = preg_replace('/\s\s+/', ' ', $string);
            $string = strip_tags($string);
        }
        return strlen($string);
    }


    // Check whether status is changeable or not
    private function checkDesignPossibility($currentStatus, $newStatus)
    {
        $flag = false;

        switch ($currentStatus) {
            case 'waiting':
                if (in_array($newStatus, ['in storyboard', 'removed', 'cancelled'])) $flag = true;
                break;
            case 'in storyboard':
                if (in_array($newStatus, ['storyboard review', 'cancelled', 'removed'])) $flag = true;
                break;
            case 'storyboard review':
                if (in_array($newStatus, ['storyboard rejected', 'in design', 'cancelled', 'removed'])) $flag = true;
                break;
            case 'storyboard rejected':
                if (in_array($newStatus, ['in storyboard', 'cancelled', 'removed'])) $flag = true;
                break;
            case 'in design':
                if (in_array($newStatus, ['design review', 'cancelled', 'removed'])) $flag = true;
                break;
            case 'design review':
                if (in_array($newStatus, ['design rejected', 'in programming', 'cancelled', 'removed', 'completed'])) $flag = true;
                break;
            case 'design rejected':
                if (in_array($newStatus, ['in design', 'cancelled', 'removed'])) $flag = true;
                break;
            case 'in programming':
                if (in_array($newStatus, ['completed', 'cancelled', 'removed'])) $flag = true;
                break;
            case 'removed':
                // if (in_array($newStatus, ['restore']))        $flag = true;
                // break;
            case 'cancelled':
                if (in_array($newStatus, ['in storyboard'])) $flag = true;
                break;
            default:
                $flag = false;
        }
        return $flag;
    }

    public function notAssignSelectedUser(Request $request)
    {
        try {
            DB::beginTransaction();
            if (gettype($request->input('reasons')) === 'array') {
                $reject = DesignRequestReject::create([
                    'status' => 'not assigned',
                    'design_request_id' => $request->input('design_request_id'),
                    'changed_by' => Auth::id(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                foreach ($request->input('reasons') as $reason) {
                    DesignRequestRejectReason::create([
                        'reason_id' => $reason,
                        'design_request_reject_id' => $reject->id
                    ]);
                }
            }
            $data = DesignRequestOrderUser::where(['user_id' => $request->input('user_id'), 'design_request_order_id' => $request->input('design_request_order_id')])->delete();
            DB::commit();
            return response()->json(['data' => $data, 'result' => true], Response::HTTP_OK);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['data' => $th, 'result' => false], Response::HTTP_CONFLICT);
        }
    }

    public function destroy(array $ids, $request)
    {
        return $this->destroyItems(
            DesignRequest::class,
            $ids,
            'design_request_reason',
            null,
            'design_request_id',
            null,
            $this->getRelations(),
            'design-request',
            $request
        );
    }

    public function resonReject(Request $request)
    {
        $queryBuilder = new UriQueryBuilder(new DesignRequestReject(), $request);
        $queryBuilder->setRelations(['DesignRequestRejectReason.reasons']);
        $queryBuilder->query->where("design_request_id", $request->design_request_id);
        $queryBuilder->query->where("status", $request->status);
        $reasonReject = $queryBuilder->build()->get();
        return response()->json($reasonReject);
    }
}
