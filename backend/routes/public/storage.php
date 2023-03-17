<?php

use App\Http\Controllers\LibraryFileController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'storage'], function () {
    Route::get("files/preview/{id}", [LibraryFileController::class, "generateLink"]);
});
