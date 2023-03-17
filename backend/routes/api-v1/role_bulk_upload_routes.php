<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleBulkUpload;

Route::apiResource('role-bulk-upload', RoleBulkUpload::class);
