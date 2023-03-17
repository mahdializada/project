<?php


use App\Http\Controllers\LabelCategoryController;
use Illuminate\Support\Facades\Route;

Route::apiResource("labels_category", LabelCategoryController::class);

