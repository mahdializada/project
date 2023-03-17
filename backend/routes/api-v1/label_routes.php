<?php


use App\Http\Controllers\LabelController;
use Illuminate\Support\Facades\Route;

Route::apiResource("labels", LabelController::class);
Route::post("label-bulk-upload", [LabelController::class, "bulkUpload"]);
Route::post("labels/change-status", [LabelController::class, "changeStatus"]);
Route::get("labels-with-category", [LabelController::class, "getCategoryLabels"]);

Route::post("labels-assign", [LabelController::class, "assignLabelToItems"]);
Route::get("get-label-history", [LabelController::class, "getLabelsHistory"]);
Route::get("get-assigned-label", [LabelController::class, "getAssignedLabelId"]);
