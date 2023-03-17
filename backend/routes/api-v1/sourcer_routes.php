<?php

use App\Http\Controllers\SourcerController;
use Illuminate\Support\Facades\Route;


Route::apiResource('sourcers', SourcerController::class);
Route::get("sourcer-country", [SourcerController::class, "getSourcerCountry"]);
Route::post("sourcers/change-status", [SourcerController::class, "changeSourcersStatus"]);
Route::post("sourcers/search", [SourcerController::class, "searchSourcer"]);
Route::get('get-company-country-in-sourcer',[SourcerController::class,'getCompanyAndCountry']);

