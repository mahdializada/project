<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\UserNotesController;
use App\Http\Controllers\WarningController;
use Illuminate\Support\Facades\Route;

Route::post('users/check-uniqueness-of-mulitple-columns', [UserController::class, "checkUniquenesOfCulumnsWithIndex"]);
Route::get('export-user-template', [UserController::class, "exportUserTemplate"]);
Route::get('users/details/{userId}', [UserController::class, 'getUserDetails']);
Route::post("users/change/profile", [UserController::class, "changeUserProfile"]);
Route::post('users/check-uniqueness', [UserController::class, "checkUniqueness"]);
Route::apiResource('warnings', WarningController::class);
Route::apiResource('users', UserController::class);
Route::post('users/searchUser', [UserController::class, "searchUser"]);
Route::apiResource('notes', UserNotesController::class);
Route::post("users/change-status", [UserController::class, "changeUserStatus"]);
Route::post("users/changeLanguage", [UserController::class, "changeLanguage"]);

// Route::get('online/users', [UserLastseenController::class, "userOnlineStatus"]);
Route::get('all/users', [UserController::class, "allUsers"]);

Route::post('users/updateUserProfilePhoto/{id}', [UserController::class, "updateUserProfilePhoto"]);
Route::post('users/profileUserEdit/{id}', [UserController::class, "profileUserEdit"]);

Route::post("users/changePassword/{id}", [UserController::class, "changePassword"])->name('changePassword');

Route::post("users/update-location", [UserController::class, "changeLoaction"])->name('changeLoaction');
Route::put("auth/update/currency", [UserController::class, "changeAuthCurrency"]);

// Session routes
