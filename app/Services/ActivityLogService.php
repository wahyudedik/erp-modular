<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ActivityLogService
{
    /**
     * Log user activity.
     */
    public function log(
        User $user,
        string $action,
        string $description = null,
        Model $model = null,
        array $properties = [],
        Request $request = null
    ): ActivityLog {
        return ActivityLog::create([
            'user_id' => $user->id,
            'action' => $action,
            'description' => $description,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->id : null,
            'properties' => $properties,
            'ip_address' => $request ? $request->ip() : null,
            'user_agent' => $request ? $request->userAgent() : null,
        ]);
    }

    /**
     * Log user login.
     */
    public function logLogin(User $user, Request $request = null): ActivityLog
    {
        return $this->log($user, 'login', null, null, [], $request);
    }

    /**
     * Log user logout.
     */
    public function logLogout(User $user, Request $request = null): ActivityLog
    {
        return $this->log($user, 'logout', null, null, [], $request);
    }

    /**
     * Log model creation.
     */
    public function logCreated(User $user, Model $model, Request $request = null): ActivityLog
    {
        return $this->log(
            $user,
            'created',
            "Created {$this->getModelName($model)}",
            $model,
            ['model_data' => $model->toArray()],
            $request
        );
    }

    /**
     * Log model update.
     */
    public function logUpdated(User $user, Model $model, array $oldData = [], Request $request = null): ActivityLog
    {
        return $this->log(
            $user,
            'updated',
            "Updated {$this->getModelName($model)}",
            $model,
            [
                'old_data' => $oldData,
                'new_data' => $model->toArray(),
                'changes' => $this->getChanges($oldData, $model->toArray())
            ],
            $request
        );
    }

    /**
     * Log model deletion.
     */
    public function logDeleted(User $user, Model $model, Request $request = null): ActivityLog
    {
        return $this->log(
            $user,
            'deleted',
            "Deleted {$this->getModelName($model)}",
            $model,
            ['model_data' => $model->toArray()],
            $request
        );
    }

    /**
     * Log module activation.
     */
    public function logModuleActivated(User $user, string $moduleName, Request $request = null): ActivityLog
    {
        return $this->log(
            $user,
            'module_activated',
            "Activated module: {$moduleName}",
            null,
            ['module_name' => $moduleName],
            $request
        );
    }

    /**
     * Log module deactivation.
     */
    public function logModuleDeactivated(User $user, string $moduleName, Request $request = null): ActivityLog
    {
        return $this->log(
            $user,
            'module_deactivated',
            "Deactivated module: {$moduleName}",
            null,
            ['module_name' => $moduleName],
            $request
        );
    }

    /**
     * Get user activity logs.
     */
    public function getUserActivity(User $user, int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return ActivityLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent activity logs.
     */
    public function getRecentActivity(int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get model name for logging.
     */
    private function getModelName(Model $model): string
    {
        $className = class_basename(get_class($model));
        return strtolower(preg_replace('/(?<!^)[A-Z]/', ' $0', $className));
    }

    /**
     * Get changes between old and new data.
     */
    private function getChanges(array $oldData, array $newData): array
    {
        $changes = [];

        foreach ($newData as $key => $value) {
            if (!isset($oldData[$key]) || $oldData[$key] !== $value) {
                $changes[$key] = [
                    'old' => $oldData[$key] ?? null,
                    'new' => $value
                ];
            }
        }

        return $changes;
    }
}
