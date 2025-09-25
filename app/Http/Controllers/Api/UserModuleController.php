<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ModuleService;
use App\Models\Module;
use App\Models\UserModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserModuleController extends Controller
{
    protected $moduleService;

    public function __construct(ModuleService $moduleService)
    {
        $this->moduleService = $moduleService;
    }
    /**
     * Display a listing of the user's modules.
     */
    public function index()
    {
        try {
            $userModules = $this->moduleService->getUserModules(Auth::id());

            return response()->json([
                'success' => true,
                'data' => $userModules,
                'message' => 'User modules retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user modules',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get active modules for the user.
     */
    public function getActive()
    {
        try {
            $activeModules = $this->moduleService->getUserActiveModules(Auth::id());

            return response()->json([
                'success' => true,
                'data' => $activeModules,
                'message' => 'Active modules retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve active modules',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Activate a module for the user.
     */
    public function activate(Request $request)
    {
        $request->validate([
            'module_id' => 'required|exists:modules,id',
            'configuration' => 'sometimes|array'
        ]);

        try {
            $configuration = $request->input('configuration', []);
            $userModule = $this->moduleService->activateModule(
                Auth::id(),
                $request->module_id,
                $configuration
            );

            return response()->json([
                'success' => true,
                'data' => $userModule,
                'message' => 'Module activated successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Deactivate a module for the user.
     */
    public function deactivate(Module $module)
    {
        try {
            $userModule = $this->moduleService->deactivateModule(Auth::id(), $module->id);

            return response()->json([
                'success' => true,
                'data' => $userModule,
                'message' => 'Module deactivated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update module configuration.
     */
    public function updateConfiguration(Request $request, Module $module)
    {
        $request->validate([
            'configuration' => 'required|array'
        ]);

        try {
            $userModule = $this->moduleService->updateModuleConfiguration(
                Auth::id(),
                $module->id,
                $request->configuration
            );

            return response()->json([
                'success' => true,
                'data' => $userModule,
                'message' => 'Module configuration updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Bulk activate modules
     */
    public function bulkActivate(Request $request)
    {
        $request->validate([
            'module_ids' => 'required|array|min:1',
            'module_ids.*' => 'exists:modules,id',
            'configurations' => 'sometimes|array'
        ]);

        try {
            $results = $this->moduleService->bulkActivateModules(
                Auth::id(),
                $request->module_ids,
                $request->input('configurations', [])
            );

            return response()->json([
                'success' => true,
                'data' => $results,
                'message' => 'Bulk activation completed'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to bulk activate modules',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
