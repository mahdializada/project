<?php

use App\Http\Controllers\ReasonBulkupload;
use App\Http\Controllers\ReasonsController;
use Illuminate\Support\Facades\Route;

Route::post("reason-bulk-upload", [ReasonBulkupload::class, "store"]);
Route::apiResource("reasons", ReasonsController::class);
Route::get("reasons_system_event", [ReasonsController::class, "fetchReasonAccordingToSystemEvent"]);
Route::post("searchReason", [ReasonsController::class, "searchReason"]);
Route::post("assign-reason", [ReasonsController::class, "assignReason"]);
Route::post("get-reason-history", [ReasonsController::class, "reasonHistory"]);
Route::get('status-event-reasons', [ReasonsController::class , 'reasonStatusEvent']);
