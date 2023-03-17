<?php

namespace App\Repositories\Advertisement;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\Repository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\Advertisement\Platform;
use App\Models\Company;
use App\Repositories\QueryBuilder\UriQueryBuilder;

/**
 * Class CategoryRepository.
 */
class PlatformRepository extends Repository
{

    public function paginate(Request $request): JsonResponse
    {
        $key = $request->query->get("key") ?? 'all';
        $queryBuilder = new UriQueryBuilder(new Platform(), $request);
        $queryBuilder->setWithTrashed();
        $queryBuilder->setRelations($this->getRelations());

        $searchInColumns        = ['code', 'platform_name', 'whereHasOne,company,name',  'platform_key', 'updated_at', 'created_at'];
        $queryBuilder->query    = $this->filterRecords($queryBuilder->query, $request, $searchInColumns);
        $statusQuery            = clone  $queryBuilder->query;
        $queryBuilder->query    = $this->fetchDataAccordingToStatus($queryBuilder->query, $key);

        $queryBuilder->query->orderBy("created_at", 'desc');
        $platform         = $queryBuilder->build()->paginate()->getData();
        $extraData = [
            'allTotal',
            "currentTotal",
            'removedTotal',
        ];
        $platform = $this->getStatusCount($statusQuery, $platform, $extraData);
        return response()->json($platform);
    }

    public function store(Request $request)
    {
        try {
            $platformModel = new Platform();
            $attributes         = $request->only($platformModel->getFillable());
            $attributes['code'] = rand(1000, 9999999999);
            $result =  Platform::create($attributes);
            $result->companies()->sync($request->companies);
            return response()->json(['result' => true, 'platform' => $result], Response::HTTP_CREATED);
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }


    public function update(Request $request)
    {
        $platformModel = Platform::findOrFail($request->id);
        $attributes = [];
        try {
            DB::beginTransaction();
            $attributes['platform_name'] = $request->platform_name;
            $attributes['platform_key'] = $request->platform_key;
            $attributes['country_id'] = $request->country_id;
            $platformModel->update($attributes);
            $platformModel->companies()->sync($request->companies);
            DB::commit();
            return response()->json(['result' => true, 'platform' => $platformModel], Response::HTTP_CREATED);
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function destroy($ids)
    {

        try {
            //code...
            DB::beginTransaction();
            $platforms = Platform::withTrashed()->whereIn('id', $ids)->get();
            // ADD REASON
            foreach ($platforms as $platform) {
                $platform->trashed() ? $platform->forceDelete() : $platform->delete();
            }
            DB::commit();
            return response()->json(['result' => true], Response::HTTP_OK);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json($th->getMessage(), 404);
        }
    }


    public function storeRules(): array
    {
        return [
            'platform_name'  => ['required', 'string', 'min:3', 'max:255', "exists:platform_names,name"],
            'platform_key'   => ['required'],
            'companies' => ['required', "array"],
            "companies.*" => ['uuid', 'exists:companies,id'],
        ];
    }

    public function updateRules(): array
    {
        return [
            'platform_name'       => ['required', 'string', 'min:3', 'max:255', "exists:platform_names,name"],
            'platform_key'        => ['required'],
            'companies' => ['required', "array"],
            "companies.*" => ['uuid', 'exists:companies,id'],
        ];
    }

    private function getRelations(): array
    {
        return [
            'companies:id,name,logo',
        ];
    }

    public function fetchCompanies()
    {
        $companies = Company::whereHas("platforms")->select(["id", "name", "logo"])->get();
        return response()->json($companies, Response::HTTP_OK);
    }


    public static function connectionPlatforms()
    {
        $platforms = DB::table('platforms')
            ->join("connections", "connections.platform_id", "=", "platforms.id")
            ->select(["platforms.id", 'platforms.platform_name AS name'])
            ->groupBy("platforms.id")
            ->get();
        return $platforms;
    }


    public function show(Request $request)
    {
        if ($request->query->has('code')) {
            $query =  Platform::with($this->getRelations())
                ->where('code', $request->code);
            if ($request->key == "all") {
                $query = $query->withTrashed();
            } else if ($request->key == "removed") {
                $query = $query->onlyTrashed();
            }
            $platforms = $query->first();
            if ($platforms) {
                return response()->json($platforms, Response::HTTP_OK);
            } else {
                return response()->json(["message" => "not found"], Response::HTTP_NOT_FOUND);
            }
        }
        return response()->json(["message" => "not found"], Response::HTTP_NOT_FOUND);
    }

    public function restorePlatforms($ids)
    {
        try {
            Platform::onlyTrashed()->whereIn('id', $ids)->restore();
            return response()->json(['result' => true], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 404);
        }
    }
}
