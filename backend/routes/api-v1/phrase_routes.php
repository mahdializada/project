<?php

use App\Http\Controllers\PhraseController;
use Illuminate\Support\Facades\Route;

Route::apiResource("phrases", PhraseController::class);
