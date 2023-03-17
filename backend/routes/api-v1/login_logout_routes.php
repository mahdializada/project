<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login'])->withoutMiddleware('auth:api')->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware([
    'auth:api',
    "isUserInTimestampRange"
]);
Route::get('/auth/user', [AuthController::class, 'me'])->name('authenticated user')->middleware(['auth:api', "isUserInTimestampRange"]);
// Route::get('/auth/user', [AuthController::class ,'me'])->name('authenticated user');
