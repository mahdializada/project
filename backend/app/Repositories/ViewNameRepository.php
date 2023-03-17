<?php


namespace App\Repositories;


use App\Models\Role;
use App\Models\ViewName;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ViewNameRepository extends Repository
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
        $queryBuilder = new UriQueryBuilder(new ViewName(), $request);
        return $queryBuilder->build()->paginate();
    }

    private function paginateTrashed(Request $request): JsonResponse
    {
        $queryBuilder = new UriQueryBuilder(new ViewName(), $request, true);
        return $queryBuilder->build()->paginate();
    }


    public function store(Request $request): JsonResponse
    {
        return "hello world";
        try {
            response()->json(["view_name"=>ViewName::create($request),Response::HTTP_CREATED]);
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function storeRules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2',],
            'columns' => ['required'],
            'user_id' => ['required| min:1'],
        ];
    }

    public function update(Request $request): JsonResponse
    {
        try {

        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function updateRules(Request $request)
    {
        $roleId = $request->input("id");
        $rules = [
            "id" => ['required', 'exists:roles,id'],
            'name' => ['required', 'unique:roles,name,' . $roleId, 'string', 'min:2', 'max:32'],
        ];
        $request->validate($rules);
    }


    public function destroy(array $ids): JsonResponse
    {
        try {
            $roles = Role::withTrashed()->findMany($ids);
            $roles->each(function ($query) {
                $query->trashed ? $query->foreceDelete() : $query->delete();
            });
            return $this->successResponse($roles->pluck('id'));
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }
}
