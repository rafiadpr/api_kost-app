<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UnitCategoryController;
use App\Http\Controllers\UnitAssetController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthCustomerController;

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('v1')->group(function () {
    Route::get('/customers', [CustomerController::class, 'index']);
    Route::get('/customers/{id}', [CustomerController::class, 'show']);
    Route::post('/customers', [CustomerController::class, 'store']);
    Route::put('/customers/{id}', [CustomerController::class, 'update']);
    Route::delete('/customers/{id}', [CustomerController::class, 'destroy']);

    Route::get('/tasks', [TaskController::class, 'index']);
    Route::get('/tasks/{id}', [TaskController::class, 'show']);
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::put('/tasks/{id}', [TaskController::class, 'update']);
    Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);

    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    Route::get('/roles', [RoleController::class, 'index']);
    Route::get('/roles/{id}', [RoleController::class, 'show']);
    Route::post('/roles', [RoleController::class, 'store']);
    Route::put('/roles/{id}', [RoleController::class, 'update']);
    Route::delete('/roles/{id}', [RoleController::class, 'destroy']);

    Route::get('/unit-category', [UnitCategoryController::class, 'index']);
    Route::get('/unit-category/{id}', [UnitCategoryController::class, 'show']);
    Route::post('/unit-category', [UnitCategoryController::class, 'store']);
    Route::put('/unit-category/{id}', [UnitCategoryController::class, 'update']);
    Route::delete('/unit-category/{id}', [UnitCategoryController::class, 'destroy']);

    Route::get('/units', [UnitController::class, 'index']);
    Route::get('/units/{id}', [UnitController::class, 'show']);
    Route::post('/units', [UnitController::class, 'store']);
    Route::put('/units/{id}', [UnitController::class, 'update']);
    Route::delete('/units/{id}', [UnitController::class, 'destroy']);

    Route::get('/unit-asset', [UnitAssetController::class, 'index']);
    Route::get('/unit-asset/{id}', [UnitAssetController::class, 'show']);
    Route::post('/unit-asset', [UnitAssetController::class, 'store']);
    Route::put('/unit-asset/{id}', [UnitAssetController::class, 'update']);
    Route::delete('/unit-asset/{id}', [UnitAssetController::class, 'destroy']);

    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::get('/auth/profile', [AuthController::class, 'profile'])->middleware(['auth.api']);

    Route::post('/auth/forget-password', [AuthController::class, 'submitForgetPasswordForm'])->name('password.email'); //halaman forget password, untuk input email terus submit
    Route::get('/auth/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('reset.password.get'); //tampilan di email, untuk masuk ke halaman reset password
    Route::post('/auth/reset-password/{token}', [AuthController::class, 'submitResetPasswordForm'])->name('reset.password.post'); //halaman reset password, untuk input password baru

    Route::post('/auth/otp-request', [AuthController::class, 'submitForgetPasswordForm'])->name('otp.email');
    Route::get('/auth/otp-validation/{token}', [AuthController::class, 'showResetPasswordForm'])->name('otp.get');
    Route::post('/auth/otp-validation/{token}', [AuthController::class, 'submitResetPasswordForm'])->name('otp.post');

    Route::post('/auth/login-customer', [AuthCustomerController::class, 'login']);
    Route::get('/auth/profile-customer', [AuthCustomerController::class, 'profile'])->middleware(['auth.api']);
    // Route::middleware('auth:api_customer')->get('/auth/profile-customer', [AuthCustomerController::class, 'profile']);

    Route::post('/auth/forget-password-customer', [AuthCustomerController::class, 'submitForgetPasswordForm'])->name('forget.customer'); //halaman forget password, untuk input email terus submit
    Route::get('/auth/get-forget-token/{token}', [AuthCustomerController::class, 'getTokenCustomer']);
    Route::put('/auth/update-password', [AuthCustomerController::class, 'updatePassword']);

    Route::post('/auth/kirim-otp', [AuthCustomerController::class, 'kirimOtp']);
    Route::get('/auth/cek-email-otp/{email}', [AuthCustomerController::class, 'getEmailOtpCustomer']);
    Route::get('/auth/cek-otp', [AuthCustomerController::class, 'verifyOtp']);

    Route::get('/route-cache', function() {
        $exitCode = Artisan::call('optimize:clear');
        echo "optimized";
        return print_r($exitCode);
    });
});
