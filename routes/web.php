<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Models\Package;

Route::get('/', function () {
    $packages = Package::all();
    return view('welcome', compact('packages'));
});

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/logout', [AuthController::class, 'logout']);

Route::get('/verify', [AuthController::class, 'showVerify']);
Route::post('/verify', [AuthController::class, 'verify']);

// Authenticated Routes
Route::middleware('auth')->group(function () {
    
    // Client Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::post('/tickets', [DashboardController::class, 'storeTicket']);
    Route::post('/checkout', [OrderController::class, 'checkout']);
    Route::delete('/order/{id}/cancel', [OrderController::class, 'cancel'])->name('order.cancel');

    // Admin Panel (Protected by Controller Check)
    Route::get('/admin', [AdminController::class, 'index']);
    Route::post('/admin/packages', [AdminController::class, 'storePackage']);
    Route::put('/admin/packages/{id}', [AdminController::class, 'updatePackage']);
    Route::delete('/admin/packages/{id}', [AdminController::class, 'destroyPackage']);

});
