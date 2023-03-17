<?php

use App\Http\Controllers\DesignRequestController;
use App\Http\Controllers\DesignRequestHistoryController;
use Illuminate\Support\Facades\Route;


Route::put("design-request-column", [DesignRequestController::class, "updateColumn"]);
Route::get("design-request-data", [DesignRequestController::class, "fetchData"]);
Route::post("design-request/assign-user", [DesignRequestController::class, "assignUsers"]);
Route::post("design-request/change-status", [DesignRequestController::class, "changeStatus"]);
Route::post("design-request/not-assign-user", [DesignRequestController::class, "notAssignUser"]);
Route::apiResource("design-request", DesignRequestController::class);
Route::apiResource("design-request-histories", DesignRequestHistoryController::class);
Route::post("design_request_histories_search", [DesignRequestHistoryController::class,'design_request_histories_search']);
