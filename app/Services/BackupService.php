<?php

namespace App\Services;

use App\Models\User;
use App\Models\BusinessType;
use App\Models\Module;
use App\Models\Role;
use App\Models\Permission;
use App\Models\ActivityLog;
use App\Models\SecurityEvent;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BackupService
{
    /**
     * Create full system backup
     */
    public function createFullBackup(): array
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $backupKey = app(EncryptionService::class)->generateBackupKey();

        $backup = [
            'metadata' => [
                'created_at' => now()->toISOString(),
                'backup_key' => $backupKey,
                'version' => '1.0',
                'type' => 'full_backup',
            ],
            'data' => [
                'users' => $this->backupUsers(),
                'business_types' => $this->backupBusinessTypes(),
                'modules' => $this->backupModules(),
                'roles' => $this->backupRoles(),
                'permissions' => $this->backupPermissions(),
                'activity_logs' => $this->backupActivityLogs(),
                'security_events' => $this->backupSecurityEvents(),
            ]
        ];

        // Encrypt backup data
        $encryptedBackup = app(EncryptionService::class)->encryptBackupData($backup);

        // Store backup
        $filename = "backup_full_{$timestamp}.json";
        Storage::disk('local')->put("backups/{$filename}", $encryptedBackup);

        return [
            'success' => true,
            'filename' => $filename,
            'backup_key' => $backupKey,
            'size' => strlen($encryptedBackup),
            'created_at' => $backup['metadata']['created_at'],
        ];
    }

    /**
     * Create incremental backup
     */
    public function createIncrementalBackup(Carbon $since): array
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $backupKey = app(EncryptionService::class)->generateBackupKey();

        $backup = [
            'metadata' => [
                'created_at' => now()->toISOString(),
                'backup_key' => $backupKey,
                'version' => '1.0',
                'type' => 'incremental_backup',
                'since' => $since->toISOString(),
            ],
            'data' => [
                'users' => $this->backupUsers($since),
                'business_types' => $this->backupBusinessTypes($since),
                'modules' => $this->backupModules($since),
                'roles' => $this->backupRoles($since),
                'permissions' => $this->backupPermissions($since),
                'activity_logs' => $this->backupActivityLogs($since),
                'security_events' => $this->backupSecurityEvents($since),
            ]
        ];

        // Encrypt backup data
        $encryptedBackup = app(EncryptionService::class)->encryptBackupData($backup);

        // Store backup
        $filename = "backup_incremental_{$timestamp}.json";
        Storage::disk('local')->put("backups/{$filename}", $encryptedBackup);

        return [
            'success' => true,
            'filename' => $filename,
            'backup_key' => $backupKey,
            'size' => strlen($encryptedBackup),
            'created_at' => $backup['metadata']['created_at'],
        ];
    }

    /**
     * Restore from backup
     */
    public function restoreFromBackup(string $filename, string $backupKey): array
    {
        try {
            // Read backup file
            $encryptedBackup = Storage::disk('local')->get("backups/{$filename}");

            // Decrypt backup data
            $backup = app(EncryptionService::class)->decryptBackupData($encryptedBackup);

            // Verify backup key
            if ($backup['metadata']['backup_key'] !== $backupKey) {
                return [
                    'success' => false,
                    'message' => 'Invalid backup key'
                ];
            }

            // Start database transaction
            DB::beginTransaction();

            try {
                // Restore data
                $this->restoreUsers($backup['data']['users']);
                $this->restoreBusinessTypes($backup['data']['business_types']);
                $this->restoreModules($backup['data']['modules']);
                $this->restoreRoles($backup['data']['roles']);
                $this->restorePermissions($backup['data']['permissions']);
                $this->restoreActivityLogs($backup['data']['activity_logs']);
                $this->restoreSecurityEvents($backup['data']['security_events']);

                DB::commit();

                return [
                    'success' => true,
                    'message' => 'Backup restored successfully',
                    'restored_at' => now()->toISOString(),
                ];
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to restore backup: ' . $e->getMessage()
            ];
        }
    }

    /**
     * List available backups
     */
    public function listBackups(): array
    {
        $files = Storage::disk('local')->files('backups');
        $backups = [];

        foreach ($files as $file) {
            if (str_ends_with($file, '.json')) {
                $backups[] = [
                    'filename' => basename($file),
                    'size' => Storage::disk('local')->size($file),
                    'created_at' => Storage::disk('local')->lastModified($file),
                ];
            }
        }

        return $backups;
    }

    /**
     * Delete backup
     */
    public function deleteBackup(string $filename): bool
    {
        return Storage::disk('local')->delete("backups/{$filename}");
    }

    /**
     * Backup users
     */
    private function backupUsers(?Carbon $since = null): array
    {
        $query = User::query();

        if ($since) {
            $query->where('updated_at', '>=', $since);
        }

        return $query->get()->toArray();
    }

    /**
     * Backup business types
     */
    private function backupBusinessTypes(?Carbon $since = null): array
    {
        $query = BusinessType::query();

        if ($since) {
            $query->where('updated_at', '>=', $since);
        }

        return $query->get()->toArray();
    }

    /**
     * Backup modules
     */
    private function backupModules(?Carbon $since = null): array
    {
        $query = Module::query();

        if ($since) {
            $query->where('updated_at', '>=', $since);
        }

        return $query->get()->toArray();
    }

    /**
     * Backup roles
     */
    private function backupRoles(?Carbon $since = null): array
    {
        $query = Role::query();

        if ($since) {
            $query->where('updated_at', '>=', $since);
        }

        return $query->get()->toArray();
    }

    /**
     * Backup permissions
     */
    private function backupPermissions(?Carbon $since = null): array
    {
        $query = Permission::query();

        if ($since) {
            $query->where('updated_at', '>=', $since);
        }

        return $query->get()->toArray();
    }

    /**
     * Backup activity logs
     */
    private function backupActivityLogs(?Carbon $since = null): array
    {
        $query = ActivityLog::query();

        if ($since) {
            $query->where('created_at', '>=', $since);
        }

        return $query->get()->toArray();
    }

    /**
     * Backup security events
     */
    private function backupSecurityEvents(?Carbon $since = null): array
    {
        $query = SecurityEvent::query();

        if ($since) {
            $query->where('created_at', '>=', $since);
        }

        return $query->get()->toArray();
    }

    /**
     * Restore users
     */
    private function restoreUsers(array $users): void
    {
        foreach ($users as $userData) {
            User::updateOrCreate(
                ['id' => $userData['id']],
                $userData
            );
        }
    }

    /**
     * Restore business types
     */
    private function restoreBusinessTypes(array $businessTypes): void
    {
        foreach ($businessTypes as $businessTypeData) {
            BusinessType::updateOrCreate(
                ['id' => $businessTypeData['id']],
                $businessTypeData
            );
        }
    }

    /**
     * Restore modules
     */
    private function restoreModules(array $modules): void
    {
        foreach ($modules as $moduleData) {
            Module::updateOrCreate(
                ['id' => $moduleData['id']],
                $moduleData
            );
        }
    }

    /**
     * Restore roles
     */
    private function restoreRoles(array $roles): void
    {
        foreach ($roles as $roleData) {
            Role::updateOrCreate(
                ['id' => $roleData['id']],
                $roleData
            );
        }
    }

    /**
     * Restore permissions
     */
    private function restorePermissions(array $permissions): void
    {
        foreach ($permissions as $permissionData) {
            Permission::updateOrCreate(
                ['id' => $permissionData['id']],
                $permissionData
            );
        }
    }

    /**
     * Restore activity logs
     */
    private function restoreActivityLogs(array $activityLogs): void
    {
        foreach ($activityLogs as $logData) {
            ActivityLog::updateOrCreate(
                ['id' => $logData['id']],
                $logData
            );
        }
    }

    /**
     * Restore security events
     */
    private function restoreSecurityEvents(array $securityEvents): void
    {
        foreach ($securityEvents as $eventData) {
            SecurityEvent::updateOrCreate(
                ['id' => $eventData['id']],
                $eventData
            );
        }
    }
}
