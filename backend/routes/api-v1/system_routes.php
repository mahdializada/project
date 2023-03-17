<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActionController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\Advertisement\CurrencyController;
use App\Http\Controllers\Advertisement\AdvertisementGraphController;


Route::apiResource("systems", SystemController::class);
Route::post("systems/searchSystem", [SystemController::class, 'searchSystem']);
Route::apiResource("actions", ActionController::class);
Route::post("graph/{graph_tab}", [AdvertisementGraphController::class, "graphActions"])->withoutMiddleware(['auth:api']);
Route::get('systems_sub_systems', [SystemController::class, "getSubSystems"]);
Route::get('paginate-systems', [SystemController::class, "paginate"]);
Route::get('/systems-for-select', [SystemController::class, "c"]);
Route::get('all-system-subsystem', [SystemController::class, "getSystemWithSubsystem"]);
Route::get("currency/list", [CurrencyController::class, "currencyList"]);
