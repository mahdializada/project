<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepartmentBulkUpload;

Route::apiResource('department-bulk-upload', DepartmentBulkUpload::class);