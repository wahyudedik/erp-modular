<?php

use App\Http\Controllers\Api\BusinessTypeController;
use App\Http\Controllers\Api\ModuleController;
use App\Http\Controllers\Api\UserModuleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserInvitationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public API routes
Route::prefix('v1')->group(function () {
    // Business Types
    Route::get('/business-types', [BusinessTypeController::class, 'index']);
    Route::get('/business-types/{businessType}', [BusinessTypeController::class, 'show']);
    Route::get('/business-types/{businessType}/module-recommendations', [BusinessTypeController::class, 'getModuleRecommendations']);

    // Modules
    Route::get('/modules', [ModuleController::class, 'index']);
    Route::get('/modules/{module}', [ModuleController::class, 'show']);
    Route::get('/modules/category/{category}', [ModuleController::class, 'getByCategory']);
    Route::get('/modules/categories/list', [ModuleController::class, 'getCategories']);
    Route::get('/modules/business-type/{businessType}', [ModuleController::class, 'getForBusinessType']);
    Route::get('/modules/statistics', [ModuleController::class, 'getStatistics']);
});

// Protected API routes
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // User Management
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::post('/users/{user}/deactivate', [UserController::class, 'deactivate']);
    Route::post('/users/{user}/activate', [UserController::class, 'activate']);

    // User Profile
    Route::get('/profile', [UserController::class, 'profile']);
    Route::put('/profile', [UserController::class, 'updateProfile']);

    // User Invitations
    Route::get('/invitations', [UserInvitationController::class, 'index']);
    Route::post('/invitations', [UserInvitationController::class, 'store']);
    Route::get('/invitations/{invitation}', [UserInvitationController::class, 'show']);
    Route::post('/invitations/{invitation}/resend', [UserInvitationController::class, 'resend']);
    Route::post('/invitations/{invitation}/cancel', [UserInvitationController::class, 'cancel']);

    // User Modules
    Route::get('/user-modules', [UserModuleController::class, 'index']);
    Route::get('/user-modules/active', [UserModuleController::class, 'getActive']);
    Route::post('/user-modules/activate', [UserModuleController::class, 'activate']);
    Route::post('/user-modules/bulk-activate', [UserModuleController::class, 'bulkActivate']);
    Route::delete('/user-modules/{module}/deactivate', [UserModuleController::class, 'deactivate']);
    Route::put('/user-modules/{module}/configuration', [UserModuleController::class, 'updateConfiguration']);
});

// Public invitation routes
Route::prefix('v1')->group(function () {
    Route::post('/invitations/accept', [UserInvitationController::class, 'accept']);
});
