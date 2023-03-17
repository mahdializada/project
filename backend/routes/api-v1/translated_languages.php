<?php


use App\Http\Controllers\TranslatedLanguageController;
use Illuminate\Support\Facades\Route;

Route::apiResource("languages_translated", TranslatedLanguageController::class);
Route::post("languages_translated/change-status", [TranslatedLanguageController::class, "changeLanguageStatus"]);
Route::post("languages_translated/searchLanguage", [TranslatedLanguageController::class, "searchLanguage"]);
