<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Module::active();

        // Filter by category if provided
        if ($request->has('category')) {
            $query->byCategory($request->category);
        }

        // Filter by core modules
        if ($request->has('core')) {
            $query->where('is_core', $request->boolean('core'));
        }

        $modules = $query->ordered()->get();

        return response()->json([
            'success' => true,
            'data' => $modules,
            'message' => 'Modules retrieved successfully'
        ]);
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
        $modules = Module::active()
            ->byCategory($category)
            ->ordered()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $modules,
            'message' => "Modules for category '{$category}' retrieved successfully"
        ]);
    }

    /**
     * Get all module categories.
     */
    public function getCategories()
    {
        $categories = Module::getCategories();

        return response()->json([
            'success' => true,
            'data' => $categories,
            'message' => 'Module categories retrieved successfully'
        ]);
    }
}
