<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SystemFileController;
use App\Http\Controllers\FileCommentController;
use App\Http\Controllers\LibraryFileController;
use App\Http\Controllers\PersonalFileController;
use App\Http\Controllers\PersonalFileTrashController;
use App\Http\Controllers\PersonalFileFavoriteController;
use App\Http\Controllers\PersonalFileMyShareController;
use App\Http\Controllers\PersonalFileShareController;
use App\Http\Controllers\PersonalFileRecentController;
use App\Http\Controllers\RequestStorageController;


// personal file recent routes
Route::get("files/personal/settings", [PersonalFileController::class, 'settingsPersonalFiles']);
Route::post("files/personal/setSettings", [PersonalFileController::class, 'defaultSettingsPersonal']);
Route::post("files/storage/personal/update", [PersonalFileController::class, 'updateUserStorage']);

Route::get('files/personal/recent', [PersonalFileRecentController::class, 'index']);
Route::get('files/personal/recent/{id}', [PersonalFileRecentController::class, 'show']);


Route::apiResource("storage-requests", RequestStorageController::class);
Route::post("storage-requests/change-status", [RequestStorageController::class, "changeRequestStorageStatus"]);
Route::post('storage-requests/search', [RequestStorageController::class, "searchItem"]);


// Favorites Page Routes
Route::get('files/personal/favorites', [PersonalFileFavoriteController::class, "index"]);
Route::get('files/personal/favorites/{id}', [PersonalFileFavoriteController::class, "show"]);

Route::apiResource('files/personal/shared', PersonalFileShareController::class);
Route::apiResource('files/personal/my_shares', PersonalFileMyShareController::class);
Route::apiResource('files/personal/trash', PersonalFileTrashController::class);
Route::apiResource('files/personal', PersonalFileController::class);
Route::get('files/personal/auth/folders', [PersonalFileController::class, 'authFolders']);
Route::post('files/personal/auth/relocation', [PersonalFileController::class, 'copyOrMove']);
Route::get("files/personal/files/search", [PersonalFileController::class, 'searchPersonalFiles']);
Route::post("files/personal/restoreFile", [PersonalFileTrashController::class, 'restoreFileOrFolder']);

// recent files

Route::post("files/personal/files/rename", [PersonalFileController::class, 'renameFile']);
// Route::post("files/personal/files/password", [PersonalFileController::class, 'updateFilePassword']);
Route::post("files/personal/files/download", [PersonalFileController::class, 'downloadFileOrFolder']);
Route::put("files/personal/actions/activities", [PersonalFileController::class, 'toggleActivities']);
Route::post('files/personal/folder/bulkupload', [PersonalFileController::class, "bulkUploadFolder"]);
Route::post("files/personal/folder/action", [PersonalFileController::class, "createOrRenameFolder"]);
Route::post("files/personal/share", [PersonalFileController::class, "share"]);
Route::post("files/personal/share/delete", [PersonalFileShareController::class, "deleteFromShare"]);

// Library Routes
Route::apiResource('files/library', LibraryFileController::class);
Route::post("files/library/files/download", [LibraryFileController::class, 'downloadFileOrFolder']);
Route::put("files/library/actions/activities", [LibraryFileController::class, 'toggleActivities']);
Route::post("files/library/files/rename", [LibraryFileController::class, 'renameFile']);
Route::post("files/library/folder/action", [LibraryFileController::class, "createOrRenameFolder"]);



// System Routes
Route::apiResource('system_files', SystemFileController::class);


// Common Routes for (Library, System, Personal Files)

// File Details
Route::get('files/personal/details/{id}', [PersonalFileController::class, 'fileDetails']);
Route::get('files/library/details/{id}', [LibraryFileController::class, 'fileDetails']);

// Comments & Replies
Route::post('files/comment', [FileCommentController::class, 'store']);
Route::delete('files/comment/{id}', [FileCommentController::class, 'destroy']);
Route::post('files/description', [FileCommentController::class, 'updateDescriptions']);
