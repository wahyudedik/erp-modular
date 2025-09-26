<?php

namespace App\Services;

use App\Models\User;
use App\Models\PasswordReset;
use App\Models\TwoFactorAuthentication;
use App\Models\UserSession;
use App\Models\SecurityEvent;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AuthenticationService
{
    /**
     * Logout user and revoke all tokens
     */
    public function logout(User $user, Request $request = null): bool
    {
        try {
            // Revoke all tokens
            $user->tokens()->delete();

            // Deactivate all sessions
            $user->userSessions()->update(['is_active' => false]);

            // Log security event
            $this->logSecurityEvent($user, 'logout', 'User logged out successfully', $request);

            return true;
        } catch (\Exception $e) {
            $this->logSecurityEvent($user, 'logout_failed', 'Failed to logout user: ' . $e->getMessage(), $request, 'high');
            return false;
        }
    }

    /**
     * Send forgot password email
     */
    public function sendForgotPasswordEmail(string $email, Request $request = null): bool
    {
        try {
            $user = User::where('email', $email)->first();

            if (!$user) {
                // Log suspicious activity
                $this->logSecurityEvent(null, 'forgot_password_attempt', "Forgot password attempt for non-existent email: {$email}", $request, 'medium');
                return false;
            }

            // Generate reset token
            $token = Str::random(64);
            $expiresAt = now()->addHours(1);

            // Store reset token
            PasswordReset::create([
                'email' => $email,
                'token' => Hash::make($token),
                'expires_at' => $expiresAt,
                'ip_address' => $request?->ip(),
                'user_agent' => $request?->userAgent(),
            ]);

            // Send email (implement your email template)
            // Mail::to($email)->send(new ForgotPasswordMail($token, $expiresAt));

            // Log security event
            $this->logSecurityEvent($user, 'forgot_password_sent', 'Password reset email sent', $request);

            return true;
        } catch (\Exception $e) {
            $this->logSecurityEvent(null, 'forgot_password_failed', 'Failed to send forgot password email: ' . $e->getMessage(), $request, 'high');
            return false;
        }
    }

    /**
     * Reset password with token
     */
    public function resetPassword(string $token, string $newPassword, Request $request = null): bool
    {
        try {
            $passwordReset = PasswordReset::where('token', Hash::make($token))
                ->where('expires_at', '>', now())
                ->where('used', false)
                ->first();

            if (!$passwordReset) {
                $this->logSecurityEvent(null, 'invalid_reset_token', 'Invalid or expired password reset token used', $request, 'medium');
                return false;
            }

            $user = User::where('email', $passwordReset->email)->first();

            if (!$user) {
                return false;
            }

            // Update password
            $user->update(['password' => Hash::make($newPassword)]);

            // Mark token as used
            $passwordReset->update(['used' => true]);

            // Revoke all tokens
            $user->tokens()->delete();

            // Log security event
            $this->logSecurityEvent($user, 'password_reset', 'Password reset successfully', $request);

            return true;
        } catch (\Exception $e) {
            $this->logSecurityEvent(null, 'password_reset_failed', 'Failed to reset password: ' . $e->getMessage(), $request, 'high');
            return false;
        }
    }

    /**
     * Change password with old password verification
     */
    public function changePassword(User $user, string $oldPassword, string $newPassword, Request $request = null): bool
    {
        try {
            // Verify old password
            if (!Hash::check($oldPassword, $user->password)) {
                $this->logSecurityEvent($user, 'invalid_old_password', 'Invalid old password provided for password change', $request, 'medium');
                return false;
            }

            // Update password
            $user->update(['password' => Hash::make($newPassword)]);

            // Revoke all tokens except current one
            $user->tokens()->where('id', '!=', $request->user()->currentAccessToken()->id)->delete();

            // Log security event
            $this->logSecurityEvent($user, 'password_changed', 'Password changed successfully', $request);

            return true;
        } catch (\Exception $e) {
            $this->logSecurityEvent($user, 'password_change_failed', 'Failed to change password: ' . $e->getMessage(), $request, 'high');
            return false;
        }
    }

    /**
     * Enable Two-Factor Authentication
     */
    public function enable2FA(User $user, string $secretKey, array $recoveryCodes, Request $request = null): bool
    {
        try {
            $twoFA = $user->twoFactorAuthentication()->firstOrNew();
            $twoFA->update([
                'secret_key' => $secretKey,
                'recovery_codes' => $recoveryCodes,
                'is_enabled' => true,
                'enabled_at' => now(),
                'app_enabled' => true,
            ]);

            $this->logSecurityEvent($user, '2fa_enabled', 'Two-factor authentication enabled', $request);

            return true;
        } catch (\Exception $e) {
            $this->logSecurityEvent($user, '2fa_enable_failed', 'Failed to enable 2FA: ' . $e->getMessage(), $request, 'high');
            return false;
        }
    }

    /**
     * Verify 2FA code
     */
    public function verify2FA(User $user, string $code): bool
    {
        try {
            $twoFA = $user->twoFactorAuthentication;

            if (!$twoFA || !$twoFA->is_enabled) {
                return false;
            }

            // Implement TOTP verification logic here
            // This is a simplified version - you should use a proper TOTP library

            $twoFA->update(['last_used_at' => now()]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Create user session
     */
    public function createUserSession(User $user, Request $request): UserSession
    {
        $sessionId = Str::uuid()->toString();

        return UserSession::create([
            'user_id' => $user->id,
            'session_id' => $sessionId,
            'device_name' => $this->getDeviceName($request),
            'device_type' => $this->getDeviceType($request),
            'browser' => $this->getBrowser($request),
            'os' => $this->getOS($request),
            'ip_address' => $request->ip(),
            'country' => $this->getCountry($request),
            'city' => $this->getCity($request),
            'is_active' => true,
            'is_current' => true,
            'last_activity' => now(),
            'expires_at' => now()->addDays(30),
        ]);
    }

    /**
     * Get active sessions for user
     */
    public function getActiveSessions(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return $user->userSessions()
            ->where('is_active', true)
            ->orderBy('last_activity', 'desc')
            ->get();
    }

    /**
     * Revoke session
     */
    public function revokeSession(User $user, string $sessionId): bool
    {
        try {
            $session = $user->userSessions()->where('session_id', $sessionId)->first();

            if ($session) {
                $session->update(['is_active' => false]);
                $this->logSecurityEvent($user, 'session_revoked', "Session revoked: {$sessionId}");
                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check for suspicious activity
     */
    public function checkSuspiciousActivity(User $user, Request $request): bool
    {
        $suspicious = false;

        // Check for multiple failed login attempts
        $failedAttempts = SecurityEvent::where('user_id', $user->id)
            ->where('event_type', 'login_failed')
            ->where('created_at', '>', now()->subMinutes(15))
            ->count();

        if ($failedAttempts >= 5) {
            $suspicious = true;
            $this->logSecurityEvent($user, 'multiple_failed_logins', "Multiple failed login attempts: {$failedAttempts}", $request, 'high');
        }

        // Check for login from new location
        $recentLogins = SecurityEvent::where('user_id', $user->id)
            ->where('event_type', 'login_success')
            ->where('created_at', '>', now()->subDays(7))
            ->get();

        $newLocation = true;
        foreach ($recentLogins as $login) {
            if ($login->ip_address === $request->ip()) {
                $newLocation = false;
                break;
            }
        }

        if ($newLocation && $recentLogins->count() > 0) {
            $suspicious = true;
            $this->logSecurityEvent($user, 'login_from_new_location', 'Login from new location detected', $request, 'medium');
        }

        return $suspicious;
    }

    /**
     * Log security event
     */
    public function logSecurityEvent(?User $user, string $eventType, string $description, Request $request = null, string $severity = 'medium'): void
    {
        SecurityEvent::create([
            'user_id' => $user?->id,
            'event_type' => $eventType,
            'severity' => $severity,
            'description' => $description,
            'ip_address' => $request?->ip() ?? 'unknown',
            'user_agent' => $request?->userAgent(),
            'country' => $this->getCountry($request),
            'city' => $this->getCity($request),
            'metadata' => [
                'timestamp' => now()->toISOString(),
                'user_agent_parsed' => $this->parseUserAgent($request?->userAgent()),
            ],
        ]);
    }

    /**
     * Get device name from request
     */
    private function getDeviceName(Request $request): string
    {
        $userAgent = $request->userAgent();
        // Implement device detection logic
        return 'Unknown Device';
    }

    /**
     * Get device type from request
     */
    private function getDeviceType(Request $request): string
    {
        $userAgent = $request->userAgent();
        // Implement device type detection logic
        return 'Desktop';
    }

    /**
     * Get browser from request
     */
    private function getBrowser(Request $request): string
    {
        $userAgent = $request->userAgent();
        // Implement browser detection logic
        return 'Unknown Browser';
    }

    /**
     * Get OS from request
     */
    private function getOS(Request $request): string
    {
        $userAgent = $request->userAgent();
        // Implement OS detection logic
        return 'Unknown OS';
    }

    /**
     * Get country from request
     */
    private function getCountry(Request $request = null): ?string
    {
        // Implement geolocation logic
        return null;
    }

    /**
     * Get city from request
     */
    private function getCity(Request $request = null): ?string
    {
        // Implement geolocation logic
        return null;
    }

    /**
     * Parse user agent
     */
    private function parseUserAgent(?string $userAgent): array
    {
        if (!$userAgent) {
            return [];
        }

        // Implement user agent parsing logic
        return [
            'raw' => $userAgent,
            'parsed' => true,
        ];
    }
}
