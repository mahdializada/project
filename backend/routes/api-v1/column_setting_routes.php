<?php

use App\Http\Controllers\Advertisement\ColumnCategoryController;
use App\Http\Controllers\ViewNameController;
use Illuminate\Support\Facades\Route;

Route::post("view-name-edit", [ViewNameController::class, 'editDefault']);
Route::apiResource("view_name", ViewNameController::class);
Route::post("column_category/search", [ColumnCategoryController::class, "search"]);
Route::apiResource("column_category", ColumnCategoryController::class);
Route::post("column_category/change-status", [ColumnCategoryController::class, 'changeStatus']);

Route::get("all-column-category", [ColumnCategoryController::class, "fetchAllCategory"]);
Route::post("add-column-category", [ColumnCategoryController::class, "addNewCategory"]);
Route::get('sub-system-header', [ViewNameController::class, "fetchHeaders"]);
Route::post("save-column-changes", [ColumnCategoryController::class, "saveColumnChanges"]);

Route::get("subsystem-column-category", [ColumnCategoryController::class, "subsystemColumnsCategory"]);
