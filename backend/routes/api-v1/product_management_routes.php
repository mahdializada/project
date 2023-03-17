<?php

use App\Http\Controllers\AttributeController;
use App\Http\Controllers\ProductManagement\BrandController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductManagement\ProductController;
use App\Http\Controllers\ProductManagement\CategoryController;
use App\Http\Controllers\ProductManagement\ProductStudyController;

Route::group(['prefix' => 'product-management'], function () {
    Route::apiResource("pr-categories", CategoryController::class);
    Route::get("pr-attributes", [AttributeController::class, "index"]);
    Route::get("pr-products/check-sku", [ProductController::class, 'checkSku']);
    Route::post("pr-products/images", [ProductController::class, 'images']);
    Route::apiResource("pr-products", ProductController::class);
    Route::apiResource("pr-product-study", ProductStudyController::class);

    //Brand Routes
    Route::apiResource("pr-brand", BrandController::class);
    Route::post("pr-brand/search", [BrandController::class, "searchItem"]);
    Route::post("pr-brand/change-status", [BrandController::class, "changeBrandStatus"]);
});
