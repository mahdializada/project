<?php

use Illuminate\Support\Facades\Route;

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
    return view('home');
});

route::get('/home',function(){


	return view('home');
});
Route::view('master', 'layout.master');
Route::view('about-us','about.about-us');
route::view('contact','contact.contact');
route::view('history', 'history.history');
route::view('who_we','who_we_are.who_we');
route::view('couching','couching.couching');
route::view('refereeing','refereeing.refereeing');
route::view('fixture','fixture.fixture');
route::view('result','result.result');
route::view('award','award.award');
route::view('cups','cups.cups');
route::view('friendly','friendly.friendly');
route::view('wallpaper','wallpaper.wallpaper');
route::view('exculusive','exculusive.exculusive');
route::view('accessories','accessories.accessories');
route::view('women','women.women');
route::view('mens','mens.mens');
route::view('store','store.store');
route::view('futsal','futsal.futsal');
route::view('kabul_legure','kabul_legure.kabul_legure');
route::view('partener','partener.partener');
route::view('jobs_team','jobs_team.jobs_team');
route::view('statutes','statutes.statutes');
route::view('tribunal','tribunal.tribunal');
