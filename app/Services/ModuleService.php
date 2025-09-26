<?php

namespace App\Services;

use App\Models\Module;
use App\Models\BusinessType;
use App\Models\UserModule;
use Illuminate\Support\Facades\DB;

class ModuleService
{
    /**
     * Get all modules
     */
    public function getAllModules()
    {
        return Module::with(['recommendedForBusinessTypes'])
            ->orderBy('name')
            ->get();
    }

    /**
     * Get modules by category
     */
    public function getModulesByCategory($category)
    {
        return Module::where('category', $category)
            ->with(['recommendedForBusinessTypes'])
            ->orderBy('name')
            ->get();
    }

    /**
     * Get all module categories
     */
    public function getModuleCategories()
    {
        return Module::select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');
    }

    /**
     * Get modules for business type with recommendations
     */
    public function getModulesForBusinessType($businessTypeId)
    {
        $businessType = BusinessType::findOrFail($businessTypeId);

        $recommendedModules = Module::whereHas('recommendedForBusinessTypes', function ($query) use ($businessTypeId) {
            $query->where('business_type_id', $businessTypeId);
        })->get();

        $allModules = Module::with(['recommendedForBusinessTypes'])->get();

        return [
            'business_type' => $businessType,
            'recommended_modules' => $recommendedModules,
            'all_modules' => $allModules,
            'total_recommended' => $recommendedModules->count(),
            'total_available' => $allModules->count()
        ];
    }

    /**
     * Get module statistics
     */
    public function getModuleStatistics($userId = null)
    {
        $totalModules = Module::count();
        $activeModules = UserModule::where('is_active', true)->count();

        $categoryStats = Module::select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->get();

        $businessTypeStats = BusinessType::withCount('modules')->get();

        return [
            'total_modules' => $totalModules,
            'active_modules' => $activeModules,
            'category_statistics' => $categoryStats,
            'business_type_statistics' => $businessTypeStats,
            'user_active_modules' => $userId ? UserModule::where('user_id', $userId)->where('is_active', true)->count() : 0
        ];
    }
}
