<?php


use App\Http\Controllers\UsersChartController;
use Illuminate\Support\Facades\Route;

Route::apiResource("users_chart", UsersChartController::class);
Route::get("users_chart_month", [UsersChartController::class,'monthFilter']);
Route::get("users_chart_year",  [UsersChartController::class,'yearFilter']);
Route::get("users_chart_week",  [UsersChartController::class,'weekFilter']);


