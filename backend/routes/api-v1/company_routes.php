<?php


use App\Http\Controllers\CompanyController;
use Illuminate\Support\Facades\Route;

Route::get('export-company-template', [CompanyController::class, "exportCompanyTemplate"]);
Route::post('companies/check-uniqueness', [CompanyController::class, "checkUniqueness"]);
Route::post('companies/check-name-uniqueness-on-crud', [CompanyController::class, "checkNameUniquenessOnCrud"]);
Route::post('companies/check-email-uniqueness-on-crud', [CompanyController::class, "checkEmailUniquenessOnCrud"]);
Route::apiResource('companies', CompanyController::class);
Route::get('companies/restore/{id}',    [CompanyController::class, 'restore']);
Route::post('companies/validate-name',  [CompanyController::class, 'validateCompanyName']);
Route::post('companies/validate-email', [CompanyController::class, 'validateCompanyEmail']);
Route::post("companies/change-status",   [CompanyController::class, "changeCompanyStatus"]);
Route::post("companies/change-status/add-users",   [CompanyController::class, "changeCompanyStatusAndAddUser"]);
Route::post('companies/searchCompany',  [CompanyController::class, "searchCompany"]);
Route::get('paginate-companies',  [CompanyController::class, "paginate"]);
Route::get('connection-companies',  [CompanyController::class, "getCompaniesUsedInConnection"]);
