<?php

namespace App\Http\Controllers\Advertisement;

use App\Models\Advertisement\BankAccount;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Advertisement\AdvertisementTabs\BankAccountRepository;

class BankAccountController extends Controller
{
    protected $repository ;
    public function __construct()
    {
        $this->repository= new BankAccountRepository();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {   
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->repository->storerole($request);
        return $this->repository->storeRepository($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Advertisement\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->repository->read($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Advertisement\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->repository->storerole($request);
        return $this->repository->isUpdate($id,$request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Advertisement\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->repository->deleted($id);
    }
}
