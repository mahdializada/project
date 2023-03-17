<?php


use App\Http\Controllers\IndustryController;
use Illuminate\Support\Facades\Route;

Route::apiResource('industries', IndustryController::class);

