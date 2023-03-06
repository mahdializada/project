<?php

namespace App\Http\Controllers\Advertisement;

use App\Models\Currency;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Advertisement\CurrencyRepository;

class CurrencyController extends Controller
{
    private CurrencyRepository $repository;


    public function __construct()
    {

        $this->repository = new CurrencyRepository();
    }

    public function index(Request $request)
    {
        return  $this->repository->paginate($request);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function currencyList()
    {
        $allowed_currecies = ["USD", "AED"];
        $columns = ["id", "name", "symbol", "code"];
        $relation = ["country" => function ($query) {
            $query->whereIn("iso2", ["AE", "US"])->select(["iso2", "currency"]);
        }];
        $currenies  = Currency::with($relation)->whereHas("country")
            ->whereIn("code", $allowed_currecies)
            ->select($columns)->get();
        return response()->json($currenies);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return  $this->repository->store($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
    public function destroy($ids)
    {
        $ids        = explode(",", $ids);
        return  $this->repository->destroy($ids);
    }

    public function getAllCurrencies()
    {
        return  $this->repository->getAllCurrencies();
    }

    public function changeStatus(Request $request)
    {
        return  $this->repository->changeDailyRateStatus($request);
    }
}
