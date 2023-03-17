<?php

use App\Http\Controllers\CrmOrder\OrderController;
use Illuminate\Support\Facades\Route;


Route::apiResource("crm-orders", OrderController::class);
Route::get("orders/fetch-items/{types}/{id}", [OrderController::class, "fetchItems"]);
Route::get("crm/get-products", [OrderController::class, "getProducts"]);
Route::post("orders/get-company-products", [OrderController::class, "getCompanyProducts"]);
