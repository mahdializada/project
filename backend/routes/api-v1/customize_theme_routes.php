<?php


use App\Http\Controllers\CustomizeThemeController;
use Illuminate\Support\Facades\Route;

Route::get('customize-theme/custome-theme', [CustomizeThemeController::class, "store"]);
