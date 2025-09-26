<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\UserSession;
use App\Models\SecurityEvent;

class TrackUserSession
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

        // Only track for authenticated users
        if ($request->user()) {
            $this->trackSession($request);
        }

        return $response;
    }

    /**
     * Track user session
     */
    private function trackSession(Request $request)
    {
        try {
            $user = $request->user();
            $token = $request->user()->currentAccessToken();

            if (!$token) {
                return;
            }

            // Get device information
            $userAgent = $request->userAgent();
            $deviceInfo = $this->parseUserAgent($userAgent);

            // Check if session already exists
            $session = UserSession::where('user_id', $user->id)
                ->where('session_id', $token->id)
                ->first();

            if ($session) {
                // Update existing session
                $session->update([
                    'last_activity' => now(),
                    'ip_address' => $request->ip(),
                ]);
            } else {
                // Create new session
                UserSession::create([
                    'user_id' => $user->id,
                    'session_id' => $token->id,
                    'device_name' => $deviceInfo['device_name'],
                    'device_type' => $deviceInfo['device_type'],
                    'browser' => $deviceInfo['browser'],
                    'os' => $deviceInfo['os'],
                    'ip_address' => $request->ip(),
                    'is_active' => true,
                    'is_current' => true,
                    'last_activity' => now(),
                ]);

                // Mark other sessions as not current
                UserSession::where('user_id', $user->id)
                    ->where('session_id', '!=', $token->id)
                    ->update(['is_current' => false]);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to track user session: ' . $e->getMessage());
        }
    }

    /**
     * Parse user agent string
     */
    private function parseUserAgent($userAgent)
    {
        $deviceType = 'desktop';
        $browser = 'Unknown';
        $os = 'Unknown';
        $deviceName = 'Unknown Device';

        // Detect mobile devices
        if (preg_match('/Mobile|Android|iPhone|iPad/', $userAgent)) {
            $deviceType = 'mobile';
        } elseif (preg_match('/Tablet|iPad/', $userAgent)) {
            $deviceType = 'tablet';
        }

        // Detect browser
        if (preg_match('/Chrome\/([0-9.]+)/', $userAgent, $matches)) {
            $browser = 'Chrome ' . $matches[1];
        } elseif (preg_match('/Firefox\/([0-9.]+)/', $userAgent, $matches)) {
            $browser = 'Firefox ' . $matches[1];
        } elseif (preg_match('/Safari\/([0-9.]+)/', $userAgent, $matches)) {
            $browser = 'Safari ' . $matches[1];
        } elseif (preg_match('/Edge\/([0-9.]+)/', $userAgent, $matches)) {
            $browser = 'Edge ' . $matches[1];
        }

        // Detect OS
        if (preg_match('/Windows NT ([0-9.]+)/', $userAgent, $matches)) {
            $os = 'Windows ' . $matches[1];
        } elseif (preg_match('/Mac OS X ([0-9_]+)/', $userAgent, $matches)) {
            $os = 'macOS ' . str_replace('_', '.', $matches[1]);
        } elseif (preg_match('/Linux/', $userAgent)) {
            $os = 'Linux';
        } elseif (preg_match('/Android ([0-9.]+)/', $userAgent, $matches)) {
            $os = 'Android ' . $matches[1];
        } elseif (preg_match('/iPhone OS ([0-9_]+)/', $userAgent, $matches)) {
            $os = 'iOS ' . str_replace('_', '.', $matches[1]);
        }

        // Create device name
        $deviceName = $deviceType . ' - ' . $browser . ' on ' . $os;

        return [
            'device_name' => $deviceName,
            'device_type' => $deviceType,
            'browser' => $browser,
            'os' => $os,
        ];
    }
}
