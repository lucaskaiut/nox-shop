<?php

use App\Modules\Company\Http\Controllers\CompanyController;
use App\Modules\Customer\Http\Controllers\CustomerController;
use App\Modules\User\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'user', 'controller' => UserController::class], function () {
    Route::post('login', [UserController::class, 'login'])->name('user.login');
    Route::post('forgot-password', [UserController::class, 'sendResetPasswordLink'])->name('user.reset-password');
    Route::post('reset-password', [UserController::class, 'resetPassword'])->name('user.reset-password');
});
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::resource('user', UserController::class)->parameters(['user' => 'id'])->except(['create', 'edit']);
    Route::resource('customer', CustomerController::class)->parameters(['customer' => 'id'])->except(['create', 'edit']);
    Route::put('company', [CompanyController::class, 'update'])->name('company.update');
    Route::get('company', [CompanyController::class, 'show'])->name('company.show');
});
Route::post('customer/register', [CustomerController::class, 'register'])->name('customer.register');