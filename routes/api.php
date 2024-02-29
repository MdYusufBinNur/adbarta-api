<?php

use App\Action\HelperAction;
use App\Http\Controllers\Api\v1\Admin\CategoryController;
use App\Http\Controllers\Api\v1\Admin\SubCategoryController;
use App\Http\Controllers\Api\v1\Auth\AuthController;
use App\Http\Controllers\Api\v1\User\UserController;
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

Route::prefix('v1')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('signup', [AuthController::class, 'register']);
    Route::post('send-reset-password-code', [AuthController::class, 'sendResetPasswordCodeToMail']);
    Route::post('check-reset-password-code', [AuthController::class, 'checkResetCodeValidity']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);

    Route::get('category-list',[WebController::class,'categories']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('verify-user', [AuthController::class, 'checkVerificationCodeValidity']);
        Route::get('profile', [UserController::class, 'profile']);
        Route::post('profile-update', [UserController::class, 'profileUpdate']);

        Route::middleware(['checkUserRole:super_admin'])->group(function () {
            Route::resource('categories', CategoryController::class);
            Route::resource('sub-categories', SubCategoryController::class);
            Route::resource('users', UserController::class);

        });
        Route::get('signout', function () {
            auth()->user()->currentAccessToken()->delete();
            return HelperAction::successResponse('Successfully Logout', null);
        });
    });
});
