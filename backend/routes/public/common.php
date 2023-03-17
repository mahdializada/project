<?php

use App\Http\Controllers\Advertisement\ConnectionController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'common'], function () {
    Route::get("connections/{connection_code}", [ConnectionController::class, "checkForConnectionCode"]);
});
