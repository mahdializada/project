<?php

use App\Http\Controllers\LogController;
use Illuminate\Support\Facades\Route;


Route::apiResource("logs", LogController::class);
Route::get("filter_logs",[LogController::class,"filterLogs"]);
