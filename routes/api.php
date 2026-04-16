<?php
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

// API routes
Route::get('products/{from}/{to}', [ProductController::class, 'index']);
Route::post('/products', [ProductController::class, 'store']);
Route::get('products/{product}', [ProductController::class, 'show']);


Route::put('products/{product}', [ProductController::class, 'update']);
Route::post('products/{product}/images', [ProductController::class, 'uploadImages']);
Route::delete('products/{product}/images/{image}', [ProductController::class, 'deleteImage']);

Route::delete('/products/{product}', [ProductController::class, 'destroy']);

Route::get('products/search/{query}/{from}/{to}', [ProductController::class, 'search']);
Route::get('products/{category}/{from}/{to}', [ProductController::class, 'getByCategory']);

Route::post('pay/order', [PaymentController::class, 'payByStripe']);
