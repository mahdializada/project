<?php

use App\Models\User;
use App\Models\Notification;
use App\Events\SendNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Carbon;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {

    //  event(new \App\Events\SendRefreshAdsEvent(["email"=>'test@test.com']));
    return view('welcome');
});

Route::get('/broadcast', function () {
    $user = User::inRandomOrder()->first();
    Auth::loginUsingId($user->id);
    $note = Notification::inRandomOrder()->first();
    broadcast(new SendNotification(User::inRandomOrder()->first()->id, $note->id, ['ahmad', 'dkdkdk'], ['ahmad']));
});
