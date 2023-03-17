<?php

namespace App\Http\Controllers;

use ActivityLog;
use PhpParser\JsonDecoder;
use Illuminate\Http\Request;
use App\Models\DesignRequest;

use Illuminate\Http\Response;
use function PHPSTORM_META\type;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

use App\Models\DesignRequestOrderUser;
use App\Repositories\DesignRequestRepository;

class DesignRequestController extends Controller
{
    private $repository;
    private $ActivityLog;

    public function __construct()
    {
        $this->repository = new DesignRequestRepository();
        $this->middleware('scope:drv')->only(['index', 'show']);
        $this->middleware('scope:drc')->only(['store']);
        $this->middleware('scope:dru')->only(['update']);
        $this->middleware('scope:drd')->only(['destroy']);
        $this->middleware('scope:dra')->only(['assignUsers']);


        $this->ActivityLog = new ActivityLog();
    }
    public function index(Request $request)
    {
        $onlyAssigned = true;
        if ($this->tokenCan('drva')) {
            $onlyAssigned = false;
        }

        return $this->repository->paginate($request, $onlyAssigned);
    }


    public function store(Request $request)
    {
        $request->validate($this->repository->storeRules());
        //set log
        $this->ActivityLog->setLog($request, 'landing_page', 'design_request', 'insert');
        return $this->repository->store($request);
    }

    public function assignUsers(Request $request)
    {
        $this->ActivityLog->setLog($request, 'landing_page', 'design_request', 'assign_users');

        if ($request->query->get("type") === "unassign") {
            return  $this->repository->unAssign($request);
        }
        return $this->repository->assign($request);
    }

    // Update the specified resource in storage.

    public function update(Request $request): JsonResponse
    {
        $this->repository->updateRules($request);
        $this->ActivityLog->setLog($request, 'landing_page', 'design_request', 'update');

        return $this->repository->update($request);
    }


    // update specific column
    public function updateColumn(Request $request)
    {
        $id = $request->input("id");
        $userOrder = DesignRequestOrderUser::where("user_id", $request->user()->id)
            ->where("design_request_order_id", $id)->first();
        if ($userOrder && $userOrder->user_can_edit) {
            return $this->repository->updateColumn($request);
        } else if ($this->tokenCan("drva")) {
            return $this->repository->updateColumn($request);
        }
        return response()->json(null, Response::HTTP_UNAUTHORIZED);
    }

    public function show(Request $request, $id)
    {
        return $this->repository->show($request, $id);
    }

    public function destroy(Request $request, $ids)
    {

        $this->ActivityLog->setLog($request, 'landing_page', 'design_request', 'delete');
        $ids        = explode(",", $ids);
        $reasons    = explode(',', $request->query->get('reasons'));
        return $this->repository->destroy($ids, $reasons);
    }

    public function changeStatus(Request $request)
    {

        $result = $this->repository->changeDesignRequestStatus($request);
        if (!in_array($result->status(), [400, 401, 404, 406])) {
            $this->ActivityLog->setLog($request, 'landing_page', 'design_request', 'change status');
        }
        return $result;
    }

    public function notAssignUser(Request $request): JsonResponse
    {
        return $this->repository->notAssignSelectedUser($request);
    }

    public function resonReject(Request $request)
    {
        return $this->repository->resonReject($request);
    }
    public function autoReview(Request $request)
    {
        $requests = $request->all();
        $flag = false;
        DB::beginTransaction();
        $result = '';
        $response = '';
        foreach ($requests as  $value) {
            # code...
            $req =  new \Illuminate\Http\Request($value);
            $result =   $this->repository->changeDesignRequestStatus($req);
            if (in_array($result->status(), [400, 401, 404, 406])) {
                $flag = true;
                $response = $result;
            }
        }
        if ($flag) {
            # code...
            DB::rollBack();
            return  $response;
        } else {
            foreach ($requests as  $value) {
                $req =  new \Illuminate\Http\Request($value);
                $req['latitude'] =  $request->header('latitude');
                $req['longitude'] =  $request->header('longitude');
                $this->ActivityLog->setLog($req, 'landing_page', 'design_request_auto_review', 'change status');
            }

            DB::commit();
            return $result;
        }
    }

    public function searchDesignRequest(Request $request): JsonResponse
    {
        return $this->repository->search($request);
    }

    public function updateWorkArea(Request $request, $id): JsonResponse
    {
        $designRequest = DesignRequest::find($id);
        $designRequest->type = $request->type;
        $designRequest->template_id = $request->template_id;
        $designRequest->save();
        return response()->json(['result' => true, 'data' => $designRequest->load('template')], Response::HTTP_OK);
    }

    public function fetchData(Request $request)
    {
        if ($request->query->get("product-code") == true) {
            return $this->repository->getProductCode();
        }
        if ($request->query->get("template") == true) {
            return $this->repository->getTemplate($request->query->get("type"));
        }
        return response()->json(null);
    }
}
