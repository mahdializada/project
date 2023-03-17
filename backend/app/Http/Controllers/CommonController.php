<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use App\Models\State;
use App\Models\Company;
use App\Models\Country;
use App\Models\Language;
use Illuminate\Http\Request;
use App\Exports\ReasonExport;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;

class CommonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $allowedTables = [
            "countries",
            "languages",
            "social_media",
            "industries",
            "systems",
        ];
        if ($request->query->has("table")) {
            try {
                $tables = explode(",", $request->query->get("table"));
                $result = [];
                foreach ($tables as $table) {
                    if (in_array($table, $allowedTables)) {
                        $data = DB::table($table)->get();
                        $result[$table] = $data;
                    }
                }
                return response()->json($result, 200);
            } catch (\Throwable $th) {
                return response()->json($th->getMessage(), 500);
            }
        }

        return response()->json("Please add your table name", 400);
    }

    public function exportTemplate(): JsonResponse
    {
        Excel::store(
            new ReasonExport(),
            'export-excel-files/reasons.xlsx',
            'public'
        );
        return response()->json(env("APP_URL") . Storage::url('export-excel-files/reasons.xlsx'));
    }


    public function authCompanies(Request $request)
    {
        if ($request->query->getBoolean("has-landing")) {
            $relations = [
                "companies" => function ($query) {
                    $query->whereHas('systems', function ($query) {
                        $query->where("name", "like", "%" . "Content Mangement System" .  "%");
                    })->select(['companies.id', "name", 'status', "logo"]);
                },
            ];
            $authCompanies = $request->user()->load($relations)->getRelation("companies");
            return response()->json($authCompanies);
        }
        $authCompanies = $request->user()->load("companies:id,name,status,logo");
        return response()->json($authCompanies->getRelation("companies"));
    }



    public function changeAuthUserCompanies(Request $request)
    {
        $user = $request->user();
        $selectedCompanies = json_encode($request->input("companies"));
        $user->selected_companies = $selectedCompanies;
        $user->save();
        return response()->json(["message" => "Successfully chanaged user company", "status" => true]);
    }


    public function fetchUsers(Request $request)
    {
        $columns = ["id", 'firstname', 'lastname'];
        $query = User::where("status", "active");
        if ($request->filter == "storage_request") {
            $query = $query->whereHas("requestStorage");
        }
        $users = $query->select($columns)->get();
        return response()->json(["result" => true, "users" => $users]);
    }

    public function fetchLanguages(Request $request)
    {
        $columns = ["id", "name", "native"];
        $languages = Language::select($columns)->get();
        return response()->json(["result" => true, "languages" => $languages]);
    }

    public function fetchRoles(Request $request)
    {
        $columns = ["id", "name",  "code"];

        $roles = Role::where("status", "active")->select($columns)->get();
        return response()->json(["result" => true, "roles" => $roles]);
    }
    public function fetchCountries(Request $request)
    {
        $columns = ["id", "name", "iso2"];

        $countries = Country::whereStatus("active")->select($columns)->get();
        return response()->json(["result" => true, "countries" => $countries]);
    }
    public function fetchCompanies()
    {
        $columns = ["id", "name",  "logo"];

        $companies = Company::where("status", "active")->select($columns)->get();
        return response()->json(["result" => true, "companies" => $companies]);
    }

    public function fetchDepartmentTeam($departmentIds): JsonResponse
    {
        $departmentIds = explode(",", $departmentIds);
        if (count($departmentIds) < 1) {
            return response()->json(["error" => true]);
        }
        $companySystems = Team::query()->whereHas("departments", function (Builder $query) use ($departmentIds) {
            $query->whereIn("department_id", $departmentIds);
        })->where("status", "active")->select("id", "code",  "name")->get();

        return response()->json($companySystems);
    }
}
