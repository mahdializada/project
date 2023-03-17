<?php


namespace App\Repositories;


use Exception;
use App\Models\Country;
use App\Traits\FileTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class CountryRepository extends Repository
{
    use FileTrait;

    public function paginate(Request $request): JsonResponse
    {
        if ($request->query->has("withCompany")) {
            if ($request->query->has("fields")) {
                return $this->getCompanyCountries($request->query("fields"));
            }
            return response()->json($this->getCountriesWithCompanies());
        } else {
            $key = $request->query->get("key");
            return $this->getCountriesAccordingToStatus($request, $key);
        }
    }

    public function getCompanyCountries($fields)
    {
        if (is_string($fields)) {
            $fields = explode(",", $fields);
        }
        $country = new Country();
        $fillable = $country->getFillable();
        $fillable[] = "id";
        $diff = array_diff($fields, $fillable);
        if (count($diff) > 0 && $diff[0] != '*') {
            $response = [
                "message" => "Some fields are not allowed to this endpoint",
                "fields" => array_values($diff),
                "status" => false,
            ];
            return response()->json($response, Response::HTTP_NOT_ACCEPTABLE);
        }

        $countries = Country::whereHas('companies')->whereStatus('active')
            ->orderBy('name', 'asc')->get($fields);
        return response()->json($countries, Response::HTTP_OK);
    }

    private function getCountriesAccordingToStatus($request, $key): JsonResponse
    {
        $queryBuilder = new UriQueryBuilder(new Country(), $request);

        if ($request->query->has('countriesHasCompanies')) {
            $queryBuilder->query = $queryBuilder->query->has('companies');
        }

        if ($request->query->has("for_select")) {
            $queryBuilder->query = $queryBuilder->query->select(['id', 'name', 'iso2', 'code']);
        }

        $searchInColumns    = ['name', 'national', 'native', 'region', 'subregion', 'currency_name', 'capital', 'phone_code', 'numeric_code', 'currency'];
        $queryBuilder->query = $queryBuilder->query->orderBy('name','asc');
        $queryBuilder->query = $this->filterRecords($queryBuilder->query, $request, $searchInColumns);
        $statusQuery = clone  $queryBuilder->query;
        $queryBuilder->query = $this->fetchDataAccordingToStatus($queryBuilder->query, $key);

        $countryData = $queryBuilder->build()->paginate()->getData();
        $extraData = [
            'allTotal',
            'activeTotal, status, active',
            'inactiveTotal, status, inactive',
            'blockedTotal, status, blocked'
        ];
        $countryData = $this->getStatusCount($statusQuery, $countryData, $extraData);
        return response()->json($countryData);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $country = new Country();
            $country->name = $request->input('name');
            $flagPath = $this->storeFile($request->file("flag"));
            $country->flag = $flagPath;
            $country->save();
            return $this->successResponse($country, Response::HTTP_CREATED);
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function storeRules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:32', 'unique:countries,name'],
            'flag' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];
    }

    public function update(Request $request): JsonResponse
    {
        try {
            $countryId = $request->input("id");
            $country = Country::find($countryId);
            $attributes['name'] = $request->input("name");
            if ($request->hasFile("flag_image")) {
                $flagImage = $request->file("flag_image");
                $flagPath = $this->storeAndRemove($flagImage, $country->flag);
                $attributes["flag"] = $flagPath;
            }
            $country->update($attributes);
            return $this->successResponse($country);
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function updateRules(Request $request)
    {
        $countryId = $request->input("id");
        $rules = [
            "id" => ['required', 'exists:countries,id'],
            'name' => ['required', 'unique:countries,name,' . $countryId, 'string', 'min:2', 'max:32'],
            "flag_image" => ["nullable", 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];

        $request->validate($rules);
    }


    public function show($id): JsonResponse
    {
        $country = Country::withTrashed()->find($id);
        if ($country) {
            return $this->successResponse($country);
        }
        return $this->errorResponse("Not Found");
    }


    // public function destroy($ids): JsonResponse
    // {
    //     try {
    //         $countries = Country::withTrashed()->findMany($ids);
    //         $countries->each(function ($query) {
    //             $query->trashed() ? $query->foreceDelete() : $query->delete();
    //         });
    //         return $this->successResponse($countries->pluck('id'));
    //     } catch (Exception $exception) {
    //         return $this->errorResponse($exception->getMessage());
    //     }
    // }


    private function getCountriesWithCompanies()
    {
        return Country::whereHas('companies')
            ->whereStatus('active')
            ->orderBy('name', 'asc')
            ->get(['id', 'code', 'iso2', 'name'])
            ->toArray();
    }


    /**
     * If any of ther user created by admin, doesnot have permission to view
     * the country(s), then he/she will be redirected to this route,
     */
    public function getUserLoggedInCountries(Request $request)
    {
        $countries = Country::whereHas('companies', function (Builder $builder) use ($request) {
            $builder->whereHas('users', function (Builder $builder) use ($request) {
                $builder->where('users.id', $request->user()->id);
            });
        })
            ->whereStatus('active')
            ->orderBy('name', 'asc')
            ->get(['id', 'code', 'iso2', 'name'])
            ->toArray();
        return \response()->json($countries);
    }


    public function changeCountryStatus(Request $request)
    {
        return $this->changeStatus($request, Country::class, 'countries', 'reason_country', 'country_id', [], 'country');
    }

    public function search(Request $request)
    {
        $queryBuilder = new UriQueryBuilder(new Country(), $request);
        $data = $this->searchCode($queryBuilder->query, $request);
        return $data ? response()->json($data, 200) : response()->json(['result' => false], Response::HTTP_NO_CONTENT);
    }

    // return all country with selected columns
    public function onlyCountryPhoneCode()
    {
        return $this->successResponse(Country::select(["id", "name", "iso2", "phone_code"])->get());
    }


    public static function connectionCountries()
    {
        $countries = Country::whereHas("connections")->select(["id", "name", "iso2"])->get();
        return $countries;
    }
}
