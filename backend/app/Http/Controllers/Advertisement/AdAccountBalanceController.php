<?php

namespace App\Http\Controllers\Advertisement;

use App\Http\Controllers\Controller;
use App\Repositories\Advertisement\BalanceRepository;
use Illuminate\Http\Request;

class AdAccountBalanceController extends Controller
{

    public function __construct()
    {

        $this->repository = new BalanceRepository();
    }
    public function index()
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
        $request->validate($this->storeRules());

        return  $this->repository->store($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        return  $this->repository->show($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return  $this->repository->destroy($id);
    }

    public function storeRules(): array
    {
        return [
            'amount'         => ['required'],
            'payment_type'        => 'required|string',
            'currency'         => 'required|string',

        ];
    }
}
