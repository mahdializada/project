<?php


namespace App\Repositories;


use Exception;
use App\Models\Department;
use App\Events\Updated;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use App\Repositories\QueryBuilder\UriQueryBuilder;

class DepartmentRepository extends Repository
{

    public function paginate(Request $request): JsonResponse
    {
        if ($request->query->has('key') && $request->get('key') === 'departments-of-logged-in-company') {
            return $this->paginateDepartmentsOfLoggedInCompany($request);
        }
        if ($request->query->has('company_ids')) {
            return $this->getDepartmentsBasedOnCompanyIds($request);
        }
        $key = $request->query->get("key");
        return $this->getDepartmentAccordingToStatus($request, $key);
    }

    function paginateDepartmentsOfLoggedInCompany(Request $request)
    {
        $departments = Department::whereHas('companies', function (Builder $builder) use ($request) {
            $builder->whereHas('users', function (Builder $builder) use ($request) {
                $builder->where('users.id', $request->user()->id);
            });
        })
            ->where('status', 'active')
            ->with('companies:id,name')
            ->get(['id', 'code', 'name']);
        return $this->successResponse($departments, Response::HTTP_OK);
    }

    private function getDepartmentAccordingToStatus($request, $key): JsonResponse
    {
        $queryBuilder = new UriQueryBuilder(new Department(), $request);
        $queryBuilder->setRelations($this->getRelations());
        $queryBuilder->setWithTrashed();

        $searchInColumns    = ['name', 'status', 'updated_at', 'created_at'];
        $queryBuilder->query = $this->filterRecords($queryBuilder->query, $request, $searchInColumns);
        $statusQuery = clone  $queryBuilder->query;
        $queryBuilder->query = $this->fetchDataAccordingToStatus($queryBuilder->query, $key);

        $departmentData = $queryBuilder->build()->paginate()->getData();
        $extraData = [
            'allTotal',
            'activeTotal, status, active',
            'inactiveTotal, status, inactive',
            'blockedTotal, status, blocked',
            'pendingTotal, status, pending',
            'removedTotal'
        ];
        $departmentData = $this->getStatusCount($statusQuery, $departmentData, $extraData);
        return response()->json($departmentData);
    }

    private function deletedTotalDepartments(): int
    {
        return Department::onlyTrashed()->count();
    }

    // return total number of departments based on status
    private function totalDepartments(): int
    {
        return Department::withoutTrashed()->count();
    }

    public function search($request): JsonResponse
    {
        $queryBuilder = new UriQueryBuilder(new Department(), $request);
        $data = $this->searchCode($queryBuilder->query, $request, $this->getRelations());
        return $data ? response()->json($data, 200) : response()->json(['result' => false], Response::HTTP_NO_CONTENT);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $department = null;
            DB::beginTransaction();
            $data = $request->only([
                "name",
                "business_location_id",
                "note",
            ]);
            $data['code'] = $this->randomDepCode();
            $data['created_by'] = auth()->user()->id; //auth()->user()->id;
            $data['updated_by'] = auth()->user()->id; //auth()->user()->id;
            $department = Department::create($data);
            $companyIds = $request->input('company_ids');
            $industries = $request->input("industries");
            $department->industries()->sync($industries);
            $department->companies()->sync($companyIds);
            DB::commit();
            broadcast(new Updated('department', $department->id, 'created'));
            return $this->successResponse($department, Response::HTTP_CREATED);
        } catch (Exception $exception) {

            DB::rollBack();
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function storeRules(): array
    {
        return [
            'name' => ['required', 'unique:departments,name', 'string', 'min:2', 'max:32'],
            'note' => ['required'],
            'industries' => ['required', "array"],
            'business_location_id' => ['required'],
            'company_ids' => ['required'],
            "company_ids.*" => ["exists:companies,id"],
        ];
    }

    public function update(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $data = $request->only([
                "name",
                "business_location_id",
                "note",
            ]);
            $depId = $request->input("id");
            $department = Department::withTrashed()->find($depId);
            $department->update($data);
            $companyIds = $request->input('company_ids');
            $industries = $request->input("industries");
            $department->industries()->sync($industries);
            $department->companies()->sync($companyIds);
            DB::commit();
            broadcast(new Updated('department', $department->id, 'updated'));
            return $this->successResponse(null, Response::HTTP_OK);
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->getMessage());
        }
    }


    public function updateRules(Request $request)
    {
        $depId = $request->input("id");
        $rules = [
            "id" => ['required', 'exists:departments,id'],
            'name' => ['required', 'unique:departments,name,' . $depId, 'string', 'min:2', 'max:32'],
            'note' => ['required'],
            'industries' => ['required', "array"],
            'business_location_id' => ['required'],
            'company_ids' => ['required'],
            "company_ids.*" => ["exists:companies,id"],
        ];
        $request->validate($rules);
    }


    public function show($id): JsonResponse
    {
        $department = Department::with($this->getRelations())
            ->withTrashed()->find($id);
        if ($department) {
            return $this->successResponse($department);
        }
        return $this->errorResponse("Not Found");
    }


    private function getRelations(): array
    {

        return [
            'reasons',
            'changedBy:id,firstname,lastname',
            "companies:id,name,code",
            "companies.countries:id,name,iso2",
            "businessLocation:id,name,code",
            "createdBy:id,firstname,lastname",
            "updatedBy:id,firstname,lastname",
            "industries:id,name"
        ];
    }

    public function getDepartmentsBasedOnCompanyIds($request): JsonResponse
    {
        $company_ids = $request->query->get('company_ids');
        $departments = Department::with('companies')->whereHas('companies', function (Builder $query) use ($company_ids) {
            $query->whereIn('company_id', $company_ids);
        })->where("status", "active")->get(['id', 'code', 'name']);
        return \response()->json(['departments' => $departments], Response::HTTP_OK);
    }


    /**
     * Get allowed departments of companies, based on permission and logged in
     */
    public function getAllowedDepartmentsOfCompanies(Request $request)
    {
        $departments = Department::whereHas('companies', function (Builder $builder) use ($request) {
            $builder->whereHas('users', function (Builder $builder) use ($request) {
                $builder->where('users.id', $request->user()->id);
            });
            $builder->whereIn('companies.id', $request->get('company_ids'));
        })
            ->whereStatus('active')
            ->get(['id', 'code', 'name'])
            ->toArray();
        return \response()->json(['departments' => $departments], Response::HTTP_OK);
    }

    private function randomDepCode(): int
    {
        return rand(10000, 8999999999);
    }

    public function changeDepartmentStatus(Request $request)
    {
        return $this->changeStatus($request,  Department::class, 'departments', 'reason_department', 'department_id', $this->getRelations(), 'department');
    }


    public function destroy(array $ids, $reasonId, $request)
    {
        return $this->destroyItems(
            Department::class,
            $ids,
            'reason_department',
            $reasonId,
            'department_id',
            null,
            $this->getRelations(),
            'department',
            $request
        );
    }

    public function checkUniqueness(Request $request): JsonResponse
    {
        $result = [];
        $hasMulti = $request->query->has('multi');
        $multi = $request->query->getBoolean('multi');
        if ($hasMulti && $multi) {
            $result = $this->checkMulitUniqueness($request);
        } else {
            $departmentName = $request->get('name');
            $depId = $request->query->get("id");
            $hasDepId = $request->query->has("id");
            if ($hasDepId) {
                $isExists = Department::where('name', $departmentName)->where("id", "!=", $depId)->exists();
            } else {
                $isExists = Department::where('name', $departmentName)->exists();
            }
            $result['name'] = $isExists;
        }
        return response()->json($result);
    }


    function checkMulitUniqueness($request)
    {
        $result = [];
        $columns = $request->get('names');
        foreach ($columns as $column) {
            $parsedColumn = collect($column);
            $isExists = Department::whereName($parsedColumn->get("value"))->exists();
            $result[] = ['row_name_value' => $parsedColumn->get("value"), 'value' => $isExists, 'index' => $parsedColumn->get('index')];
        }
        unset($result['']);
        return $result;
    }
}
