<?php

use App\Http\Controllers\LanguagePhraseController;
use Illuminate\Support\Facades\Route;

Route::get('export-language-template', [LanguagePhraseController::class, 'exportLanguageTemplate']);
Route::get("translations/for_system", [LanguagePhraseController::class, 'forSystem'])->withoutMiddleware(['auth:api', "isUserInTimestampRange"]);
Route::get("translations/is_uptodate", [LanguagePhraseController::class, 'isUptoDate'])->withoutMiddleware(['auth:api', "isUserInTimestampRange"]);
Route::apiResource("translations", LanguagePhraseController::class);
