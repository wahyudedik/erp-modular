<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RequireReauth
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
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        // Check if re-authentication is required
        $reauthRequired = $this->isReauthRequired($request);

        if ($reauthRequired) {
            // Check for re-authentication token or password confirmation
            $reauthToken = $request->header('X-Reauth-Token');
            $password = $request->input('password');

            if (!$reauthToken && !$password) {
                return response()->json([
                    'success' => false,
                    'message' => 'Re-authentication required for this action',
                    'requires_reauth' => true
                ], 403);
            }

            // Verify re-authentication
            if ($reauthToken) {
                if (!$this->verifyReauthToken($user, $reauthToken)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid re-authentication token'
                    ], 403);
                }
            } elseif ($password) {
                if (!Hash::check($password, $user->password)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid password'
                    ], 403);
                }
            }
        }

        return $next($request);
    }

    /**
     * Check if re-authentication is required for this request
     */
    private function isReauthRequired(Request $request): bool
    {
        $sensitiveActions = [
            'change-password',
            'change-email',
            'delete-account',
            'disable-2fa',
            'transfer-ownership',
            'financial-transactions',
        ];

        $path = $request->path();

        foreach ($sensitiveActions as $action) {
            if (str_contains($path, $action)) {
                return true;
            }
        }

        // Check for sensitive HTTP methods
        if (in_array($request->method(), ['DELETE', 'PATCH', 'PUT'])) {
            $sensitivePaths = [
                'users',
                'roles',
                'permissions',
                'account',
                'billing',
            ];

            foreach ($sensitivePaths as $sensitivePath) {
                if (str_contains($path, $sensitivePath)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Verify re-authentication token
     */
    private function verifyReauthToken($user, string $token): bool
    {
        // Check if token exists in session or cache
        $sessionKey = "reauth_token_{$user->id}";
        $storedToken = session($sessionKey);

        if (!$storedToken || $storedToken !== $token) {
            return false;
        }

        // Check if token is not expired (valid for 15 minutes)
        $tokenTime = session("reauth_time_{$user->id}");
        if (!$tokenTime || now()->diffInMinutes($tokenTime) > 15) {
            return false;
        }

        return true;
    }
}
