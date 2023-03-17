<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;

Route::get('export-role-template', [RoleController::class, "exportRoleTemplate"]);
Route::apiResource('roles', RoleController::class);
Route::post("roles/update/{id}", [RoleController::class, "update"]);
Route::post("roles/check-uniqueness", [RoleController::class, "checkUniqueness"]);
Route::post("roles/change-status", [RoleController::class, "changeRoleStatus"]);
Route::post('roles/searchRole', [RoleController::class, "searchRole"]);
