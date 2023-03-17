<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttributeController;
use \App\Http\Controllers\SingleSales\IPAController;
use App\Http\Controllers\SingleSales\ISPPController;
use App\Http\Controllers\SingleSales\ActionController;
use App\Http\Controllers\SingleSales\ProductController;
use App\Http\Controllers\SingleSales\CategoryController;
use App\Http\Controllers\SingleSales\ProductStudyCategoryController;
use App\Http\Controllers\SingleSales\ProductStudyController;
use App\Http\Controllers\SingleSales\SourcingRequestController;
use App\Http\Controllers\SingleSales\QuantityReservationRequestController;
use App\Http\Controllers\SingleSales\QuestionController;
use App\Http\Controllers\SingleSales\SalesChanelController;
use App\Http\Controllers\SingleSales\ProductSourceController;
use App\Http\Controllers\SingleSales\ProductSourceValueController;
use App\Http\Controllers\SingleSales\ActionClassController;
use App\Http\Controllers\SingleSales\BrandController;
use Illuminate\Http\Request;
use App\Models\TempFile;
use App\Repositories\Files\CloudinaryFileUtils;

Route::group(['prefix' => 'single-sales'], function () {

    route::post('upload', function (Request $request) {
        $path = 'public';
        $uploadedPath = null;
        if ($request->has('path') and $request->get('path')) {
            $path = trim($request->get('path'));
        }
        $name = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $size = $file->getSize();
            $name = $file->getClientOriginalName();
            if ($request->has('cloudinary')) {
                $cloud =  CloudinaryFileUtils::uploadFile($file, 'advertisement');
            } else {
                $cloud = '';
                $uploadedPath = $file->store($path);
            }

            // TempFile::create(['path' => $uploadedPath, 'name' => $name, 'size' => $size]);
        }

        return response()->json(['path' => $uploadedPath, 'cloud' => $cloud]);
    });
    route::delete('deleted/upload', function (Request $request) {
        $path = $request->get('path');
        $q = new TempFile();
        return response()->json(['path' => $request->get('path'), 'result' => $q->deleteTempfile($path)]);
    });

    //sales channel
    route::apiResource('sales-channel', SalesChanelController::class);

    // attributes
    route::apiResource('attribute-ssp', AttributeController::class);
    Route::post('attributes/search', [AttributeController::class, "searchItem"]);
    Route::post('attribute-ssp/change-status', [AttributeController::class, 'changeAttributesStatus']);


    // ipa
    Route::apiResource('ipa', IPAController::class);
    Route::post('ipa/search', [IPAController::class, "searchIpa"]);
    Route::post('ipa/change-status', [IPAController::class, 'changeStatus']);


    // Sourcer
    Route::apiResource("sourcer", SourcingRequestController::class);

    Route::apiResource('quantity/reservation', QuantityReservationRequestController::class);
    Route::post('quantity/reservation/change-status', [QuantityReservationRequestController::class, 'changeStatus']);

    //categories route
    Route::apiResource('categories-ssp', CategoryController::class);
    Route::post('categories/search', [CategoryController::class, "searchItem"]);
    Route::post("categories-ssp/change-status", [CategoryController::class, "changeCategoriesStatus"]);
    Route::post("categoryAttributes", [CategoryController::class, "categoryAttributes"]);




    // Product Route
    Route::apiResource("products-ssp", ProductController::class);
    Route::post("product-inventory-ssp", [ProductController::class,'inventoryInsertion']);
    Route::post("products-ssp/search", [ProductController::class, 'search']);
    Route::post("products-ssp/change-status", [ProductController::class, "changeProductsStatus"]);
    Route::get('product-ssp/last-item-code',[ProductController::class,'lastItemCode']);
    Route::get('product-ssp/generateSKU',[ProductController::class,'generateSKU']);
    Route::get("country", [ProductController::class, "getProductCountry"]);


    Route::apiResource('product-study', ProductStudyController::class);
    // Route::post("product-study/change-status", [ProductStudyController::class, "changeStatus"]);
    // Route::post("product-study/assign-user", [ProductStudyController::class, "assignUser"]);

    // product-study-category
    Route::apiResource('product-study-category', ProductStudyCategoryController::class);


    // sourcing req and result routes
    Route::apiResource('sourcing/request', SourcingRequestController::class);
    Route::post("sourcing/request/change-status", [SourcingRequestController::class, "changeStatus"]);
    Route::post("sourcing/assign-user", [SourcingRequestController::class, "assignUser"]);
    Route::post("sourcing/search", [SourcingRequestController::class, "searchSourcer"]);

    //ISPP Routes
    Route::apiResource("ispp", ISPPController::class);
    Route::post('ispp/search', [ISPPController::class, "searchItem"]);
    Route::post("ispp/change-status", [ISPPController::class, "changeStatus"]);


    //Actions Routes
    Route::apiResource("action", ActionController::class);
    Route::post('action/search', [ActionController::class, "searchItem"]);
    Route::post('action/change-status', [ActionController::class, "changeActionStatus"]);





    // quantity reservation
    route::apiResource('quantity/reservation', QuantityReservationRequestController::class);

    route::apiResource('question', QuestionController::class);
    Route::apiResource('product-source-value', ProductSourceValueController::class);
    Route::apiResource('action-class', ActionClassController::class);

     //Brand Routes
     Route::apiResource("brand-ssp", BrandController::class);
     Route::post("brand/search", [BrandController::class, "searchItem"]);
     Route::post("brand-ssp/change-status", [BrandController::class, "changeBrandStatus"]);


});
