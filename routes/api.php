<?php

use App\Action\HelperAction;
use App\Http\Controllers\Api\v1\Admin\AdminHomeController;
use App\Http\Controllers\Api\v1\Admin\CategoryController;
use App\Http\Controllers\Api\v1\Admin\DistrictController;
use App\Http\Controllers\Api\v1\Admin\Product\ProductController;
use App\Http\Controllers\Api\v1\Admin\SubCategoryController;
use App\Http\Controllers\Api\v1\Admin\SubDistrictController;
use App\Http\Controllers\Api\v1\Auth\AuthController;
use App\Http\Controllers\Api\v1\MessageController;
use App\Http\Controllers\Api\v1\Payment\BkashController;
use App\Http\Controllers\Api\v1\User\UserController;
use App\Http\Controllers\Api\v1\User\WalletController;
use App\Http\Controllers\Api\v1\Web\WebController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/', function () {
    return response()->json([
        'message' => 'Adbarta API is running'
    ]);
});

Route::prefix('v1')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('signup', [AuthController::class, 'register']);
    Route::post('send-reset-password-code', [AuthController::class, 'sendResetPasswordCodeToMail']);
    Route::post('check-reset-password-code', [AuthController::class, 'checkResetCodeValidity']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
    Route::get('category-list', [WebController::class, 'categories']);
    Route::get('divisions', [WebController::class, 'getDivisions']);
    Route::get('get-districts/{id}', [WebController::class, 'getDistricts']);
    Route::get('get-sub-districts/{id}', [WebController::class, 'getSubDistricts']);
    Route::get('get-district', [WebController::class, 'getDistrict']);
    Route::get('sub-category-list/{categoryId}', [WebController::class, 'getSubCategories']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('verify-user', [AuthController::class, 'checkVerificationCodeValidity']);
        Route::get('profile', [UserController::class, 'profile']);
        Route::get('wallet', [WalletController::class, 'wallet']);
        Route::resource('chats', MessageController::class)->only('index', 'store');
        Route::get('chats/{roomId}', [MessageController::class, 'getMessagesByRoomId']);
        Route::get('check-chats/{userID}', [MessageController::class, 'checkExistingChat']);
        Route::post('profile-update', [UserController::class, 'profileUpdate']);
        Route::middleware(['checkUserRole:super_admin'])->group(function () {
            Route::resource('districts', DistrictController::class);
            Route::post('subs', [SubDistrictController::class, 'store']);
            Route::post('update-district', [DistrictController::class, 'updateStatus']);
            Route::get('sub-district/{id}', [DistrictController::class, 'getSubs']);
            Route::get('active-sub', [SubDistrictController::class, 'activeSubDistrict']);
            Route::get('active-district', [SubDistrictController::class, 'activeDistrict']);

            Route::resource('categories', CategoryController::class);
            Route::resource('sub-categories', SubCategoryController::class);
            Route::resource('users', UserController::class);
            Route::resource('ads', ProductController::class);
            Route::post('add-point/{id}', [WalletController::class, 'addWalletCredit']);
            Route::get('get-wallet-histories', [WalletController::class, 'getWalletHistory']);
            Route::get('get-historical-data', [AdminHomeController::class, 'homeData']);
            Route::post('change-status/{changeStatus}', [WalletController::class, 'changeStatus']);
        });
        Route::resource('products', ProductController::class);
        Route::post('upload-image', [ProductController::class, 'uploadImage']);
        Route::post('submit-transaction', [WalletController::class, 'saveTransactionId']);

        Route::get('signout', function () {
            auth()->user()->currentAccessToken()->delete();
            return HelperAction::successResponse('Successfully Logout', null);
        });

        Route::get('logout', function () {
            auth()->user()->currentAccessToken()->delete();
            return HelperAction::successResponse('Successfully Logout', null);
        });

        /**
         * Bkash Payment Api's
         */
        // Payment Routes for bKash
        Route::post('bkash-get-token', [BkashController::class, 'getToken']);
        Route::post('bkash-refresh-token', [BkashController::class, 'refreshToken']);
        Route::post('bkash-create-payment', [BkashController::class, 'createPayment']);
        Route::post('bkash-execute-payment', [BkashController::class, 'executePayment']);
        Route::post('bkash-query-payment', [BkashController::class, 'queryPayment']);
        Route::post('bkash-success', [BkashController::class, 'bkashSuccess']);

        // Refund Routes for bKash
//        Route::get('/bkash/refund', [\App\Http\Controllers\Api\v1\Payment\BkashController::class,'index'])->name('bkash-refund');
//        Route::post('/bkash/refund', 'BkashRefundController@refund')->name('bkash-refund');
    });
    Route::get('get-ads', [WebController::class, 'allAds']);
    Route::get('get-ads/{slug}', [WebController::class, 'adDetails']);
    Route::post('search-ad', [ProductController::class, 'searchProduct']);
});
