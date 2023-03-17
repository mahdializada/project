<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserBulkUpload;

Route::apiResource('bulk-upload-user', UserBulkUpload::class);
