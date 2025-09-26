<?php

namespace App\Traits;

use App\Models\ActivityLog;
use App\Services\AuditTrailService;
use Illuminate\Database\Eloquent\Model;

trait Auditable
{
    /**
     * Boot the auditable trait
     */
    protected static function bootAuditable()
    {
        static::created(function (Model $model) {
            if (auth()->check()) {
                app(AuditTrailService::class)->logCreated($model, auth()->user());
            }
        });

        static::updated(function (Model $model) {
            if (auth()->check()) {
                $oldValues = $model->getOriginal();
                $newValues = $model->getChanges();

                app(AuditTrailService::class)->logUpdated($model, auth()->user(), $oldValues, $newValues);
            }
        });

        static::deleted(function (Model $model) {
            if (auth()->check()) {
                app(AuditTrailService::class)->logDeleted($model, auth()->user());
            }
        });
    }

    /**
     * Get activity logs for this model
     */
    public function activityLogs()
    {
        return $this->morphMany(ActivityLog::class, 'model');
    }

    /**
     * Get recent activity logs
     */
    public function getRecentActivityLogs(int $limit = 10)
    {
        return $this->activityLogs()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
