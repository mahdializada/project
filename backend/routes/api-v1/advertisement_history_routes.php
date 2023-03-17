<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Advertisement\AdvertisementHistoryController;

Route::group(['prefix' => 'advertisement'], function () {

    Route::get('budget-history/{id}', [AdvertisementHistoryController::class, "budgetHistory"]);
    Route::post('campaign-bids-history', [AdvertisementHistoryController::class, "fetchCampaignsBidsHistory"]);
    Route::post('get-bids-history', [AdvertisementHistoryController::class, "fetchBidHistory"]);
    // advertisement/tab-count
    Route::get('budget-history-tab-count', [AdvertisementHistoryController::class, "tabCount"]);
});
