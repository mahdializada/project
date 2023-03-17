<?php

use App\Http\Controllers\DesignRequestChartController;
use App\Http\Controllers\DesignRequestController;
use Illuminate\Support\Facades\Route;

Route::apiResource('design_requests', DesignRequestController::class);
Route::put('design-requests/work-area/{id}', [DesignRequestController::class, 'updateWorkArea']);
Route::get('design-requests-chart', [DesignRequestChartController::class, 'index']);
Route::post('designRequests/reasonReject', [DesignRequestController::class, "resonReject"]);
Route::post('designRequests/autoReview', [DesignRequestController::class, "autoReview"]);
Route::post("design-requests/search", [DesignRequestController::class, "searchDesignRequest"]);
