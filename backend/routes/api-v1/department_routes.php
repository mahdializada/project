<?php


use App\Http\Controllers\DepartmentController;
use Illuminate\Support\Facades\Route;

Route::post('departments/check-uniqueness', [DepartmentController::class, "checkUniqueness"]);
Route::get('export-department-template', [DepartmentController::class, "exportDepartmentTemplate"]);
Route::apiResource("departments", DepartmentController::class);
Route::post("departments/change-record-status/{id}", [DepartmentController::class, "changeRecordStatus"]);
Route::post("departments/search-department", [DepartmentController::class, "searchDepartment"]);
Route::post("departments/change-status", [DepartmentController::class, "changeDepartmentStatus"]);


