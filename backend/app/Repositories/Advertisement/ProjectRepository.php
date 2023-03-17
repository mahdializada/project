<?php

namespace App\Repositories\Advertisement;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\Repository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\Advertisement\Project;
use App\Repositories\QueryBuilder\UriQueryBuilder;


class ProjectRepository extends Repository
{
    public function paginate(Request $request): JsonResponse
    {
        $key = $request->query->get("key");
        $queryBuilder = new UriQueryBuilder(new Project(), $request);
        $queryBuilder->setWithTrashed();
        $searchInColumns = ['code', 'companies', "countries", "name", 'domain'];
        $queryBuilder->query = $this->filterRecords($queryBuilder->query, $request, $searchInColumns);
        $statusQuery = clone $queryBuilder->query;
        $queryBuilder->query = $this->fetchDataAccordingToStatus($queryBuilder->query, $key);

        $queryBuilder->query->orderBy("created_at", 'desc');
        $designRequest = $queryBuilder->build()->paginate()->getData();
        $extraData = [
            'allTotal',
            "currentTotal",
            'removedTotal',
        ];
        $designRequest = $this->getStatusCount($statusQuery, $designRequest, $extraData);
        return response()->json($designRequest);
    }

    public function show(Request $request)
    {
        if ($request->query->has('code')) {
            $query =  Project::where('code', $request->code);
            if ($request->key == "all") {
                $query = $query->withTrashed();
            } else if ($request->key == "removed") {
                $query = $query->onlyTrashed();
            }
            $projects = $query->first();
            if ($projects) {
                return response()->json($projects, Response::HTTP_OK);
            } else {
                return response()->json(["message" => "not found"], Response::HTTP_NOT_FOUND);
            }
        }
        return response()->json(["message" => "not found"], Response::HTTP_NOT_FOUND);
    }



    public function store(Request $request)
    {
        try {
            $project = new Project();
            $attributes = $request->only($project->getFillable());
            $attributes['code'] = rand(10000, 8999999999);
            $attributes["countries"] = json_encode($attributes["countries"]);
            $attributes["companies"] = json_encode($attributes["companies"]);
            $project = Project::create($attributes);
            return response()->json(["result" => true, "project" => $project], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 501);
        }
    }

    public function update(Request $request)
    {
        try {
            $project = Project::withTrashed()->find($request->id);
            $attributes = $request->only($project->getFillable());
            unset($attributes["id"]);
            unset($attributes["code"]);
            $attributes["countries"] = json_encode($attributes["countries"]);
            $attributes["companies"] = json_encode($attributes["companies"]);
            $project->update($attributes);
            return response()->json(["result" => true, "project" => $project], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 501);
        }
    }


    public function validationRule($is_update = false): array
    {
        $domain_validation = '/^(?!:\/\/)(?=.{1,255}$)((.{1,63}\.){1,127}(?![0-9]*$)[a-z0-9-]+\.?)$/i';
        $rules =  [
            'name' => ['required', 'min:1', "max:255", "string"],
            'countries' => ['required', 'array'],
            'countries.*' => ['exists:countries,id', "uuid"],
            'companies' => ['required', 'array'],
            'companies.*' => ['exists:companies,id', "uuid"],
            'domain' => ['nullable', "regex:$domain_validation"],
        ];
        if ($is_update) $rules["id"] = ['required', 'exists:projects,id'];
        return $rules;
    }


    public function destroy($ids)
    {
        try {
            DB::beginTransaction();
            $projects = Project::withTrashed()->whereIn('id', $ids)->get();
            foreach ($projects as $project) {
                $project->trashed() ? $project->forceDelete() : $project->delete();
            }
            DB::commit();
            return response()->json(['result' => true], Response::HTTP_OK);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json($th->getMessage(), 404);
        }
    }
    public function restoreProjects($ids)
    {
        try {
            Project::onlyTrashed()->whereIn('id', $ids)->restore();
            return response()->json(['result' => true], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 404);
        }
    }
}
