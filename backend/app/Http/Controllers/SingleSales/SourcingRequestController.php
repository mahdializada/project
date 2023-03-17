<?php

namespace App\Http\Controllers\SingleSales;

use ActivityLog;
use App\Http\Controllers\Controller;
use App\Repositories\SingleSales\SourcingRequestRepository;
use Illuminate\Http\Request;

class SourcingRequestController extends Controller
{
    private $repository;
    private $ActivityLog;

    public function __construct()
    {
        $this->repository = new SourcingRequestRepository();
        $this->ActivityLog = new ActivityLog();
    }


    public function index(Request  $request)
    {
        return $this->repository->paginate($request);
    }


    public function store(Request  $request)
    {
        
        $request->validate($this->repository->storeRules());
        return $this->repository->store($request);
    }

    public function show(Request  $request)
    {
        return $this->repository->show($request);
    }


    public function update(Request  $request)
    {
       
        $this->repository->updateRules($request);
        //        $this->ActivityLog->setLog($request, 'SSMS', 'sourcing request', 'update');

        return $this->repository->update($request);
    }


    public function destroy(Request $request, $ids)
    {
        $ids        = explode(",", $ids);
        $reason_ids    = explode(',', $request->get('reasons'));
        return $this->repository->destroy($ids, $reason_ids);
    }


    public function changeStatus(Request  $request)
    {
        return $this->repository->changeSourcingRequestStatus($request);
    }

    public function searchSourcer(Request $request)
    {
        return $this->repository->searchSourcer($request);
    }

    public function assignUser(Request $request){
        if ($request->type === "unassign") {
            return  $this->repository->unAssign($request);
        }
        return $this->repository->assign($request);
    }
}
