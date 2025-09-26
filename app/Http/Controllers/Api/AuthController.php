<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Temporarily removed AuthenticationService dependency

    /**
     * Login user
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
            'remember_me' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }

            if (!$user->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account is inactive'
                ], 403);
            }

            // Create token with expiration based on remember me
            $tokenName = $request->remember_me ? 'remember-token' : 'api-token';
            $token = $user->createToken($tokenName)->plainTextToken;

            // Log successful login
            $this->logSecurityEvent(
                $user->id,
                'login_success',
                'info',
                'User logged in successfully',
                [
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'remember_me' => $request->remember_me ?? false
                ]
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user->fresh(['businessType']),
                    'token' => $token,
                ],
                'message' => 'Login successful'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout user
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Log logout event
            $this->logSecurityEvent(
                $user->id,
                'logout',
                'info',
                'User logged out successfully'
            );

            // Revoke all tokens for this user
            $user->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send forgot password email
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                // Return success even if user doesn't exist for security
                return response()->json([
                    'success' => true,
                    'message' => 'If the email exists, a password reset link has been sent'
                ]);
            }

            // Generate reset token
            $token = \Illuminate\Support\Str::random(64);
            $expiresAt = now()->addHours(1);

            // Store reset token
            \App\Models\PasswordReset::create([
                'email' => $request->email,
                'token' => Hash::make($token),
                'created_at' => now(),
            ]);

            // TODO: Send email with reset link
            // For now, we'll log the token for testing
            \Log::info("Password reset token for {$request->email}: {$token}");

            return response()->json([
                'success' => true,
                'message' => 'Password reset link sent to your email',
                'debug_token' => $token // Remove this in production
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send password reset email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset password with token
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $passwordReset = \App\Models\PasswordReset::where('email', $request->email)
                ->where('created_at', '>', now()->subHours(1))
                ->first();

            if (!$passwordReset || !Hash::check($request->token, $passwordReset->token)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired reset token'
                ], 400);
            }

            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Update password
            $user->update(['password' => Hash::make($request->password)]);

            // Delete the reset token
            $passwordReset->delete();

            // Revoke all existing tokens
            $user->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Change password
     */
    public function changePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $request->user();

            // Verify old password
            if (!Hash::check($request->old_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid old password'
                ], 400);
            }

            // Update password
            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Password change failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user sessions
     */
    public function getSessions(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $sessions = $user->userSessions()
                ->orderBy('last_activity', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $sessions,
                'message' => 'Sessions retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sessions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Revoke specific session
     */
    public function revokeSession(Request $request, $sessionId): JsonResponse
    {
        try {
            $user = $request->user();
            $session = $user->userSessions()->findOrFail($sessionId);

            // If it's the current session, revoke the token
            if ($session->is_current) {
                $request->user()->currentAccessToken()->delete();
            }

            $session->delete();

            return response()->json([
                'success' => true,
                'message' => 'Session revoked successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to revoke session: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Revoke all other sessions
     */
    public function revokeAllOtherSessions(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Revoke all tokens except current
            $user->tokens()
                ->where('id', '!=', $request->user()->currentAccessToken()->id)
                ->delete();

            // Mark all other sessions as inactive
            $user->userSessions()
                ->where('is_current', false)
                ->update(['is_active' => false]);

            return response()->json([
                'success' => true,
                'message' => 'All other sessions revoked successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to revoke sessions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get security events
     */
    public function getSecurityEvents(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $events = $user->securityEvents()
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $events,
                'message' => 'Security events retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve security events: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Enable 2FA for user
     */
    public function enable2FA(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Generate secret key
            $secretKey = $this->generateSecretKey();
            $recoveryCodes = $this->generateRecoveryCodes();

            // Create or update 2FA record
            $twoFA = $user->twoFactorAuthentication()->firstOrNew();
            $twoFA->secret_key = $secretKey;
            $twoFA->recovery_codes = json_encode($recoveryCodes);
            $twoFA->is_enabled = false; // Not enabled until verified
            $twoFA->save();

            // Generate QR code URL
            $qrCodeUrl = $this->generateQRCodeUrl($user->email, $secretKey);

            return response()->json([
                'success' => true,
                'data' => [
                    'secret_key' => $secretKey,
                    'qr_code_url' => $qrCodeUrl,
                    'recovery_codes' => $recoveryCodes,
                ],
                'message' => '2FA setup initiated. Please verify with your authenticator app.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to enable 2FA: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify 2FA setup
     */
    public function verify2FA(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $request->user();
            $twoFA = $user->twoFactorAuthentication;

            if (!$twoFA || !$twoFA->secret_key) {
                return response()->json([
                    'success' => false,
                    'message' => '2FA not set up'
                ], 400);
            }

            // Verify the code
            if ($this->verifyTOTPCode($twoFA->secret_key, $request->code)) {
                $twoFA->is_enabled = true;
                $twoFA->enabled_at = now();
                $twoFA->save();

                $this->logSecurityEvent(
                    $user->id,
                    '2fa_enabled',
                    'info',
                    'Two-factor authentication enabled'
                );

                return response()->json([
                    'success' => true,
                    'message' => '2FA enabled successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid verification code'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify 2FA: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Disable 2FA
     */
    public function disable2FA(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $request->user();

            // Verify password
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid password'
                ], 400);
            }

            // Disable 2FA
            $user->twoFactorAuthentication()->delete();

            $this->logSecurityEvent(
                $user->id,
                '2fa_disabled',
                'info',
                'Two-factor authentication disabled'
            );

            return response()->json([
                'success' => true,
                'message' => '2FA disabled successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to disable 2FA: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate secret key for 2FA
     */
    private function generateSecretKey(): string
    {
        return \Google2FA::generateSecretKey();
    }

    /**
     * Generate recovery codes
     */
    private function generateRecoveryCodes(): array
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = strtoupper(\Illuminate\Support\Str::random(8));
        }
        return $codes;
    }

    /**
     * Generate QR code URL
     */
    private function generateQRCodeUrl(string $email, string $secretKey): string
    {
        $issuer = config('app.name');
        $accountName = $email;

        return \Google2FA::getQRCodeUrl(
            $issuer,
            $accountName,
            $secretKey
        );
    }

    /**
     * Verify TOTP code
     */
    private function verifyTOTPCode(string $secretKey, string $code): bool
    {
        return \Google2FA::verifyKey($secretKey, $code);
    }

    /**
     * Send email verification
     */
    public function sendEmailVerification(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Create verification record
            $verification = \App\Models\EmailVerification::createForUser($user);

            // TODO: Send actual email
            // For now, we'll log the verification link
            $verificationUrl = config('app.frontend_url') . '/verify-email?token=' . $verification->token;
            \Log::info("Email verification link for {$user->email}: {$verificationUrl}");

            return response()->json([
                'success' => true,
                'message' => 'Verification email sent successfully',
                'debug_url' => $verificationUrl // Remove this in production
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send verification email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify email with token
     */
    public function verifyEmail(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $verification = \App\Models\EmailVerification::where('token', $request->token)
                ->where('is_verified', false)
                ->first();

            if (!$verification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid verification token'
                ], 400);
            }

            if ($verification->isExpired()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Verification token has expired'
                ], 400);
            }

            // Mark as verified
            $verification->markAsVerified();

            // Update user email if different
            if ($verification->email !== $verification->user->email) {
                $verification->user->update(['email' => $verification->email]);
            }

            $this->logSecurityEvent(
                $verification->user->id,
                'email_verified',
                'info',
                'Email address verified successfully'
            );

            return response()->json([
                'success' => true,
                'message' => 'Email verified successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send magic link for passwordless login
     */
    public function sendMagicLink(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $email = $request->email;
            $user = User::where('email', $email)->first();

            if (!$user) {
                // Return success even if user doesn't exist for security
                return response()->json([
                    'success' => true,
                    'message' => 'If the email exists, a magic link has been sent'
                ]);
            }

            // Create magic link
            $magicLink = \App\Models\MagicLink::createForEmail($email, 1);

            // TODO: Send actual email
            // For now, we'll log the magic link
            $loginUrl = config('app.frontend_url') . '/magic-login?token=' . $magicLink->token;
            \Log::info("Magic link for {$email}: {$loginUrl}");

            return response()->json([
                'success' => true,
                'message' => 'Magic link sent to your email',
                'debug_url' => $loginUrl // Remove this in production
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send magic link: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Login with magic link
     */
    public function loginWithMagicLink(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $magicLink = \App\Models\MagicLink::findValidByToken($request->token);

            if (!$magicLink) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired magic link'
                ], 400);
            }

            $user = User::where('email', $magicLink->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            if (!$user->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account is inactive'
                ], 403);
            }

            // Mark magic link as used
            $magicLink->markAsUsed();

            // Create token
            $token = $user->createToken('api-token')->plainTextToken;

            // Log successful login
            $this->logSecurityEvent(
                $user->id,
                'magic_link_login',
                'info',
                'User logged in with magic link',
                ['ip' => request()->ip(), 'user_agent' => request()->userAgent()]
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user->fresh(['businessType']),
                    'token' => $token,
                ],
                'message' => 'Login successful with magic link'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to login with magic link: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refresh token for remember me functionality
     */
    public function refreshToken(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Check if user has remember token
            $rememberToken = $user->tokens()
                ->where('name', 'remember-token')
                ->where('expires_at', '>', now())
                ->first();

            if (!$rememberToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid remember token found'
                ], 401);
            }

            // Create new token
            $newToken = $user->createToken('api-token')->plainTextToken;

            // Log token refresh
            $this->logSecurityEvent(
                $user->id,
                'token_refreshed',
                'info',
                'Token refreshed successfully'
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user->fresh(['businessType']),
                    'token' => $newToken,
                ],
                'message' => 'Token refreshed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh token: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate re-authentication token
     */
    public function generateReauthToken(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $request->user();

            // Verify password
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid password'
                ], 400);
            }

            // Generate re-authentication token
            $reauthToken = \Illuminate\Support\Str::random(64);

            // Store token in session (valid for 15 minutes)
            session([
                "reauth_token_{$user->id}" => $reauthToken,
                "reauth_time_{$user->id}" => now(),
            ]);

            // Log re-authentication
            $this->logSecurityEvent(
                $user->id,
                'reauth_token_generated',
                'info',
                'Re-authentication token generated'
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'reauth_token' => $reauthToken,
                    'expires_in' => 900, // 15 minutes
                ],
                'message' => 'Re-authentication token generated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate re-authentication token: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Deactivate user account
     */
    public function deactivateAccount(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
            'reason' => 'string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $request->user();

            // Verify password
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid password'
                ], 400);
            }

            // Deactivate account
            $user->update([
                'is_active' => false,
                'deactivated_at' => now(),
                'deactivation_reason' => $request->reason,
            ]);

            // Revoke all tokens
            $user->tokens()->delete();

            // Log account deactivation
            $this->logSecurityEvent(
                $user->id,
                'account_deactivated',
                'high',
                'User account deactivated',
                ['reason' => $request->reason]
            );

            return response()->json([
                'success' => true,
                'message' => 'Account deactivated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to deactivate account: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete user account permanently
     */
    public function deleteAccount(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
            'confirmation' => 'required|string|in:DELETE',
            'reason' => 'string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $request->user();

            // Verify password
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid password'
                ], 400);
            }

            // Log account deletion before deletion
            $this->logSecurityEvent(
                $user->id,
                'account_deleted',
                'critical',
                'User account permanently deleted',
                ['reason' => $request->reason]
            );

            // Delete user account
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Account deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete account: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reactivate user account
     */
    public function reactivateAccount(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::where('email', $request->email)
                ->where('is_active', false)
                ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account not found or already active'
                ], 404);
            }

            // TODO: Verify reactivation token
            // For now, we'll just reactivate the account
            $user->update([
                'is_active' => true,
                'deactivated_at' => null,
                'deactivation_reason' => null,
            ]);

            // Log account reactivation
            $this->logSecurityEvent(
                $user->id,
                'account_reactivated',
                'info',
                'User account reactivated'
            );

            return response()->json([
                'success' => true,
                'message' => 'Account reactivated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reactivate account: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Log security event
     */
    private function logSecurityEvent($userId, $eventType, $severity, $description, $metadata = [])
    {
        try {
            \App\Models\SecurityEvent::create([
                'user_id' => $userId,
                'event_type' => $eventType,
                'severity' => $severity,
                'description' => $description,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'metadata' => json_encode($metadata),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to log security event: ' . $e->getMessage());
        }
    }
}
