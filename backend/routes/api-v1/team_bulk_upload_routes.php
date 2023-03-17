<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeamBulkUpload;

Route::apiResource('team-bulk-upload', TeamBulkUpload::class);
