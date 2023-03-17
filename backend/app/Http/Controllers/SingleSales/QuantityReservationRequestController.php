<?php

namespace App\Http\Controllers\SingleSales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\SingleSales\QuantityReservation\QuantityReservationRepository;
class QuantityReservationRequestController extends Controller
{
    private $repository;
    public function __construct()
    {
        $this->repository = new QuantityReservationRepository();
    }

    public function index(Request  $request){
        return $this->repository->paginate($request);
    }


    public function store(Request  $request){
        $request->validate($this->repository->storeRules());
        return $this->repository->store($request);
    }


    public function show(Request  $request)
    {
        return $this->repository->show($request);
    }


    public function update(Request  $request){
        $this->repository->updateRules($request);
//        $this->ActivityLog->setLog($request, 'SSMS', 'sourcing request', 'update');

        return $this->repository->update($request);
    }


    public function destroy(Request $request,$ids){
        $ids        = explode(",", $ids);
        $reason_ids    = explode(',', $request->get('reasons'));
        return $this->repository->destroy($ids, $reason_ids);
    }


    public function changeStatus(Request  $request){
        return $this->repository->changeQuantityStatus($request);
    }

}
