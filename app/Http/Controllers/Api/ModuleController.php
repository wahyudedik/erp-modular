<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ModuleService;
use App\Models\Module;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    protected $moduleService;

    public function __construct(ModuleService $moduleService)
    {
        $this->moduleService = $moduleService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if ($request->has('category')) {
                $modules = $this->moduleService->getModulesByCategory($request->category);
            } else {
                $modules = $this->moduleService->getAllModules();
            }

            return response()->json([
                'success' => true,
                'data' => $modules,
                'meta' => [
                    'total' => $modules->count(),
                    'categories' => $this->moduleService->getModuleCategories()
                ],
                'message' => 'Modules retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve modules',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Module $module)
    {
        return response()->json([
            'success' => true,
            'data' => $module,
            'message' => 'Module retrieved successfully'
        ]);
    }

    /**
     * Get modules by category.
     */
    public function getByCategory($category)
    {
        try {
            $modules = $this->moduleService->getModulesByCategory($category);

            return response()->json([
                'success' => true,
                'data' => $modules,
                'meta' => [
                    'category' => $category,
                    'total' => $modules->count()
                ],
                'message' => "Modules for category '{$category}' retrieved successfully"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve modules by category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all module categories.
     */
    public function getCategories()
    {
        try {
            $categories = $this->moduleService->getModuleCategories();

            return response()->json([
                'success' => true,
                'data' => $categories,
                'message' => 'Module categories retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get modules for business type with recommendations
     */
    public function getForBusinessType($businessTypeId)
    {
        try {
            $data = $this->moduleService->getModulesForBusinessType($businessTypeId);
            
            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Modules for business type retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve modules for business type',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get module statistics
     */
    public function getStatistics(Request $request)
    {
        try {
            $userId = $request->user()?->id;
            $statistics = $this->moduleService->getModuleStatistics($userId);
            
            return response()->json([
                'success' => true,
                'data' => $statistics,
                'message' => 'Module statistics retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve module statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
