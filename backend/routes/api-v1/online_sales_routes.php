<?php

use App\Http\Controllers\OnlineSalesManagement\OnlineSalesController;
use Illuminate\Support\Facades\Route;

Route::apiResource('online-sales', OnlineSalesController::class);
Route::post('online-sales/store-note', [OnlineSalesController::class, 'storeNote']);
Route::delete('online-sales/delete-note/{id}', [OnlineSalesController::class, 'deleteNote']);
Route::get('online-sales/fetch-note/{product_code}', [OnlineSalesController::class, 'fetchNote']);
Route::get('online-sales/fetch-history-status/{item_code}', [OnlineSalesController::class, 'fetchHistoryStatus']);
Route::get('online-sales/increment-item/{item_code}', [OnlineSalesController::class, 'incrementItem']);
Route::get('online-sales/check-item-unique/{item_code}', [OnlineSalesController::class, 'checkItemUnique']);
// Get last item code
Route::get('last-item-code', [OnlineSalesController::class, 'lastItemCode']);
Route::get('online-sales/filter/{type}', [OnlineSalesController::class, 'fetchFilterItems']);
Route::post('online-sales/change-status', [OnlineSalesController::class, 'changeStatus']);
Route::post('online-sales/item-status', [OnlineSalesController::class, 'changeItemStatus']);

// get Online store info for profile
Route::post('online-sales/profile-graph', [OnlineSalesController::class, 'getOnlineSalesProfileInfo']);
Route::delete('online-sales-product', [OnlineSalesController::class, 'deleteProduct']);
