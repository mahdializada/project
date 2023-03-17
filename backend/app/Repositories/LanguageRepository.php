<?php


namespace App\Repositories;

use App\Models\Country;
use App\Models\Language;
use App\Models\TranslatedLanguage;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class LanguageRepository extends Repository
{

    public function paginate(Request $request): JsonResponse
    {
        if ($request->query->has("withCountries")) {
            return $this->languageWithCountries();
        }
        if ($request->query->has("type") && $request->query->get("type") === "deleted") {
            return $this->paginateTrashed($request);
        }
        if ($request->query->has("countryId")) {
            return $this->countryLanguages($request->query->get("countryId"));
        }

        return $this->paginateUnTrashed($request);
    }


    private function paginateUnTrashed(Request $request): JsonResponse
    {
        $queryBuilder = new UriQueryBuilder(new Language(), $request);
        return $queryBuilder->build()->paginate();
    }

    private function paginateTrashed(Request $request): JsonResponse
    {
        $queryBuilder = new UriQueryBuilder(new Language(), $request, true);
        return $queryBuilder->build()->paginate();
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $language = new Language();
            $language->name = $request->input('name');
            $language->country_id = $request->input("country_id");
            $language->save();
            return $this->successResponse($language, Response::HTTP_CREATED);
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function storeRules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:32', 'unique:languages,name'],
            'country_id' => ['required', 'exists:countries,id'],
        ];
    }

    public function update(Request $request): JsonResponse
    {
        try {
            $languageId = $request->input("id");
            $language = Language::find($languageId);
            $attributes['name'] = $request->input("name");
            $attributes["country_id"] = $request->input("country_id");
            $language->update($attributes);
            return $this->successResponse($language);
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }


    // return country languages
    public function countryLanguages(string $countryId): JsonResponse
    {
        $languages = Country::with("languages")->find($countryId)->getRelation("languages");
        return $this->successResponse($languages);
    }
    private function languageWithCountries(): JsonResponse
    {
        $trlang = TranslatedLanguage::get('country_language_id');
        $languages = DB::table('country_language')->select(
            [
                'country_language.id as country_language_id',
                'countries.id as country_id',
                'languages.id as language_id',
                'languages.dir as dir',
                'countries.iso2',
                'countries.name as country_name',
                'languages.name as language_name',
                'languages.native as language_native'
            ]
        )->whereNotIn('country_language.id', $trlang)
            ->join('countries', 'country_language.country_id', '=', 'countries.id')
            ->join('languages', 'country_language.language_id', '=', 'languages.id')
            ->get();
        return $this->successResponse($languages);
    }
    public function updateRules(Request $request)
    {
        $languageId = $request->input("id");
        $rules = [
            "id" => ['required', 'exists:languages,id'],
            'name' => ['required', 'unique:languages,name,' . $languageId, 'string', 'min:2', 'max:32'],
            "country_id" => ["required", "exists:countries,id"],
        ];

        $request->validate($rules);
    }


    public function show($id): JsonResponse
    {
        $language = Language::find($id);
        if ($language) {
            return $this->successResponse($language);
        }
        return $this->errorResponse("Not Found");
    }


    public function destroy(array $ids): JsonResponse
    {
        try {
            $languages = Language::withTrashed()->findMany($ids);
            $languages->each(function ($query) {
                $query->trashed ? $query->foreceDelete() : $query->delete();
            });
            return $this->successResponse($languages->pluck('id'));
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }
}
