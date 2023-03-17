<?php

use App\Http\Controllers\UserNotificationController;
use Illuminate\Support\Facades\Route;

Route::get('notifications', [UserNotificationController::class, 'index']);
Route::post('notifications/seen', [UserNotificationController::class, 'seen']);
