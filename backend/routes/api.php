<?php

use App\Http\Controllers\Advertisement\AdvertisementController;
use App\Http\Controllers\Advertisement\ColumnCategoryController;
use App\Http\Controllers\Advertisement\ConnectionController;
use App\Http\Controllers\Advertisement\ProductProfileInfoController;
use App\Http\Controllers\Advertisement\TikTokInstantPageController;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CloudinaryFileController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\WhatsappMessageController;
use App\Models\Advertisement\ProductProfileInfo;
use Illuminate\Http\Request;





route::post('instant-order/{account_name}', [TikTokInstantPageController::class, 'instantOrders']);

route::get('webhook', [TikTokInstantPageController::class, 'getTiktokOrder']);
route::get('countries', function (Request $request) {
    $request->header('Authorization', "Bearer ");
    $request->header('Authorization', 'ddd');
    $token = $request->header('Authorization');
    return $token;
    $countries = ['Afghanistan', "pakista"];
    return json_encode([['name' => "afghanistan", 'cities' => ["Kabul", "Kandahar"]]]);
});

Route::prefix('v1/')->group(function () {
    Route::post("forgot-password", [ForgotPasswordController::class, "sendPasswordResetToken"]);
    Route::post('reset-password', [ForgotPasswordController::class, 'resetPassword']);
    Route::get('get_location', [LogController::class, 'locationTracker']);

    Route::prefix("public/")->group(function () {
        include __DIR__ . '/public/storage.php';
        include __DIR__ . '/public/common.php';
    });
    // Login Logout Routes
    include __DIR__ . '/api-v1/login_logout_routes.php';
    // 'dailylogs:users', 'auth:sanctum'
    Route::middleware(['auth:api', "isUserInTimestampRange"])->group(function () {

        Route::post('upload-to-cloud', [CloudinaryFileController::class, 'upload']);
        Route::delete('delete-upload-to-cloud', [CloudinaryFileController::class, 'destroy']);
        // send otp code
        Route::post("otp-code/{sub_system}", [WhatsappMessageController::class, "createOtpCode"]);

        // new landing routes
        include __DIR__ . '/api-v1/new_landing_routes.php';
        // business location Bulk upload Routes
        include __DIR__ . '/api-v1/business_locations_bulkupload_routes.php';

        // common routes
        include __DIR__ . "/api-v1/common_routes.php";

        // subsystem Route Routes
        include __DIR__ . '/api-v1/subsystem_routes.php';

        // Language Bulk upload Routes
        include __DIR__ . '/api-v1/language_bulkupload_routes.php';

        // Role Bulk upload Routes
        include __DIR__ . '/api-v1/role_bulk_upload_routes.php';

        // Team Bulk upload Routes
        include __DIR__ . '/api-v1/team_bulk_upload_routes.php';

        // Department Bulk upload Routes
        include __DIR__ . '/api-v1/department_bulk_uploaded_routes.php';

        // Company Bulk upload Routes
        include __DIR__ . '/api-v1/company_bulk_upload_routes.php';

        // User Bulk upload Routes
        include __DIR__ . '/api-v1/user_bulk_upload_routes.php';

        // User Routes
        include __DIR__ . '/api-v1/user_routes.php';

        // Label Routes
        include __DIR__ . '/api-v1/label_routes.php';

        include __DIR__ . '/api-v1/label_category_routes.php';

        include __DIR__ . '/api-v1/social_media_routes.php';

        include __DIR__ . '/api-v1/design_request_routes.php';

        // order designRequest Routes
        include __DIR__ . '/api-v1/order_design_request_routes.php';

        include __DIR__ . '/api-v1/file_management_routes.php';

        // Department Routes
        //  'dailylogs:departments'
        Route::middleware([])->group(function () {
            include __DIR__ . '/api-v1/department_routes.php';
        });

        // companies Routes
        include __DIR__ . '/api-v1/company_routes.php';

        include __DIR__ . '/api-v1/supplier_routes.php';



        // National Routes
        include __DIR__ . '/api-v1/country_routes.php';


        //column setting Routes
        include __DIR__ . '/api-v1/column_setting_routes.php';

        //Log Routes
        include __DIR__ . '/api-v1/log_routes.php';

        // Reason Routes
        include __DIR__ . '/api-v1/reason_routes.php';

        // Status Events Routes
        include __DIR__ . '/api-v1/status_event_routes.php';

        // System Routes
        include __DIR__ . '/api-v1/system_routes.php';

        // //countries Routes
        include __DIR__ . '/api-v1/country_routes.php';

        // //charts Routes
        include __DIR__ . '/api-v1/users_chart_routes.php';

        // teams Routes
        include __DIR__ . '/api-v1/team_routes.php';

        // roles Routes
        include __DIR__ . '/api-v1/role_routes.php';

        include __DIR__ . '/api-v1/translated_languages.php';

        include __DIR__ . '/api-v1/phrase_routes.php';

        include __DIR__ . '/api-v1/translations_route.php';

        // MASTER MANAGEMENT SYSTEM ROUTES

        // business location routes
        include __DIR__ . "/api-v1/business_location_routes.php";

        include __DIR__ . "/api-v1/industry_routes.php";

        include __DIR__ . "/api-v1/landing_routes.php";

        include __DIR__ . "/api-v1/customize_theme_routes.php";

        include __DIR__ . "/api-v1/notifications_route.php";


        // Singles Sales Product Management System Routes
        include __DIR__ . "/api-v1/single_sales_routes.php";


        // Advertisement routes
        include __DIR__ . "/api-v1/advertisement_routes.php";
        include __DIR__ . "/api-v1/advertisement_history_routes.php";
        include __DIR__ . "/api-v1/remarks_routes.php";

        include __DIR__ . "/api-v1/product_management_routes.php";
        include __DIR__ . "/api-v1/crm_order_routes.php";
        include __DIR__ . "/api-v1/content_library_routes.php";

        // Online store routes
        include __DIR__ . "/api-v1/online_sales_routes.php";

        // Sourcer routes
        include __DIR__ . "/api-v1/sourcer_routes.php";
    });
});


Route::fallback(function () {
    return response()->json("There is no route", Response::HTTP_NOT_FOUND);
});
