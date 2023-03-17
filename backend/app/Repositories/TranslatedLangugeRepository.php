<?php


namespace App\Repositories;

use App\Models\LanguagePhrase;
use App\Models\Phrase;
use Exception;
use Carbon\Carbon;
use App\Models\TranslatedLanguage;
use App\Traits\FileTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Repositories\QueryBuilder\UriQueryBuilder;

class TranslatedLangugeRepository extends Repository
{
    private $model;

    use FileTrait;

    public function __construct()
    {
        $this->model = new TranslatedLanguage();
    }

    public function paginate($request): JsonResponse
    {
        $key = $request->query->get("key");
        return $this->getLanguagesAccordingToStatus($request, $key);
    }

    private function getLanguagesAccordingToStatus($request, $key): JsonResponse
    {

        $queryBuilder = new UriQueryBuilder(new TranslatedLanguage(), $request);
        $queryBuilder->setRelations($this->getRelations());
        $queryBuilder->setWithTrashed();

        $searchInColumns    = ['status', 'whereHasTwo, countryLanguage, language, name, native', 'direction', 'updated_at', 'created_at'];
        $queryBuilder->query = $this->filterRecords($queryBuilder->query, $request, $searchInColumns);
        $statusQuery = clone  $queryBuilder->query;
        $queryBuilder->query = $this->fetchDataAccordingToStatus($queryBuilder->query, $key);

        $languagesData = $queryBuilder->build()->paginate()->getData();
        $extraData = [
            'allTotal',
            'activeTotal, status, active',
            'inactiveTotal, status, inactive',
            'blockedTotal, status, blocked',
            'pendingTotal, status, pending',
            'removedTotal'
        ];
        $languagesData = $this->getStatusCount($statusQuery, $languagesData, $extraData);
        foreach ($languagesData->data as $data) {
            $languagePhrase = LanguagePhrase::where('translated_language_id', $data->id)->with('phrase')->get();
            $frontendCount = 0;
            $backendCount = 0;
            foreach ($languagePhrase as $lang) {
                if ($lang->phrase->word_type == 'frontend') {
                    $frontendCount++;
                } else if ($lang->phrase->word_type == 'backend') {
                    $backendCount++;
                }
            }
            $data->translatedCount = count($languagePhrase);
            $data->frontendCount = $frontendCount;
            $data->backendCount = $backendCount;
        }
        $languagesData->totalWords       = Phrase::count();
        $languagesData->frontendWords    = Phrase::where("word_type", "frontend")->count();
        $languagesData->backendWords     = Phrase::where("word_type", "backend")->count();

        return response()->json($languagesData);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $model = new TranslatedLanguage();
            $attributes['country_language_id'] = $request->selectedLangaugeId;
            $attributes['direction'] = $request->direction;
            $attributes['created_by'] = $request->user()->id;
            $attributes['updated_by'] = $request->user()->id;
            $attributes['status'] = 'pending';
            $attributes['code'] = rand(100000, 9999999999);
            // return response()->json($attributes);
            $lang = $model->create($attributes);

            return $this->successResponse($lang->load($this->getRelations()), Response::HTTP_CREATED);
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function storeRules(): array
    {
        return [
            "direction" => ["required"],
            "selectedLangaugeId" => ["required", "exists:country_language,id"],
        ];
    }

    public function update(Request $request): JsonResponse
    {
        try {
            $teamModal = new TranslatedLanguage();
            $teamId = $request->input("id");
            $team = $teamModal::find($teamId);
            $attributes = $request->only($teamModal->getFillable());
            if ($request->icon !== "null") {
                $this->prefix = "admin/team";
                $logoPath = $this->storeFile($request->file("icon"));
                $attributes["logo"] = $logoPath;
            }
            $attributes["updated_by"] = $request->user()->id;
            $attributes['end_time'] = $attributes['end_time'] != 'null' ? $attributes['end_time'] : null;

            $attributes["number_of_people"] = $request->user_ids != null ? count($request->user_ids) : 0;

            $team->update($attributes);

            $members = $team->members()->sync($request->user_ids);

            $team->departments()->sync($request->department_ids);

            $permissions = $request->permissions;

            $actionSubSystems = array();
            $systems = array();
            if ($permissions) {
                foreach ($permissions as $permission) {
                    $permission = json_decode($permission);
                    array_push($actionSubSystems, ['sub_system_id' => $permission->sub_system_id, 'action_id' => $permission->action_id]);
                }
                foreach ($actionSubSystems as $sub) {
                    $res = DB::table("action_sub_system")
                        ->where("sub_system_id", $sub['sub_system_id'])
                        ->where("action_id", $sub['action_id'])
                        ->get();
                    array_push($systems, [
                        'action_sub_system_id' => $res[0]->id,
                        'updated_at' => Carbon::now()
                    ]);
                }
                $team->permissions()->detach();
                $team->permissions()->attach($systems);
            }
            $team->save();
            return $this->successResponse($team->load($this->getRelations()));
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function updateRules(Request $request)
    {
        $teamId = $request->input("id");
        $rules = [
            "id" => ["required", "exists:teams,id"],
            "department_ids" => ["required", "array"],
            "department_ids.*" => ["exists:departments,id", "uuid"],
            "user_ids" => ["array"],
            "user_ids.*" => ["exists:users,id", "uuid"],
            "name" => ["required", "string", "min:2", "max:50"],
            "schedule_type" => ["required"],
            "end_time" => ["required"],
            "note" => ["required"],
            "permissions" => ["array"],
        ];
        if ($request->icon !== "null") {
            $rules['icon'] = ["image", "file", "max:1024"];
        }
        $request->validate($rules);
    }


    public function show(Request $request, $id): JsonResponse
    {
        if ($request->query->has("fetchByCode")) {
            $team = $this->model->with($this->getRelations())->where('code', $id)->first();
            if ($team) {
                return $this->successResponse($team);
            }
        }
        if ($request->query->has("baseLanguage")) {
            $team = $this->model->with($this->getRelations())->where('code', $id)->first();
            if ($team) {
                return $this->successResponse($team);
            }
        }
        $team = $this->model->with($this->getRelations())->find($id);
        if ($team) {
            return $this->successResponse($team);
        }
        return $this->errorResponse("Not Found");
    }

    private function getRelations()
    {
        return [
            "countryLanguage",
            "countryLanguage.country:id,name,iso2",
            "countryLanguage.language:id,name,native,dir,code",
            "createdBy:id,firstname,lastname",
            "updatedBy:id,firstname,lastname",
        ];
    }


    public function destroy(array $ids, $reasonId)
    {
        return $this->destroyItems(TranslatedLanguage::class, $ids, 'reason_translated_language', $reasonId, 'translated_language_id', null, $this->getRelations(), '');
    }

    public function changeLanguageStatus(Request $request)
    {
        return $this->changeStatus($request, TranslatedLanguage::class, 'translated_languages', 'reason_translated_language', 'translated_language_id');
    }

    public function searchLanguages($query, $request)
    {
        $content = $request->input('searchContent');
        if ($content != null) {
            $query = $query->whereHas('countryLanguage', function ($query) use ($content) {
                $query->whereHas('language', function ($q1) use ($content) {
                    $q1->where(function ($q2) use ($content) {
                        $q2->where('name', 'like', '%' . $content . '%')
                            ->orWhere('native', 'like', '%' . $content . '%');
                    });
                });
            });
        }
        return $query;
    }

    public function search($request): JsonResponse
    {
        $queryBuilder = new UriQueryBuilder(new TranslatedLanguage(), $request);
        $data = $this->searchCode($queryBuilder->query, $request, $this->getRelations());
        return $data ? response()->json($data, 200) : response()->json(['result' => false], Response::HTTP_NO_CONTENT);
    }
}
