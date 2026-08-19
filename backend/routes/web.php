<?php

use App\Http\Controllers\Web\AuthWebController;
use App\Http\Controllers\Web\AdminWebController;
use App\Http\Controllers\Web\StoreOwnerWebController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Web Dashboards (Admin & Store Owner)
|--------------------------------------------------------------------------
*/

// Guest routes (authentication)
Route::get('/login', [AuthWebController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthWebController::class, 'login']);
Route::get('/register', [AuthWebController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthWebController::class, 'register']);

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout');

    // Redirect based on role
    Route::get('/dashboard', function () {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('store.dashboard');
    })->name('dashboard');

    // Admin routes
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminWebController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [AdminWebController::class, 'users'])->name('users.index');
        Route::post('/users/{id}/toggle-status', [AdminWebController::class, 'toggleUserStatus'])->name('users.toggle-status');
        Route::get('/stores', [AdminWebController::class, 'stores'])->name('stores.index');
        Route::post('/stores/{id}/approve', [AdminWebController::class, 'approveStore'])->name('stores.approve');
        Route::post('/stores/{id}/suspend', [AdminWebController::class, 'suspendStore'])->name('stores.suspend');
        Route::get('/categories', [AdminWebController::class, 'categories'])->name('categories.index');
        Route::post('/categories', [AdminWebController::class, 'storeCategory'])->name('categories.store');
        Route::put('/categories/{id}', [AdminWebController::class, 'updateCategory'])->name('categories.update');
        Route::delete('/categories/{id}', [AdminWebController::class, 'deleteCategory'])->name('categories.delete');
        Route::get('/subcategories', [AdminWebController::class, 'subcategories'])->name('subcategories.index');
        Route::post('/subcategories', [AdminWebController::class, 'storeSubcategory'])->name('subcategories.store');
        Route::put('/subcategories/{id}', [AdminWebController::class, 'updateSubcategory'])->name('subcategories.update');
        Route::delete('/subcategories/{id}', [AdminWebController::class, 'deleteSubcategory'])->name('subcategories.delete');
        Route::get('/live-streams', [AdminWebController::class, 'liveStreams'])->name('live-streams.index');
        Route::post('/live-streams/{id}/end', [AdminWebController::class, 'endLiveStream'])->name('live-streams.end');
    });

    // Store Owner routes
    Route::middleware('store_owner')->prefix('store')->name('store.')->group(function () {
        Route::get('/', [StoreOwnerWebController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [StoreOwnerWebController::class, 'editStore'])->name('profile.edit');
        Route::put('/profile', [StoreOwnerWebController::class, 'updateStore'])->name('profile.update');
        Route::get('/offers', [StoreOwnerWebController::class, 'offersIndex'])->name('offers.index');
        Route::get('/offers/create', [StoreOwnerWebController::class, 'createOffer'])->name('offers.create');
        Route::post('/offers', [StoreOwnerWebController::class, 'storeOffer'])->name('offers.store');
        Route::get('/offers/{id}/edit', [StoreOwnerWebController::class, 'editOffer'])->name('offers.edit');
        Route::put('/offers/{id}', [StoreOwnerWebController::class, 'updateOffer'])->name('offers.update');
        Route::delete('/offers/{id}', [StoreOwnerWebController::class, 'deleteOffer'])->name('offers.delete');
        Route::post('/ai/generate-description', [StoreOwnerWebController::class, 'generateDescription'])->name('ai.generate-description');
        Route::get('/live-streams', [StoreOwnerWebController::class, 'liveStreamsIndex'])->name('live-streams.index');
        Route::get('/live-streams/broadcast', [StoreOwnerWebController::class, 'broadcast'])->name('live-streams.broadcast');
        Route::post('/live-streams/start', [StoreOwnerWebController::class, 'startLiveStream'])->name('live-streams.start');
        Route::post('/live-streams/{id}/end', [StoreOwnerWebController::class, 'endLiveStream'])->name('live-streams.end');
    });
});

// Root redirect
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('store.dashboard');
    }
    return redirect()->route('login');
});