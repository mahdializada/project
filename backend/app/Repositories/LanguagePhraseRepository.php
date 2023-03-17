<?php


namespace App\Repositories;

use Exception;
use Carbon\Carbon;
use App\Models\Phrase;
use App\Models\Country;
use App\Models\CountryLanguage;
use App\Models\Language;
use App\Traits\FileTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\LanguagePhrase;
use Illuminate\Http\JsonResponse;
use App\Models\TranslatedLanguage;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use App\Repositories\QueryBuilder\UriQueryBuilder;

class LanguagePhraseRepository extends Repository
{
    private $model;

    use FileTrait;

    public function __construct()
    {
        $this->model = new LanguagePhrase();
    }

    public function paginate($request): JsonResponse
    {
        return $this->getTranslationsAccordingToStatus($request);
    }

    private function getTranslationsAccordingToStatus($request): JsonResponse
    {
        $queryBuilder = new UriQueryBuilder(new LanguagePhrase(), $request);
        $queryBuilder->setRelations($this->getRelations());
        $queryBuilder->query = $this->filterPhrase($queryBuilder->query, $request);
        $queryBuilder->query = $this->searchPhrases($queryBuilder->query, $request);
        if ($request->query->get("language_id")) {
            $translated_language = TranslatedLanguage::where(['id' => $request->query->get("language_id")])->with($this->getLanguageRelations())->first();
            $queryBuilder->query->where("translated_language_id", $request->query->get("language_id"));
        }
        if ($request->query->get("base_language")) {
            $translated_language = $this->getBaseLang();
            $queryBuilder->query->where("translated_language_id", $translated_language->id);
        }
        $languagesData = $queryBuilder->build()->paginate()->getData();
        $languagesData->language = $translated_language;
        return response()->json($languagesData);
    }

    private function getLanguageRelations()
    {
        return [
            "countryLanguage",
            "countryLanguage.country:id,name,iso2",
            "countryLanguage.language:id,name,native,dir,code",
            "createdBy:id,firstname,lastname",
            "updatedBy:id,firstname,lastname",
        ];
    }

    private function getBaseLang()
    {
        // $language = Language::where(['name' => 'English'])->first();
        // $country = Country::where(['name' => 'United States'])->first();
        // $country_language = CountryLanguage::where(['country_id' => $country->id, 'language_id' => $language->id])->first();

        $country_language = DB::table('languages')
            ->join('country_language', 'languages.id', '=', 'country_language.language_id')
            ->join('countries', 'countries.id', '=', 'country_language.country_id')
            ->select(['country_language.id'])
            ->where(['languages.name' => 'English', 'countries.name' => 'United States'])->first();
        $translated_language = TranslatedLanguage::where(
            ['country_language_id' => $country_language->id]
        )->with([
            "countryLanguage",
            "countryLanguage.country:id,name,iso2",
            "countryLanguage.language:id,name,native,dir"
        ])->first();
        return $translated_language;
    }


    public function store(Request $request): JsonResponse
    {
        try {
            $model = new LanguagePhrase();
            $attr = $request->only($model->getFillable());

            $attr['created_by'] = $request->user()->id;
            $attr['updated_by'] = $request->user()->id;
            $attr['code'] = rand(1000000, 100000000000);

            $model->where([
                'translated_language_id' => $attr['translated_language_id'],
                'phrase_id' => $attr['phrase_id']
            ])->delete();

            $translation = $model->create($attr);
            $lang = TranslatedLanguage::find($attr['translated_language_id']);
            $lang->update(['updated_at' => Carbon::now()]);
            $lang->save();
            return $this->successResponse($translation->load($this->getRelations()), Response::HTTP_CREATED);
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function storeRules(): array
    {
        return [
            "translation" => ["required"],
            "phrase_id" => ["required", "exists:phrases,id"],
            "translated_language_id" => ["required", "exists:translated_languages,id", "uuid"],
        ];
    }

    public function update(Request $request): JsonResponse
    {
        return $this->successResponse(' ', Response::HTTP_CREATED);
    }

    public function updateRules(Request $request)
    {
    }


    public function show(Request $request, $id): JsonResponse
    {
        return $this->successResponse('yes', Response::HTTP_CREATED);
    }

    private function getRelations()
    {
        return [
            "phrase:id,phrase",
            "createdBy:id,firstname,lastname",
            "updatedBy:id,firstname,lastname",
        ];
    }

    public function changePhraseStatus(Request $request)
    {
    }


    public function destroy(array $ids, $reasonId)
    {
    }

    public function filterPhrase($query, $request)
    {
        $department_id = $request->department_id;
        $created_date = $request->created_date;
        $updated_date = $request->updated_date;
        $name = $request->name;
        $include_id = $request->include;
        $team_ids = $request->team_ids;
        if ($department_id != null) {
            $query = $query->whereHas('departments', function ($query) use ($department_id) {
                $query->where(function ($q) use ($department_id) {
                    $q->where('department_id', $department_id);
                });
            });
        }
        if ($created_date != null) {
            if (count($created_date) == 2) {
                $start_date = $created_date[0];
                $end_date = $created_date[1];
            } else {
                $start_date = $created_date[0];
                $end_date = null;
            }
            if ($start_date != null) {
                $start_date_format = Carbon::createFromFormat('Y-m-d', $start_date);
            }
            if ($end_date != null) {
                $end_date_format = Carbon::createFromFormat('Y-m-d', $end_date);
            }
            if ($start_date == null) {
                $year = $end_date_format->format('Y');
                $month = $end_date_format->format('m');
                $day = '01';
                $start_date = $year . '-' . $month . '-' . $day;
                $start_date_format = Carbon::createFromFormat('Y-m-d', $start_date);
            }
            if ($end_date) {
                if ($end_date_format->gt($start_date_format)) {
                    $query = $query->whereBetween(DB::raw('DATE(created_at)'), array($start_date_format, $end_date_format));
                } elseif ($end_date_format->lessThan($start_date_format)) {
                    $query = $query->whereBetween(DB::raw('DATE(created_at)'), array($end_date_format->subDay(), $start_date_format));
                } else {
                    $query = $query->whereDate('created_at', $start_date_format);
                }
            } else {
                $query = $query->whereDate('created_at', $start_date_format);
            }
        }
        if ($updated_date != null) {
            if (count($updated_date) == 2) {
                $start_date = $updated_date[0];
                $end_date = $updated_date[1];
            } else {
                $start_date = $updated_date[0];
                $end_date = null;
            }
            if ($start_date != null) {
                $start_date_format = Carbon::createFromFormat('Y-m-d', $start_date);
            }
            if ($end_date != null) {
                $end_date_format = Carbon::createFromFormat('Y-m-d', $end_date);
            }
            if ($start_date == null) {
                $start_date_format = $end_date_format->startOfMonth('Y-m-d');
            }
            if ($end_date) {
                if ($end_date_format->gt($start_date_format)) {
                    $query = $query->whereBetween(DB::raw('DATE(updated_at)'), array($start_date_format->subDay(), $end_date_format));
                } elseif ($end_date_format->lt($start_date_format)) {
                    $query = $query->whereBetween(DB::raw('DATE(updated_at)'), array($end_date_format->subDay(), $start_date_format));
                } else {
                    $query = $query->whereDate('updated_at', $start_date_format);
                }
            } else {
                $query = $query->whereDate('updated_at', $start_date_format);
            }
        }
        if ($name != null) {
            $query = $query->where('name', 'like', '%' . $name . '%');
        }
        if ($team_ids != null) {
            if (count($team_ids) > 0) {
                if ($include_id != null) {
                    if ($include_id == 0) {
                        $query = $query->whereIn('code', $team_ids);
                    } elseif ($include_id == 1) {
                        $query = $query->whereNotIn('code', $team_ids);
                    }
                }
            }
        }

        return $query;
    }

    public function searchPhrases($query, $request)
    {
        $type = $request->type;
        $content = $request->contentData;
        if ($content != null) {
            if ($type == 0) {
                $query = $query->where(function ($query) use ($content) {
                    return $query->where('name', 'like', '%' . $content . '%');
                });
            }
        }
        return $query;
    }

    public function search($request): JsonResponse
    {

        $type = $request->type;
        $content = $request->content;
        $queryBuilder = new UriQueryBuilder(new LanguagePhrase(), $request);
        $queryBuilder->setWithTrashed();
        $queryBuilder->setRelations($this->getRelations());
        $queryBuilder->query = $this->filterPhrase($queryBuilder->query, $request);
        if ($content != null) {
            if ($type == 1) {
                $languagesData = $queryBuilder->query->where('code', last($content))->with($this->getRelations())->first();
                if ($languagesData) {
                    return response()->json($languagesData, 200);
                }
            }
        }
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function forSystem(Request $request)
    {
        if ($request->query->get("baseLanguage")) {
            $lang = $this->getBaseLang();
            $translate = LanguagePhrase::select(['code', 'translation', 'id', 'phrase_id'])->where("translated_language_id", $lang->id)->with('phrase:code,id,phrase')->get();
        } else {
            $lang = TranslatedLanguage::where(['id' => $request->query->get("language_id")])->with($this->getLanguageRelations())->first();
            $translate = LanguagePhrase::select(['code', 'translation', 'id', 'phrase_id'])->where("translated_language_id", $request->query->get("language_id"))->with('phrase:code,id,phrase')->get();
        }
        $ttt = [];
        foreach ($translate as $t) {
            $ttt[$t->phrase->phrase] = $t->translation;
        }
        return response()->json(['translations' => $ttt, 'language' => $lang], Response::HTTP_OK);
    }
    public function isUptoDate(Request $request)
    {
        //     language_id: translations.language.id,
        //       updated_at: translations.language.updated_at,
        $lang = TranslatedLanguage::where(['id' => $request->query->get("language_id"), 'updated_at' => Carbon::create($request->query->get("updated_at"))->toDateTimeString()])->first();
        return response()->json(['result' => $lang ? true : false], Response::HTTP_OK);
    }
}
