<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\UserModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserModuleController extends Controller
{
    /**
     * Display a listing of the user's modules.
     */
    public function index()
    {
        $userModules = Auth::user()->userModules()
            ->with('module')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $userModules,
            'message' => 'User modules retrieved successfully'
        ]);
    }

    /**
     * Get active modules for the user.
     */
    public function getActive()
    {
        $activeModules = Auth::user()->activeModules()
            ->with('module')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $activeModules,
            'message' => 'Active modules retrieved successfully'
        ]);
    }

    /**
     * Activate a module for the user.
     */
    public function activate(Request $request)
    {
        $request->validate([
            'module_id' => 'required|exists:modules,id'
        ]);

        $module = Module::findOrFail($request->module_id);

        // Check if module is already activated
        $existingModule = Auth::user()->userModules()
            ->where('module_id', $request->module_id)
            ->first();

        if ($existingModule) {
            if ($existingModule->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Module is already activated'
                ], 400);
            } else {
                $existingModule->activate();
                return response()->json([
                    'success' => true,
                    'data' => $existingModule->load('module'),
                    'message' => 'Module activated successfully'
                ]);
            }
        }

        // Create new user module
        $userModule = Auth::user()->userModules()->create([
            'module_id' => $request->module_id,
            'is_active' => true,
            'activated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'data' => $userModule->load('module'),
            'message' => 'Module activated successfully'
        ], 201);
    }

    /**
     * Deactivate a module for the user.
     */
    public function deactivate(Module $module)
    {
        $userModule = Auth::user()->userModules()
            ->where('module_id', $module->id)
            ->first();

        if (!$userModule) {
            return response()->json([
                'success' => false,
                'message' => 'Module not found for user'
            ], 404);
        }

        if (!$userModule->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Module is already deactivated'
            ], 400);
        }

        $userModule->deactivate();

        return response()->json([
            'success' => true,
            'data' => $userModule->load('module'),
            'message' => 'Module deactivated successfully'
        ]);
    }

    /**
     * Update module configuration.
     */
    public function updateConfiguration(Request $request, Module $module)
    {
        $request->validate([
            'configuration' => 'required|array'
        ]);

        $userModule = Auth::user()->userModules()
            ->where('module_id', $module->id)
            ->first();

        if (!$userModule) {
            return response()->json([
                'success' => false,
                'message' => 'Module not found for user'
            ], 404);
        }

        $userModule->updateConfiguration($request->configuration);

        return response()->json([
            'success' => true,
            'data' => $userModule->load('module'),
            'message' => 'Module configuration updated successfully'
        ]);
    }
}
