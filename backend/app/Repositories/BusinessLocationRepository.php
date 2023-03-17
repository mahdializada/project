<?php

namespace App\Repositories;

use ActivityLog;
use App\Models\BusinessLocation;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BusinessLocationRepository extends Repository
{

    private $ActivityLog;
    public function __construct()
    {
        $this->ActivityLog = new ActivityLog();
    }


    public function paginate(Request $request): JsonResponse
    {
        if ($request->query->get("for_select")) {
            return $this->getBLForSelect();
        } else if ($request->query->has('key') && $request->get('key') == 'only-business-locations') {
            return $this->getOnlyBusinessLocations();
        }
        $tableTabKey = $request->query->get("key");
        return $this->getBusinessLocationsAccordingToStatus($request, $tableTabKey);
    }

    function getOnlyBusinessLocations()
    {
        $businessLocations = BusinessLocation::whereStatus('active')->get(['id', 'name', 'code'])->toArray();
        return $this->successResponse($businessLocations, 200);
    }

    private function getBLForSelect()
    {
        $bl = BusinessLocation::select(['id', 'code', 'name'])
            ->where('status', 'active')
            ->get();
        return response()->json(['data' => $bl], Response::HTTP_OK);
    }


    private function getBusinessLocationsAccordingToStatus($request, $key)
    {
        $queryBuilder = new UriQueryBuilder(new BusinessLocation(), $request);
        $queryBuilder->setRelations($this->getRelations());
        $queryBuilder->setWithTrashed();

        $searchInColumns    = ['name', 'status', 'email', 'location_type', 'map_link', 'address', 'updated_at', 'created_at'];
        $queryBuilder->query = $this->filterRecords($queryBuilder->query, $request, $searchInColumns);
        $statusQuery = clone  $queryBuilder->query;
        $queryBuilder->query = $this->fetchDataAccordingToStatus($queryBuilder->query, $key);

        $businessLocationData = $queryBuilder->build()->paginate()->getData();
        $extraData = [
            'allTotal',
            'activeTotal, status, active',
            'inactiveTotal, status, inactive',
            'blockedTotal, status, blocked',
            'pendingTotal, status, pending',
            'warningTotal, status, warning',
            'removedTotal'
        ];
        $businessLocationData = $this->getStatusCount($statusQuery, $businessLocationData, $extraData);
        return response()->json($businessLocationData);
    }

    /**
     * Store resouces into database
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $businessLocation = new BusinessLocation();
            $attributes = $request->only($businessLocation->getFillable());
            $attributes["code"] = (string) $this->randomCode();
            $attributes["created_by"] = $request->user()->id;
            $businessLocation = BusinessLocation::create($attributes);

            $businessLocation->load($this->getRelations())->refresh();
            $this->ActivityLog->setLog($request, 'masters', 'business_location', 'insert');

            return $this->successResponse($businessLocation, Response::HTTP_CREATED);
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Generate Random Code for user
     * @return int
     */
    private function randomCode(): int
    {
        return rand(10000, 8999999999);
    }


    /**
     * Validations rules for creating new user into server
     * @return array
     */
    public function storeRules(): array
    {
        return [
            'name'                      => ['required', 'string', 'min:2', 'max:32', 'unique:business_locations,name'],
            'email'                     => ['required', 'email', 'unique:users,email'],
            "address"                   => ["required", "string"],
            "note"                      => ["required", "string"],
            "location_type"             => ["required", "string"],
            "map_link"                  => ["nullable", "url"],
            "country_id"                => ["required", "exists:countries,id", "uuid"],
            "state_id"                  => ["required", "exists:states,id", "uuid"],
            "company_id"                => ["required", "exists:companies,id", "uuid"],
        ];
    }



    public function update(Request $request): JsonResponse
    {
        try {

            $businessLocation            = BusinessLocation::find($request->input("id"));
            $attributes                  = $request->only($businessLocation->getFillable());

            $attributes["updated_by"]    = $request->user()->id;
            unset($attributes["status"]);
            unset($attributes["id"]);
            unset($attributes["code"]);
            if (is_null($request->input("map_link"))) {
                unset($attributes["map_link"]);
            }
            $businessLocation->update($attributes);

            $businessLocation->load($this->getRelations())->refresh();
            $this->ActivityLog->setLog($request, 'masters', 'business_location', 'update');
            return $this->successResponse($businessLocation, Response::HTTP_OK);
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    // rules for updating items
    public function updateRules(Request $request)
    {
        $businessLocationId                        = $request->get("id");
        $validationRules                           = $this->storeRules();
        $validationRules["name"]                  = ['required', 'string', 'min:2', 'max:32', 'unique:business_locations,name,' . $businessLocationId];
        $validationRules["email"]                  = ['required', 'email', 'unique:business_locations,email,' . $businessLocationId];
        $validationRules["id"]                     = ['required', 'uuid', 'exists:business_locations,id'];
        unset($validationRules["map_link"]);
        $request->validate($validationRules);
    }

    /**
     * return relations
     * @return string[]
     */
    private function getRelations(): array
    {
        return [
            'reasons',
            'changedBy',
            "company:id,name,logo,code",
            "country:id,name,iso2,iso3,national",
            "state:id,name",
            "updatedBy:id,firstname,lastname,email,username,image,code,phone,gender",
            "createdBy:id,firstname,lastname,email,username,image,code,phone,gender",
        ];
    }


    // return a business location record
    public function show($id): JsonResponse
    {
        $relations = ["company.countries:id,name,iso2"];
        $businessLocation = BusinessLocation::withTrashed()->with($relations)->find($id);
        if ($businessLocation) {
            return $this->successResponse($businessLocation);
        }
        return $this->errorResponse("Not Found", Response::HTTP_NOT_FOUND);
    }


    // change business location status
    public function changeBusinessLocationStatus(Request $request)
    {
        return $this->changeStatus($request, BusinessLocation::class, 'business_locations', 'business_location_reason', 'business_location_id');
    }

    // check for uniqueness columns and values is already exists in database or not
    public function checkUniqueness(Request $request): JsonResponse
    {
        $columns                        = $request->all();
        $businessLocationId             = $request->query->get("businessLocationId");
        $hasBusinessLocationId          = $request->query->has("businessLocationId");
        $result                         = [];

        foreach ($columns as $column) {

            $column                     = collect($column);
            $columnName                 = $column->get("columnName");
            $columnValue                = $column->get("value");

            if ($hasBusinessLocationId) {
                $isExists = BusinessLocation::where($columnName, $columnValue)->where("id", "!=", $businessLocationId)->exists();
            } else {
                $isExists = BusinessLocation::where($columnName, $columnValue)->exists();
            }
            $result[$columnName] = $isExists;
        }

        return response()->json($result);
    }


    // check the uniqueness of columns and its related values in database
    public function checkUniquenesOfCulumnsWithIndex(Request $request): JsonResponse
    {
        $result = [];
        $columns = $request->all();
        foreach ($columns as $column) {
            $column = collect($column);
            $isExists = BusinessLocation::where($column->get("name"), $column->get("value"))->exists();
            $result[] = [
                'column' => $column->get("name"),
                'if_exists' => $isExists,
                'column_value' => $column->get('value'),
                'index' => $column->get('index')
            ];
        }
        unset($result['']);
        return response()->json($result);
    }

    // remove a business location
    public function destroy(array $ids, $reasonId)
    {
        return $this->destroyItems(BusinessLocation::class, $ids, 'business_location_reason', $reasonId, 'business_location_id', null, [], 'business-locations');
    }

    public function search($request)
    {
        $queryBuilder = new UriQueryBuilder(new BusinessLocation(), $request);
        $data = $this->searchCode($queryBuilder->query, $request, $this->getRelations());
        return $data ? response()->json($data, 200) : response()->json(['result' => false], Response::HTTP_NO_CONTENT);
    }
}
