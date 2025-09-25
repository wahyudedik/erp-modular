<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusinessType;
use Illuminate\Http\Request;

class BusinessTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $businessTypes = BusinessType::active()
            ->ordered()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $businessTypes,
            'message' => 'Business types retrieved successfully'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(BusinessType $businessType)
    {
        $businessType->load('recommendedModules');

        return response()->json([
            'success' => true,
            'data' => $businessType,
            'message' => 'Business type retrieved successfully'
        ]);
    }

    /**
     * Get module recommendations for a business type.
     */
    public function getModuleRecommendations(BusinessType $businessType)
    {
        $recommendations = $businessType->moduleRecommendations()
            ->with('module')
            ->orderedByPriority()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $recommendations,
            'message' => 'Module recommendations retrieved successfully'
        ]);
    }
}
