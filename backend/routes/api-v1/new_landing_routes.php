<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Landing\Product\Master\ProductCategoryController;
use App\Http\Controllers\Landing\Product\Master\SkillCategoryController;
use App\Http\Controllers\Landing\Product\Master\ProductSubCategoryController;
use App\Http\Controllers\Landing\Product\Master\RequirementCategoryController;
use App\Http\Controllers\Landing\Product\Master\RequirementSubCategoryController;
use App\Http\Controllers\Landing\Product\Master\SkillSubCategoryController;
use App\Http\Controllers\Landing\Product\Master\WorkerCategoryController;
use App\Http\Controllers\Landing\Product\Master\WorkerSubCategoryController;
use App\Http\Controllers\Landing\Skill\SkillController;

// ****** Please follow the [hyphened strings] routes ******* //

// skill
Route::apiResource("landing/skills", SkillController::class);
Route::get("landing/search-skill", [SkillController::class, 'search']);
Route::post("landing/skills/change-status", [SkillController::class, 'changeStatus']);

// skill category
Route::apiResource("landing/skill-categories", SkillCategoryController::class);
Route::get("landing/search-category-skill", [SkillCategoryController::class, 'search']);
Route::post("landing/skill-categories/change-status", [SkillCategoryController::class, 'changeStatus']);
Route::get("landing/all-skill-categories", [SkillCategoryController::class, "fetchAllCategories"]);

//skil sub category
Route::get("landing/skill-sub-categories", [SkillSubCategoryController::class, "index"]);
Route::get("landing/search-sub-category-skill", [SkillSubCategoryController::class, 'search']);
Route::post("landing/skill-sub-categories/change-status", [SkillSubCategoryController::class, 'changeStatus']);

// product


//product category
Route::apiResource("landing/product-categories", ProductCategoryController::class);
Route::get("landing/search-category-product", [ProductCategoryController::class, 'search']);
Route::post("landing/product-categories/change-status", [ProductCategoryController::class, 'changeStatus']);
Route::get("landing/all-product-categories", [ProductCategoryController::class, "fetchAllCategories"]);

// product sub category
Route::get("landing/product-sub-categories", [ProductSubCategoryController::class, "index"]);
Route::get("landing/search-sub-category-product", [ProductSubCategoryController::class, 'search']);
Route::post("landing/product-sub-categories/change-status", [ProductSubCategoryController::class, 'changeStatus']);

// requirement


// requirement category
Route::apiResource("landing/requiremnt-categories", RequirementCategoryController::class);
Route::get("landing/search-category-requiremnt", [RequirementCategoryController::class, 'search']);
Route::post("landing/requiremnt-categories/change-status", [RequirementCategoryController::class, 'changeStatus']);
Route::get("landing/all-requirement-categories", [RequirementCategoryController::class, "fetchAllCategories"]);


// requirement sub category
Route::get("landing/requiremnt-sub-categories", [RequirementSubCategoryController::class, "index"]);
Route::get("landing/search-sub-category-requiremnt", [RequirementSubCategoryController::class, 'search']);
Route::post("landing/requiremnt-sub-categories/change-status", [RequirementSubCategoryController::class, 'changeStatus']);


// worker category
Route::apiResource("landing/worker-categories", WorkerCategoryController::class);
Route::get("landing/search-category-worker", [WorkerCategoryController::class, 'search']);
Route::post("landing/worker-categories/change-status", [WorkerCategoryController::class, 'changeStatus']);
Route::get("landing/all-worker-categories", [WorkerCategoryController::class, "fetchAllCategories"]);


// worker sub category
Route::get("landing/worker-sub-categories", [WorkerSubCategoryController::class, "index"]);
Route::get("landing/search-sub-category-worker", [WorkerSubCategoryController::class, 'search']);
Route::post("landing/worker-sub-categories/change-status", [WorkerSubCategoryController::class, 'changeStatus']);
