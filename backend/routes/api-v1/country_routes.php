<?php


use App\Http\Controllers\CountryController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\NationalController;
use App\Http\Controllers\StateController;
use Illuminate\Support\Facades\Route;

Route::apiResource("countries", CountryController::class);
Route::apiResource("languages", LanguageController::class);
Route::get("states", [StateController::class, "index"]);
Route::get("states/country-states/{countryId}", [StateController::class, "countryState"]);
Route::post("countries/change-status", [CountryController::class, "changeCountryStatus"]);
Route::post('countries/searchCountry', [CountryController::class, "searchCountry"]);


