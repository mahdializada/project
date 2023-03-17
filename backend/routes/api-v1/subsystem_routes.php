<?php

use App\Http\Controllers\SubSystemController;
use Illuminate\Support\Facades\Route;

Route::apiResource("subsystems", SubSystemController::class);


