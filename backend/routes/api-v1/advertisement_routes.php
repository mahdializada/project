<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Advertisement\ProjectController;
use App\Http\Controllers\Advertisement\PlatformController;
use App\Http\Controllers\Advertisement\ConnectionController;
use App\Http\Controllers\Advertisement\ApplicationController;
use App\Http\Controllers\Advertisement\PlatformNameController;
use App\Http\Controllers\Advertisement\AdvertisementController;
use App\Http\Controllers\Advertisement\AdvertisementGraphController;
use App\Http\Controllers\Advertisement\AdvertisementHistoryController;
use App\Http\Controllers\Advertisement\ConnectionTemplateController;
use App\Http\Controllers\Advertisement\CurrencyController;
use App\Http\Controllers\AdvetisementUpload\AdvertisementMUController;
use App\Http\Controllers\Advertisement\AdAccountBalanceController;
use App\Http\Controllers\Advertisement\BankAccountController;
use App\Http\Controllers\Advertisement\ProductProfileInfoController;
use App\Http\Controllers\ContentLibrary\ContentLibraryController;
use App\Http\Controllers\WhatsappMessageController;

Route::group(['prefix' => 'advertisement'], function () {
    //1. manual excel upload
    Route::post("mu-connection-data", [AdvertisementMUController::class, "storeMUHistoryData"]);
    Route::get("validate-ad-ids", [AdvertisementController::class, "validateAdIds"]);
    Route::get("export-connection-template/{id}", [AdvertisementController::class, "exportConnectionTemplate"]);
    Route::delete("delete-ads", [AdvertisementController::class, "deleteInActiveAds"]);
    // Route::get("export-ad-template", [AdvertisementController::class, "exportConnectionTemplate"]);

    Route::post("manual/data/{section}", [AdvertisementMUController::class, "fetchItems"]);
    Route::post("manual/counts", [AdvertisementMUController::class, "getCounts"]);

    Route::get("validate-excel-rows/{id}", [AdvertisementController::class, "validateExcelRows"]);
    Route::get("application-accounts/{id}", [AdvertisementController::class, "getAppAdAccounts"]);
    Route::put("change-status", [AdvertisementController::class, "changeStatus"]);
    Route::apiResource("projects", ProjectController::class);
    Route::get("get-projects", [ProjectController::class,'getProjects']);
    Route::apiResource("platforms", PlatformController::class);
    Route::post('applications/users', [ApplicationController::class, 'assignUsers']);
    Route::get('applications/users', [ApplicationController::class, 'getUsers']);
    Route::apiResource("applications", ApplicationController::class);
    Route::post("applications/{application_id}/{redirect}", [ApplicationController::class, "reAuthentication"]);
    Route::get("platform_names", [PlatformNameController::class, "index"]);
    Route::get("connection/refresh", [ConnectionController::class, "refreshAds"]);
    Route::get("connection/refresh-crm", [ConnectionController::class, "refreshCrm"]);
    Route::get("connection/generate_link/{types}/{id}", [ConnectionController::class, "fetchItems"]);
    Route::get("connection/generate_link/item_code/pcode/code", [ConnectionController::class, "item_code"]);
    Route::post("connection/generate_link", [ConnectionController::class, "store"]);
    Route::put("connection/update", [ConnectionController::class, "update"]);
    Route::get("ad-accounts/po/{id}", [ConnectionController::class, "checkAccountPo"]);
    Route::post("data/{section}", [AdvertisementController::class, "fetchItems"]);
    Route::post("graph/{graph_tab}", [AdvertisementGraphController::class, "fetchGraphData"]);
    Route::post("counts", [AdvertisementController::class, "getCounts"]);
    Route::get("filter/{filter_type}", [AdvertisementController::class, "fetchFilterItems"]);
    Route::get('check-connection', [AdvertisementController::class, 'checkConnection']);
    Route::get('templates/{section}', [ConnectionTemplateController::class, "fetchItems"]);
    Route::apiResource('templates', ConnectionTemplateController::class);
    Route::post('budget-history', [AdvertisementHistoryController::class, "budgetHistory"]);
    Route::post('budget-history/details', [AdvertisementHistoryController::class, "budgetHistoryDetails"]);
    Route::post('campaign-bids-history', [AdvertisementController::class, "fetchCampaignsBidsHistory"]);
    Route::post('get-bids-history', [AdvertisementController::class, "fetchCountryCompanyBidHistory"]);
    Route::apiResource('currency', CurrencyController::class);
    Route::get('currencies', [CurrencyController::class, "getAllCurrencies"]);
    Route::post('currencies/change-status', [CurrencyController::class, "changeStatus"]);
    Route::get('currencies', [CurrencyController::class, "getAllCurrencies"]);
    Route::apiResource('balance', AdAccountBalanceController::class);
    Route::post("connection/refresh-platform", [ConnectionController::class, "refreshSpecificPlatformAds"]);
    Route::post('product-status-history', [ConnectionController::class, "productHistory"]);
    Route::apiResource('bank-account', BankAccountController::class);
    Route::post('connection-first-date', [ConnectionController::class, "getFirstDate"]);
    Route::post('update-adset-bid-budget', [AdvertisementController::class, "updateBidBudget"]);

    Route::get('connection/inactive-ads', [ConnectionController::class, 'updateInActiveAds']);
    Route::post('cheking-ads', [AdvertisementController::class, "checkingAds"]);
    Route::get('checking-ad-id-template', [AdvertisementController::class, "downloadCheckingAdId"]);
    Route::get("last-refresh", [AdvertisementController::class, "getLastRefresh"]);
    Route::post('application-subcribtion/{id}', [ApplicationController::class, 'toggleSubcribtion']);
    Route::get('application-ad-accounts/{id}', [ApplicationController::class, 'getRelatedAdAccounts']);
    Route::post("insert-inactive-ads", [ConnectionController::class, "insertInactiveAds"]);
    Route::post("get-missed-ads", [ConnectionController::class, "getMissedAdsByDate"]);
    //Route::post("connection/refresh-platform", [ConnectionController::class, "getMissedAdsByDate"]);

    ///////////////////////////////////////// chart controller
    Route::post('graph-profile/{graph_section}', [AdvertisementGraphController::class, 'fetchGraphProfileData']);
    Route::post('graph-profile-statistics', [AdvertisementGraphController::class, 'fetchGraphProfileStatistics']);
    Route::post("ad-insides", [AdvertisementGraphController::class, "fetchAdInsides"]);
    Route::post('canva-statistics', [AdvertisementGraphController::class, 'canvaComparing']);
    ////////////////////////////////////end chart controller


    //product profile
    Route::get('product-profile-info', [ProductProfileInfoController::class, 'getProductInfo']);
    Route::post('product-profile-info', [ProductProfileInfoController::class, 'store']);
    Route::put('product-profile-info/{id}', [ProductProfileInfoController::class, 'updateProductProperty']);
    Route::post('profile-info', [ProductProfileInfoController::class, 'updateProductImage']);
    Route::get('product-profile-history', [ProductProfileInfoController::class, 'getProductHistory']);
    Route::get('product-profile-history-chart', [ProductProfileInfoController::class, 'getProductGraphInfo']);
    Route::post("connection/delete_generated_links", [ConnectionController::class, "destroy"]);
    Route::get("get-connection/{id}", [ConnectionController::class, "show"]);


    // get adset targeting info
    Route::get("adset-targeting/{id}", [AdvertisementController::class, "getAdsetTargeting"]);
    Route::get("adset-bid-budget-history", [AdvertisementController::class, "getBidBudgetHistory"]);
    // delete itemcode
    Route::delete("delete-itemcode", [AdvertisementController::class, "deleteItemCode"]);




    Route::post('item-status', [AdvertisementController::class, 'changeItemStatus']);
});
