<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SocialMediaController;

Route::apiResource('social_media', SocialMediaController::class);
Route::post("social_media/change-status", [SocialMediaController::class, "changeSocialMediaStatus"]);
Route::post('social_media/searchSocialMedia', [SocialMediaController::class, "searchSocialMedia"]);
