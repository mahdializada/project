<?php

namespace App\Http\Controllers;

use App\Models\Sourcer;
use App\Repositories\SourcerRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SourcerController extends Controller
{
    private $repository;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(){
        $this->repository = new SourcerRepository();
    }
    public function index(Request $request)
    {
        return $this->repository->paginate($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->repository->storeSourcer($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Sourcer  $sourcer
     * @return \Illuminate\Http\Response
     */
    public function show(Sourcer $sourcer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sourcer  $sourcer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $request->validate($this->repository->updateRules());
        return $this->repository->update($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Sourcer  $sourcer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $ids)
    {
        $ids        = explode(",", $ids);
        return $this->repository->destroy($ids, $request->query->get('reasonId'), $request);
    }
    public function changeSourcersStatus(Request $request)
    {
        return $this->repository->changeSourcersStatus($request);
    }
        public function searchSourcer(Request  $request): JsonResponse
    {
        return $this->repository->search($request);
    }
    public function getCompanyAndCountry(){
        
        $country = DB::select('select country.id,country.name from countries as country join sourcers on country.id = sourcers.country_id');
        $company = DB::select('select company.id,company.name from companies as company join sourcers on company.id = sourcers.company_id');
        return response()->json(['country'=>$country,'company'=>$company]);
    }
}
