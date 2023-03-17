<?php

namespace App\Http\Controllers;

use ActivityLog;
use App\Models\Country;
use App\Repositories\CountryRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Psy\Util\Json;

class CountryController extends Controller
{
    private $repository;
    private $ActivityLog;


    public function __construct()
    {
        $this->repository = new CountryRepository();
        $this->ActivityLog = new ActivityLog();

        $this->middleware('scope:cnv')->only(['searchCountry', 'show']);
        $this->middleware('scope:cnc')->only(['store']);
        $this->middleware('scope:cnu')->only(['update', 'changeCountryStatus']);
        $this->middleware('scope:cnd')->only(['destroy']);
        // logs
        // $this->middleware('dailylogs:masters,country,change status')->only(['changeCountryStatus']);
    }

    public function searchCountry(Request  $request): JsonResponse
    {
        return $this->repository->search($request);
    }

    // Display a listing of the resource.

    public function index(Request $request): JsonResponse
    {
        if ($this->tokenCan('cnv')) {
            if ($request->query->get("phone_code") == true) {
                return $this->repository->onlyCountryPhoneCode();
            }
            return $this->repository->paginate($request);
        } else if ($request->query->has('for_select') && $request->query->getBoolean("for_select")) {
            return response()->json(['data' => parent::getAllCountries($request)]);
        }
        return $this->repository->getUserLoggedInCountries($request);
    }

    public function changeRecordStatus(Request $request, $id)
    {
        // return $this->repository->changeRecordStatus($request, $id);
    }

    // Store a newly created resource in storage.
    public function store(Request $request): JsonResponse
    {
        $request->validate($this->repository->storeRules());
        return $this->repository->store($request);
    }

    // Update the specified resource in storage.

    public function update(Request $request): JsonResponse
    {
        $this->repository->updateRules($request);
        return $this->repository->update($request);
    }


    // Return the specified resource if exists

    public function show($id): JsonResponse
    {
        return $this->repository->show($id);
    }


    // Remove the specified resource from storage.


    // public function destroy(Request $request, $userIds): JsonResponse
    // {
    //     $userIds = explode(",", $userIds);
    //     return $this->repository->destroy($userIds, $request->query->get('reasonId'));
    // }


    public function changeCountryStatus(Request $request): JsonResponse
    {
        // $this->ActivityLog->setLog($request, 'masters', 'country', 'change status');

        return $this->repository->changeCountryStatus($request);
    }
}
