<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\SecurityEvent;
use App\Models\User;
use Carbon\Carbon;

class SuspiciousActivityDetection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only check for authenticated users
        if ($request->user()) {
            $this->checkSuspiciousActivity($request);
        }

        return $response;
    }

    /**
     * Check for suspicious activity
     */
    private function checkSuspiciousActivity(Request $request)
    {
        try {
            $user = $request->user();
            $ipAddress = $request->ip();
            $userAgent = $request->userAgent();

            // Check for multiple failed login attempts
            $this->checkFailedLoginAttempts($user, $ipAddress);

            // Check for unusual location
            $this->checkUnusualLocation($user, $ipAddress);

            // Check for unusual time
            $this->checkUnusualTime($user);

            // Check for multiple sessions from different locations
            $this->checkMultipleLocations($user, $ipAddress);
        } catch (\Exception $e) {
            \Log::error('Failed to check suspicious activity: ' . $e->getMessage());
        }
    }

    /**
     * Check for multiple failed login attempts
     */
    private function checkFailedLoginAttempts(User $user, string $ipAddress)
    {
        $recentFailures = SecurityEvent::where('user_id', $user->id)
            ->where('event_type', 'login_failed')
            ->where('ip_address', $ipAddress)
            ->where('created_at', '>', now()->subMinutes(15))
            ->count();

        if ($recentFailures >= 5) {
            $this->logSecurityEvent(
                $user->id,
                'multiple_failed_logins',
                'high',
                'Multiple failed login attempts detected',
                ['ip' => $ipAddress, 'attempts' => $recentFailures]
            );

            // Optionally lock the account temporarily
            if ($recentFailures >= 10) {
                $user->update(['is_active' => false]);
                $this->logSecurityEvent(
                    $user->id,
                    'account_locked',
                    'critical',
                    'Account locked due to multiple failed login attempts',
                    ['ip' => $ipAddress, 'attempts' => $recentFailures]
                );
            }
        }
    }

    /**
     * Check for unusual location
     */
    private function checkUnusualLocation(User $user, string $ipAddress)
    {
        // Get recent login locations
        $recentLogins = SecurityEvent::where('user_id', $user->id)
            ->where('event_type', 'login_success')
            ->where('created_at', '>', now()->subDays(7))
            ->pluck('ip_address')
            ->unique()
            ->toArray();

        // If this is a new IP and user has logged in from other IPs recently
        if (!in_array($ipAddress, $recentLogins) && count($recentLogins) > 0) {
            $this->logSecurityEvent(
                $user->id,
                'unusual_location',
                'medium',
                'Login from unusual location detected',
                ['ip' => $ipAddress, 'previous_ips' => $recentLogins]
            );
        }
    }

    /**
     * Check for unusual login time
     */
    private function checkUnusualTime(User $user)
    {
        $currentHour = now()->hour;

        // Check if login is outside normal business hours (6 AM - 10 PM)
        if ($currentHour < 6 || $currentHour > 22) {
            $this->logSecurityEvent(
                $user->id,
                'unusual_time',
                'low',
                'Login outside normal business hours',
                ['hour' => $currentHour]
            );
        }
    }

    /**
     * Check for multiple sessions from different locations
     */
    private function checkMultipleLocations(User $user, string $ipAddress)
    {
        $activeSessions = $user->userSessions()
            ->where('is_active', true)
            ->where('ip_address', '!=', $ipAddress)
            ->count();

        if ($activeSessions >= 3) {
            $this->logSecurityEvent(
                $user->id,
                'multiple_locations',
                'medium',
                'Multiple active sessions from different locations',
                ['current_ip' => $ipAddress, 'active_sessions' => $activeSessions]
            );
        }
    }

    /**
     * Log security event
     */
    private function logSecurityEvent($userId, $eventType, $severity, $description, $metadata = [])
    {
        try {
            SecurityEvent::create([
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
