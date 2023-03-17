<?php

namespace App\Http\Controllers\SingleSales;

use App\Http\Controllers\Controller;
use App\Repositories\SingleSales\SalesChanelRepository;
use Illuminate\Http\Request;

class SalesChanelController extends Controller
{

    private $repository;
    public function __construct(SalesChanelRepository $repository)
    {
        $this->repository = $repository;
    }
  
    public function index(Request $request)
    {
        return  $this->repository->paginate($request);
    }

    

     
    public function store(Request $request)
    {
        $request->validate($this->repository->storeRules());
        return $this->repository->store($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        return $this->repository->show($request);
    }

     
 
    public function update(Request $request)
    {
        $this->repository->update($request);
    }

    public function destroy(Request $request,$ids){
        $ids        = explode(",", $ids);
        $reason_ids    = explode(',', $request->get('reasons'));
        return $this->repository->destroy($ids, $reason_ids);
    }

}
