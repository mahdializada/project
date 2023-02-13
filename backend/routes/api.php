<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\departmentController;
use App\Http\Controllers\SocialMediaController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\ColumnController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['prefix'=>'auth'], function(){
    Route::post('/login',[AuthController::class,'login']);
    Route::post('/logout',[AuthController::class,'logout'])->middleware('auth:sanctum');
    Route::get('/user',[AuthController::class,'user'])->middleware('auth:sanctum');
});

// Company Route
Route::apiResource('company',CompanyController::class);

// user route
Route::apiResource('user',UserController::class);
Route::apiResource('socialmedia',SocialMediaController::class);

// Country Route
Route::apiResource('country',CountryController::class);

// System Route
Route::apiResource('system',SystemController::class);



// Department routes
Route::apiResource('department',departmentController::class);

// Socialmedia routes
Route::apiResource('socialmedia',SocialMediaController::class);

// Team Route
Route::apiResource('team',TeamController::class);
// column Route
Route::apiResource('column',ColumnController::class);
