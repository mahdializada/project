<?php


namespace App\Repositories;


use App\Models\Warning;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserWarningRepository extends Repository
{

    public function paginate(Request $request): JsonResponse
    {
        if ($request->query->has("type") && $request->query->get("type") === "deleted") {
            return $this->paginateTrashed($request);
        }
        return $this->paginateUnTrashed($request);
    }


    private function paginateUnTrashed(Request $request): JsonResponse
    {
        $queryBuilder = new UriQueryBuilder(new Warning(), $request);
        return $queryBuilder->build()->paginate();
    }

    private function paginateTrashed(Request $request): JsonResponse
    {
        $queryBuilder = new UriQueryBuilder(new Warning(), $request, true);
        return $queryBuilder->build()->paginate();
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $warning = new Warning();
            $warning->title = $request->input("title");
            $warning->description = $request->input("description");
            $warning->user_id = $request->input("user_id");
            $warning->save();

            return $this->successResponse($warning, Response::HTTP_CREATED);
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function storeRules(): array
    {
        return [
            'title' => ['required', 'string', 'min:2', 'max:255'],
            'description' => ['nullable', 'string'],
            'user_id' => ['required', 'exists:users,id'],
        ];
    }

    public function update(Request $request): JsonResponse
    {
        try {
            $warningId = $request->input("id");
            $warning = Warning::find($warningId);
            $attributes['title'] = $request->input("title");
            $attributes['description'] = $request->input("description");
            $attributes["user_id"] = $request->input("user_id");
            $warning->update($attributes);
            return $this->successResponse($warning);
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function updateRules(Request $request)
    {
        $rules = [
            "id" => ['required', 'exists:warnings,id'],
            'title' => ['required', 'string', 'min:2', 'max:255'],
            'description' => ['nullable', 'string'],
            'user_id' => ['required', 'exists:users,id'],
        ];

        $request->validate($rules);
    }


    public function show($id): JsonResponse
    {
        $warning = Warning::find($id);
        if ($warning) {
            return $this->successResponse($warning);
        }
        return $this->errorResponse("Not Found");
    }


    public function destroy(array $ids): JsonResponse
    {
        try {
            $warnings = Warning::withTrashed()->findMany($ids);
            $warnings->each(function ($query) {
                $query->trashed ? $query->foreceDelete() : $query->delete();
            });
            return $this->successResponse($warnings->pluck('id'));
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }
}
