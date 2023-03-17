<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyBulkUpload;

Route::apiResource('company-bulk-upload', CompanyBulkUpload::class);