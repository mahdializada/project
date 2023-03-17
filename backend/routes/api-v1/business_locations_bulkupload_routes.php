<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BusinessLocationBulkupload;

Route::apiResource('business-location-bulk-upload', BusinessLocationBulkupload::class);