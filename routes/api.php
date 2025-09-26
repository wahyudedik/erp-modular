<?php

use App\Http\Controllers\Api\BusinessTypeController;
use App\Http\Controllers\Api\ModuleController;
use App\Http\Controllers\Api\UserModuleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserInvitationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Public API routes
Route::prefix('v1')->group(function () {
    // Business Types
    Route::get('/business-types', [BusinessTypeController::class, 'index']);
    Route::get('/business-types/{id}/module-recommendations', function ($id) {
        $businessType = \App\Models\BusinessType::findOrFail($id);
        $recommendations = $businessType->moduleRecommendations()
            ->with('module')
            ->orderedByPriority()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $recommendations,
            'message' => 'Module recommendations retrieved successfully'
        ]);
    });
    Route::get('/business-types/{businessType}', [BusinessTypeController::class, 'show']);
    Route::get('/business-types/{businessType}/module-recommendations', [BusinessTypeController::class, 'getModuleRecommendations']);

    // Modules
    Route::get('/modules', [ModuleController::class, 'index']);
    Route::get('/modules/{module}', [ModuleController::class, 'show']);
    Route::get('/modules/category/{category}', [ModuleController::class, 'getByCategory']);
    Route::get('/modules/categories/list', [ModuleController::class, 'getCategories']);
    Route::get('/modules/business-type/{businessType}', [ModuleController::class, 'getForBusinessType']);
    Route::get('/modules/statistics', [ModuleController::class, 'getStatistics']);

    // Debug routes
    Route::get('/debug/users', function () {
        try {
            $users = \App\Models\User::all();
            return response()->json([
                'success' => true,
                'count' => $users->count(),
                'users' => $users->take(1),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    });

    Route::post('/debug/login', function (Request $request) {
        try {
            $email = $request->input('email');
            $password = $request->input('password');

            $user = \App\Models\User::where('email', $email)->first();

            $result = [
                'success' => true,
                'input' => $request->all(),
                'user_found' => !!$user,
            ];

            if ($user) {
                $result['user_id'] = $user->id;
                $result['password_check'] = \Illuminate\Support\Facades\Hash::check($password, $user->password);
                $result['user_active'] = $user->is_active;

                // Test token creation
                $token = $user->createToken('api-token')->plainTextToken;
                $result['token_created'] = !!$token;
                $result['token_length'] = strlen($token);

                // Test businessType relationship
                $businessType = $user->businessType;
                $result['business_type_loaded'] = !!$businessType;
                if ($businessType) {
                    $result['business_type_name'] = $businessType->name;
                }
            }

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    });

    // Auth routes
    Route::post('/auth/login-simple', function (Request $request) {
        try {
            $email = $request->input('email');
            $password = $request->input('password');

            if (!$email || !$password) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email and password are required'
                ], 422);
            }

            $user = \App\Models\User::where('email', $email)->first();

            if (!$user || !\Illuminate\Support\Facades\Hash::check($password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }

            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user,
                    'token' => $token,
                ],
                'message' => 'Login successful'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    });
    Route::post('/auth/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
    Route::post('/auth/forgot-password', [\App\Http\Controllers\Api\AuthController::class, 'forgotPassword']);
    Route::post('/auth/reset-password', [\App\Http\Controllers\Api\AuthController::class, 'resetPassword']);
    Route::post('/auth/send-magic-link', [\App\Http\Controllers\Api\AuthController::class, 'sendMagicLink']);
    Route::post('/auth/magic-login', [\App\Http\Controllers\Api\AuthController::class, 'loginWithMagicLink']);

    Route::post('/auth/register', function (Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'company_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'business_type_id' => 'required|exists:business_types,id',
            'address' => 'required|string|max:500',
        ]);

        $user = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            'company_name' => $validated['company_name'],
            'phone' => $validated['phone'],
            'business_type_id' => $validated['business_type_id'],
            'address' => $validated['address'],
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'token' => $token
            ],
            'message' => 'Registration successful'
        ]);
    });
});

// Protected API routes
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Authentication routes
    Route::post('/auth/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::post('/auth/change-password', [\App\Http\Controllers\Api\AuthController::class, 'changePassword']);
    Route::get('/auth/sessions', [\App\Http\Controllers\Api\AuthController::class, 'getSessions']);
    Route::delete('/auth/sessions/{sessionId}', [\App\Http\Controllers\Api\AuthController::class, 'revokeSession']);
    Route::post('/auth/sessions/revoke-all', [\App\Http\Controllers\Api\AuthController::class, 'revokeAllOtherSessions']);
    Route::get('/auth/security-events', [\App\Http\Controllers\Api\AuthController::class, 'getSecurityEvents']);

    // 2FA routes
    Route::post('/auth/2fa/enable', [\App\Http\Controllers\Api\AuthController::class, 'enable2FA']);
    Route::post('/auth/2fa/verify', [\App\Http\Controllers\Api\AuthController::class, 'verify2FA']);
    Route::post('/auth/2fa/disable', [\App\Http\Controllers\Api\AuthController::class, 'disable2FA']);

    // Email verification routes
    Route::post('/auth/send-email-verification', [\App\Http\Controllers\Api\AuthController::class, 'sendEmailVerification']);
    Route::post('/auth/verify-email', [\App\Http\Controllers\Api\AuthController::class, 'verifyEmail']);

    // Remember me routes
    Route::post('/auth/refresh-token', [\App\Http\Controllers\Api\AuthController::class, 'refreshToken']);

    // Re-authentication routes
    Route::post('/auth/generate-reauth-token', [\App\Http\Controllers\Api\AuthController::class, 'generateReauthToken']);

    // Account management routes
    Route::post('/auth/deactivate-account', [\App\Http\Controllers\Api\AuthController::class, 'deactivateAccount']);
    Route::post('/auth/delete-account', [\App\Http\Controllers\Api\AuthController::class, 'deleteAccount']);
    Route::post('/auth/reactivate-account', [\App\Http\Controllers\Api\AuthController::class, 'reactivateAccount']);

    // Backup management routes
    Route::post('/backup/create-full', [\App\Http\Controllers\Api\BackupController::class, 'createFullBackup']);
    Route::post('/backup/create-incremental', [\App\Http\Controllers\Api\BackupController::class, 'createIncrementalBackup']);
    Route::get('/backup/list', [\App\Http\Controllers\Api\BackupController::class, 'listBackups']);
    Route::post('/backup/restore', [\App\Http\Controllers\Api\BackupController::class, 'restoreBackup']);
    Route::delete('/backup/delete', [\App\Http\Controllers\Api\BackupController::class, 'deleteBackup']);

    // User Management
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::post('/users/{user}/deactivate', [UserController::class, 'deactivate']);
    Route::post('/users/{user}/activate', [UserController::class, 'activate']);

    // User Profile
    Route::get('/profile', [UserController::class, 'profile']);
    Route::put('/profile', [UserController::class, 'updateProfile']);

    // User Business Type
    Route::post('/user/business-type', function (Request $request) {
        $validated = $request->validate([
            'business_type_id' => 'required|exists:business_types,id'
        ]);

        $user = $request->user();
        $user->update(['business_type_id' => $validated['business_type_id']]);

        return response()->json([
            'success' => true,
            'data' => $user->fresh(['businessType']),
            'message' => 'Business type updated successfully'
        ]);
    });

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

    // Concrete Factory Modules // Added
    Route::apiResource('mix-designs', \App\Http\Controllers\Api\MixDesignController::class);
    Route::get('/mix-designs/{mixDesign}/cost', [\App\Http\Controllers\Api\MixDesignController::class, 'calculateCost']);
    Route::get('/mix-designs-statistics', [\App\Http\Controllers\Api\MixDesignController::class, 'statistics']);
});

// Public invitation routes
Route::prefix('v1')->group(function () {
    Route::post('/invitations/accept', [UserInvitationController::class, 'accept']);
});
