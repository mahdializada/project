<?php


use App\Http\Controllers\DesignRequestController;
use App\Http\Controllers\DesignRequestOrderController;
use App\Models\DesignRequestOrder;
use Illuminate\Support\Facades\Route;

Route::apiResource('order_requests', DesignRequestOrderController::class);
Route::post("order_requests_search", [DesignRequestOrderController::class, "searchDesignRequestOrder"]);
Route::post("updateTaskPrograss", [DesignRequestOrderController::class, "updatePrograss"]);
// get only files
Route::get('order_request_files/{id}', [DesignRequestOrderController::class, "getDesignRequestFiles"]);
