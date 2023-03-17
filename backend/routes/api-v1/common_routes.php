<?php

use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\Route;

Route::get('common/languages', [CommonController::class, 'fetchLanguages']);
Route::get('data', [CommonController::class, "index"]);
Route::post('change-auth-user-companies', [CommonController::class, "changeAuthUserCompanies"]);
Route::get('export-reason-template', [CommonController::class, "exportTemplate"]);
Route::get('auth-companies', [CommonController::class, "authCompanies"]);
Route::get('common/roles', [CommonController::class, 'fetchRoles']);
Route::get('common/countries', [CommonController::class, 'fetchCountries']);
Route::get('common/users', [CommonController::class, 'fetchUsers']);
Route::get('common/team/{department_ids}', [CommonController::class, 'fetchDepartmentTeam']);
Route::get('common/companies', [CommonController::class, 'fetchCompanies']);

// Route::get('departments', [CommonController::class, 'fetchDepartments'])
