<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PublicController;
use App\Http\Controllers\Api\StoreOwnerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (No Authentication Required - Anonymous Browsing)
|--------------------------------------------------------------------------
*/
Route::prefix('public')->group(function () {
    // Categories
    Route::get('/categories', [PublicController::class, 'categories']);

    // Offers
    Route::get('/offers', [PublicController::class, 'offers']);
    Route::get('/offers/{id}', [PublicController::class, 'showOffer']);

    // Live Streams
    Route::get('/live-streams', [PublicController::class, 'liveStreams']);
    Route::get('/live-streams/{id}', [PublicController::class, 'showLiveStream']);
    Route::post('/live-streams/{id}/viewer-token', [PublicController::class, 'viewerToken']);

    // Stores
    Route::get('/stores', [PublicController::class, 'stores']);
    Route::get('/stores/{id}', [PublicController::class, 'showStore']);
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/profile', [AuthController::class, 'profile']);
    });
});

/*
|--------------------------------------------------------------------------
| Store Owner Routes (Requires Authentication + Store Owner Role)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'store_owner'])
    ->prefix('store-owner')
    ->group(function () {
        // Store Management
        Route::get('/store', [StoreOwnerController::class, 'myStore']);
        Route::post('/store', [StoreOwnerController::class, 'createStore']);
        Route::put('/store', [StoreOwnerController::class, 'updateStore']);

        // Offer CRUD
        Route::get('/offers', [StoreOwnerController::class, 'myOffers']);
        Route::post('/offers', [StoreOwnerController::class, 'createOffer']);
        Route::put('/offers/{id}', [StoreOwnerController::class, 'updateOffer']);
        Route::delete('/offers/{id}', [StoreOwnerController::class, 'deleteOffer']);

        // AI Description Generator
        Route::post('/ai/generate-description', [StoreOwnerController::class, 'generateAiDescription']);

        // Live Streaming
        Route::post('/live-streams/start', [StoreOwnerController::class, 'startLiveStream']);
        Route::post('/live-streams/{id}/end', [StoreOwnerController::class, 'endLiveStream']);
    });

/*
|--------------------------------------------------------------------------
| Admin Routes (Requires Authentication + Admin Role)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'admin'])
    ->prefix('admin')
    ->group(function () {
        // Store Verification
        Route::get('/stores', [AdminController::class, 'stores']);
        Route::post('/stores/{id}/approve', [AdminController::class, 'approveStore']);
        Route::post('/stores/{id}/suspend', [AdminController::class, 'suspendStore']);

        // User Management
        Route::get('/users', [AdminController::class, 'users']);
        Route::post('/users/{id}/toggle-status', [AdminController::class, 'toggleUserStatus']);

        // Category Management
        Route::get('/categories', [AdminController::class, 'categories']);
        Route::post('/categories', [AdminController::class, 'createCategory']);
        Route::put('/categories/{id}', [AdminController::class, 'updateCategory']);
        Route::delete('/categories/{id}', [AdminController::class, 'deleteCategory']);

        // Subcategory Management
        Route::post('/subcategories', [AdminController::class, 'createSubcategory']);
        Route::put('/subcategories/{id}', [AdminController::class, 'updateSubcategory']);
        Route::delete('/subcategories/{id}', [AdminController::class, 'deleteSubcategory']);

        // Live Stream Oversight
        Route::get('/live-streams', [AdminController::class, 'liveStreams']);
        Route::post('/live-streams/{id}/end', [AdminController::class, 'endLiveStream']);

        // Platform Metrics
        Route::get('/metrics', [AdminController::class, 'metrics']);
    });