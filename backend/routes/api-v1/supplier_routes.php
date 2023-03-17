<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SourcerController;
use App\Http\Controllers\ProductListController;

Route::post('supplier/check-uniqueness-of-mulitple-columns', [SupplierController::class, 'checkUniquenesOfCulumnsWithIndex']);
Route::get('export-supplier-template', [SupplierController::class, "exportSupplierTemplate"]);
Route::apiResource('suppliers', SupplierController::class);
Route::get('supplier', [SupplierController::class,'list']);
Route::apiResource('sourcers', SourcerController::class);
Route::apiResource('product_list',ProductListController::class);
Route::post('change_product_supplier',[ProductListController::class,'changeProductSupplier']);
Route::post("suppliers/change-status", [SupplierController::class, "changeSupplierStatus"]);
Route::post('suppliers/check-uniqueness', [SupplierController::class, "checkUniqueness"]);
Route::post('suppliers/searchSupplier',  [SupplierController::class, "searchSupplier"]);
Route::get("get-supplier-bank-account/{id}", [SupplierController::class, "getSupplierBankAccount"]);
Route::get('get-locations/{id}',  [SupplierController::class, "getLocations"]);
Route::post('suppliers/store-location',  [SupplierController::class, "storeLocation"]);
Route::get('get-locations/{id}',  [SupplierController::class, "getLocations"]);
Route::put('update-locations',  [SupplierController::class, "updateLocations"]);
Route::post("store-supplier-bank-account", [SupplierController::class, "storeSupplierBankAccount"]);
Route::get("get-supplier-bank-account/{id}", [SupplierController::class, "getSupplierBankAccount"]);
Route::put("update-supplier-bank-account", [SupplierController::class, "updateSupplierBankAccount"]);
Route::get("supplier-country", [SupplierController::class, "getSupplierCountry"]);
Route::post("supplier-images", [ProductController::class, 'images']);


