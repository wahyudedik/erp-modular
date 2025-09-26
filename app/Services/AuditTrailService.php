<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AuditTrailService
{
    /**
     * Log user activity
     */
    public function logActivity(
        string $eventType,
        string $description,
        ?User $user = null,
        ?Model $model = null,
        array $properties = [],
        ?Request $request = null
    ): ActivityLog {
        $request = $request ?? request();

        return ActivityLog::create([
            'user_id' => $user?->id,
            'event_type' => $eventType,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->id,
            'description' => $description,
            'properties' => $properties,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'country' => $this->getCountryFromIP($request->ip()),
            'city' => $this->getCityFromIP($request->ip()),
        ]);
    }

    /**
     * Log model creation
     */
    public function logCreated(Model $model, ?User $user = null, array $properties = []): ActivityLog
    {
        return $this->logActivity(
            'created',
            "Created {$this->getModelName($model)}",
            $user,
            $model,
            $properties
        );
    }

    /**
     * Log model update
     */
    public function logUpdated(Model $model, ?User $user = null, array $oldValues = [], array $newValues = []): ActivityLog
    {
        $changes = $this->getChanges($oldValues, $newValues);

        return $this->logActivity(
            'updated',
            "Updated {$this->getModelName($model)}",
            $user,
            $model,
            [
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'changes' => $changes,
            ]
        );
    }

    /**
     * Log model deletion
     */
    public function logDeleted(Model $model, ?User $user = null, array $properties = []): ActivityLog
    {
        return $this->logActivity(
            'deleted',
            "Deleted {$this->getModelName($model)}",
            $user,
            $model,
            $properties
        );
    }

    /**
     * Log user login
     */
    public function logLogin(User $user, array $properties = []): ActivityLog
    {
        return $this->logActivity(
            'login',
            "User logged in",
            $user,
            null,
            $properties
        );
    }

    /**
     * Log user logout
     */
    public function logLogout(User $user, array $properties = []): ActivityLog
    {
        return $this->logActivity(
            'logout',
            "User logged out",
            $user,
            null,
            $properties
        );
    }

    /**
     * Log module activation
     */
    public function logModuleActivation(User $user, string $moduleName, array $properties = []): ActivityLog
    {
        return $this->logActivity(
            'module_activated',
            "Activated module: {$moduleName}",
            $user,
            null,
            array_merge($properties, ['module' => $moduleName])
        );
    }

    /**
     * Log module deactivation
     */
    public function logModuleDeactivation(User $user, string $moduleName, array $properties = []): ActivityLog
    {
        return $this->logActivity(
            'module_deactivated',
            "Deactivated module: {$moduleName}",
            $user,
            null,
            array_merge($properties, ['module' => $moduleName])
        );
    }

    /**
     * Log permission changes
     */
    public function logPermissionChange(User $user, string $action, string $permission, array $properties = []): ActivityLog
    {
        return $this->logActivity(
            'permission_changed',
            "Permission {$action}: {$permission}",
            $user,
            null,
            array_merge($properties, ['permission' => $permission, 'action' => $action])
        );
    }

    /**
     * Log role changes
     */
    public function logRoleChange(User $user, string $action, string $role, array $properties = []): ActivityLog
    {
        return $this->logActivity(
            'role_changed',
            "Role {$action}: {$role}",
            $user,
            null,
            array_merge($properties, ['role' => $role, 'action' => $action])
        );
    }

    /**
     * Get model name for logging
     */
    private function getModelName(Model $model): string
    {
        return class_basename($model);
    }

    /**
     * Get changes between old and new values
     */
    private function getChanges(array $oldValues, array $newValues): array
    {
        $changes = [];

        foreach ($newValues as $key => $newValue) {
            $oldValue = $oldValues[$key] ?? null;
            if ($oldValue !== $newValue) {
                $changes[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return $changes;
    }

    /**
     * Get country from IP address
     */
    private function getCountryFromIP(string $ip): ?string
    {
        // TODO: Implement IP geolocation service
        return null;
    }

    /**
     * Get city from IP address
     */
    private function getCityFromIP(string $ip): ?string
    {
        // TODO: Implement IP geolocation service
        return null;
    }

    /**
     * Get activity logs for a user
     */
    public function getUserActivityLogs(User $user, int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return ActivityLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get activity logs for a model
     */
    public function getModelActivityLogs(Model $model, int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return ActivityLog::where('model_type', get_class($model))
            ->where('model_id', $model->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get system activity logs
     */
    public function getSystemActivityLogs(int $limit = 100): \Illuminate\Database\Eloquent\Collection
    {
        return ActivityLog::orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
