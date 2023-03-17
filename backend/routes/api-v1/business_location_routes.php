<?php


use App\Http\Controllers\BusinessLocationController;
use Illuminate\Support\Facades\Route;

Route::post('business-locations/check-uniqueness-of-mulitple-columns', [BusinessLocationController::class, "checkUniquenesOfCulumnsWithIndex"]);
Route::get('export-business-location-template', [BusinessLocationController::class, "exportBusinessLocationTemplate"]);
Route::get('/business-locations/for-select', [BusinessLocationController::class, "forSelect"]);
Route::apiResource('business-locations', BusinessLocationController::class);
Route::post("business-locations/change-status", [BusinessLocationController::class, "changeStatus"]);
Route::post('business-locations/check-uniqueness', [BusinessLocationController::class, "checkUniqueness"]);
Route::post('business-locations/search',  [BusinessLocationController::class, "search"]);
