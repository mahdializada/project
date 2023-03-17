<?php
use App\Http\Controllers\StatusEventController;
use Illuminate\Support\Facades\Route;


Route::apiResource("status_events", StatusEventController::class);
Route::post("status_events_search", [StatusEventController::class,'search']);


