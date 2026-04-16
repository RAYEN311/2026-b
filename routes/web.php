<?php

use App\Http\Controllers\Api\ProductController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ----------------------
// INERTIA ROUTES
// ----------------------

Route::get('products/{category}/{from}/{to}', [ProductController::class, 'getByCategory']);

// REACT CATCH-ALL — must be LAST
Route::get('/', function () {
    return response()->file(public_path('react.html'));
})->where('', '^(?!api|_ignition|sanctum|storage|assets).*$');

// AUTH ROUTES
// Serve storage images

// Dashboard (auth)
// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// // Authenticated user routes
// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

// // ----------------------
// // REACT FRONTEND ROUTE
// // ----------------------

// // Catch-all route for React (must be at the END)
// // Catch-all route for React frontend

// ----------------------
require __DIR__.'/auth.php';