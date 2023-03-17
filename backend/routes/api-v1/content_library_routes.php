<?php

use App\Http\Controllers\ContentLibrary\ContentLibraryController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'library'], function () {
    Route::apiResource("content-library", ContentLibraryController::class);
    Route::post("change-status", [ContentLibraryController::class, 'changeStatus']);
    Route::get("get-content-media", [ContentLibraryController::class, 'getContentMedia']);
    Route::post("get-product-media", [ContentLibraryController::class, 'getProductMedia']);
    Route::post("assign-tags", [ContentLibraryController::class, 'assignTags']);

    Route::get("get-project-url", [ContentLibraryController::class, 'getProjectUrl']);
    Route::get('get-itemcodes', [ContentLibraryController::class, 'getItemCodes']);

    Route::post("add-to-favorite", [ContentLibraryController::class, 'addToFavorite']);
    Route::put("edit-property", [ContentLibraryController::class, 'editProperties']);
});
