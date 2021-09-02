<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('register', [AuthController::class, 'register'])->name('register');
Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('forgotPassword');
Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('resetPassword');

Route::get('products', [ProductController::class, 'index'])->name('products');
Route::get('products/{id}', [ProductController::class, 'show'])->name('product');
Route::get('categories', [CategoryController::class, 'index'])->name('categories');
Route::get('categories/{id}', [CategoryController::class, 'show'])->name('category');

Route::middleware('auth:sanctum')->group(function(){
    Route::get('user', [AuthController::class, 'user']);
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::post('checkout-session', [CheckoutController::class, 'index']);

    // admin routes
    Route::middleware('admin')->group(function(){
        Route::apiResource('admin/products', ProductController::class);
        Route::apiResource('admin/categories', CategoryController::class);
        Route::get('admin/users', [UserController::class, 'index']);
        Route::get('admin/orders', [OrderController::class, 'index']);
    });
});
