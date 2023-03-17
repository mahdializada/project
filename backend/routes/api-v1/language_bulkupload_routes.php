<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LanguageBulkuploadController;

Route::apiResource('language-bulk-upload', LanguageBulkuploadController::class);