<?php

use App\Http\Controllers\RemarkController;
use Illuminate\Support\Facades\Route;

Route::apiResource('/remarks', RemarkController::class);
Route::post('remark-reactions', [RemarkController::class, 'reactRemarks']);
Route::delete('remark-reactions/{id}', [RemarkController::class, 'deleteRemark']);
