<?php

namespace App\Services;

use App\Models\Module;
use App\Models\UserModule;
use App\Models\BusinessType;
use App\Models\ModuleRecommendation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ModuleService
{
    /**
     * Get all available modules
     */
    public function getAllModules()
    {
        return Module::with(['permissions', 'recommendations'])
            ->orderBy('category')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get modules by category
     */
    public function getModulesByCategory($category)
    {
        return Module::where('category', $category)
            ->with(['permissions', 'recommendations'])
            ->orderBy('name')
            ->get();
    }

    /**
     * Get recommended modules for a business type
     */
    public function getRecommendedModules($businessTypeId)
    {
        return ModuleRecommendation::where('business_type_id', $businessTypeId)
            ->with(['module.permissions'])
            ->orderBy('priority', 'desc')
            ->orderBy('module.name')
            ->get()
            ->pluck('module');
    }

    /**
     * Get user's active modules
     */
    public function getUserActiveModules($userId)
    {
        return UserModule::where('user_id', $userId)
            ->where('is_active', true)
            ->with(['module.permissions'])
            ->orderBy('activated_at', 'desc')
            ->get();
    }

    /**
     * Get user's all modules (active and inactive)
     */
    public function getUserModules($userId)
    {
        return UserModule::where('user_id', $userId)
            ->with(['module.permissions'])
            ->orderBy('is_active', 'desc')
            ->orderBy('activated_at', 'desc')
            ->get();
    }

    /**
     * Activate a module for user
     */
    public function activateModule($userId, $moduleId, $configuration = [])
    {
        try {
            DB::beginTransaction();

            // Check if module exists
            $module = Module::findOrFail($moduleId);

            // Check if user already has this module
            $existingUserModule = UserModule::where('user_id', $userId)
                ->where('module_id', $moduleId)
                ->first();

            if ($existingUserModule) {
                if ($existingUserModule->is_active) {
                    throw new \Exception('Module is already active');
                }

                // Reactivate the module
                $existingUserModule->update([
                    'is_active' => true,
                    'activated_at' => now(),
                    'configuration' => $configuration
                ]);

                $userModule = $existingUserModule;
            } else {
                // Create new user module
                $userModule = UserModule::create([
                    'user_id' => $userId,
                    'module_id' => $moduleId,
                    'is_active' => true,
                    'activated_at' => now(),
                    'configuration' => $configuration
                ]);
            }

            // Log the activation
            Log::info("Module activated", [
                'user_id' => $userId,
                'module_id' => $moduleId,
                'module_name' => $module->name
            ]);

            DB::commit();

            return $userModule->load(['module.permissions']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to activate module", [
                'user_id' => $userId,
                'module_id' => $moduleId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Deactivate a module for user
     */
    public function deactivateModule($userId, $moduleId)
    {
        try {
            DB::beginTransaction();

            $userModule = UserModule::where('user_id', $userId)
                ->where('module_id', $moduleId)
                ->where('is_active', true)
                ->firstOrFail();

            $userModule->update([
                'is_active' => false,
                'deactivated_at' => now()
            ]);

            // Log the deactivation
            Log::info("Module deactivated", [
                'user_id' => $userId,
                'module_id' => $moduleId,
                'module_name' => $userModule->module->name
            ]);

            DB::commit();

            return $userModule->load(['module.permissions']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to deactivate module", [
                'user_id' => $userId,
                'module_id' => $moduleId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update module configuration
     */
    public function updateModuleConfiguration($userId, $moduleId, $configuration)
    {
        $userModule = UserModule::where('user_id', $userId)
            ->where('module_id', $moduleId)
            ->where('is_active', true)
            ->firstOrFail();

        $userModule->update([
            'configuration' => $configuration,
            'updated_at' => now()
        ]);

        Log::info("Module configuration updated", [
            'user_id' => $userId,
            'module_id' => $moduleId,
            'configuration' => $configuration
        ]);

        return $userModule->load(['module.permissions']);
    }

    /**
     * Check if user has permission for a module
     */
    public function hasModulePermission($userId, $moduleId, $permission = null)
    {
        $userModule = UserModule::where('user_id', $userId)
            ->where('module_id', $moduleId)
            ->where('is_active', true)
            ->with(['module.permissions'])
            ->first();

        if (!$userModule) {
            return false;
        }

        // If no specific permission requested, just check if module is active
        if (!$permission) {
            return true;
        }

        // Check specific permission
        $modulePermission = $userModule->module->permissions
            ->where('name', $permission)
            ->first();

        return $modulePermission ? true : false;
    }

    /**
     * Get module categories
     */
    public function getModuleCategories()
    {
        return Module::select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');
    }

    /**
     * Get modules by business type with recommendations
     */
    public function getModulesForBusinessType($businessTypeId)
    {
        $businessType = BusinessType::findOrFail($businessTypeId);
        
        $recommendedModules = $this->getRecommendedModules($businessTypeId);
        $allModules = $this->getAllModules();
        
        return [
            'business_type' => $businessType,
            'recommended' => $recommendedModules,
            'all_modules' => $allModules,
            'categories' => $this->getModuleCategories()
        ];
    }

    /**
     * Bulk activate modules
     */
    public function bulkActivateModules($userId, $moduleIds, $configurations = [])
    {
        $results = [];
        
        foreach ($moduleIds as $moduleId) {
            try {
                $configuration = $configurations[$moduleId] ?? [];
                $userModule = $this->activateModule($userId, $moduleId, $configuration);
                $results[] = [
                    'module_id' => $moduleId,
                    'success' => true,
                    'user_module' => $userModule
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'module_id' => $moduleId,
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }
        
        return $results;
    }

    /**
     * Get module statistics
     */
    public function getModuleStatistics($userId = null)
    {
        $query = UserModule::query();
        
        if ($userId) {
            $query->where('user_id', $userId);
        }
        
        $activeModules = $query->where('is_active', true)->count();
        $totalModules = Module::count();
        
        $categoryStats = DB::table('user_modules')
            ->join('modules', 'user_modules.module_id', '=', 'modules.id')
            ->where('user_modules.is_active', true)
            ->when($userId, function ($q) use ($userId) {
                return $q->where('user_modules.user_id', $userId);
            })
            ->select('modules.category', DB::raw('COUNT(*) as count'))
            ->groupBy('modules.category')
            ->get();
        
        return [
            'active_modules' => $activeModules,
            'total_modules' => $totalModules,
            'coverage_percentage' => $totalModules > 0 ? round(($activeModules / $totalModules) * 100, 2) : 0,
            'category_stats' => $categoryStats
        ];
    }
}
