<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MixDesign;
use App\Models\MixDesignComposition;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class MixDesignController extends Controller
{
    /**
     * Display a listing of mix designs
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = MixDesign::with(['compositions', 'createdBy', 'approvedBy']);

            // Filter by strength class
            if ($request->has('class_strength')) {
                $query->where('class_strength', $request->class_strength);
            }

            // Filter by active status
            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            // Search by name or mix_id
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('mix_id', 'like', "%{$search}%");
                });
            }

            $mixDesigns = $query->orderBy('class_strength')
                ->orderBy('name')
                ->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $mixDesigns,
                'meta' => [
                    'strength_classes' => MixDesign::getStrengthClasses(),
                    'slump_classes' => MixDesign::getSlumpClasses(),
                    'exposure_classes' => MixDesign::getExposureClasses()
                ],
                'message' => 'Mix designs retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve mix designs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created mix design
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'mix_id' => 'required|string|unique:mix_designs,mix_id',
                'name' => 'required|string|max:255',
                'class_strength' => 'required|string|in:' . implode(',', array_keys(MixDesign::getStrengthClasses())),
                'target_strength' => 'required|numeric|min:0',
                'slump' => 'required|numeric|min:0',
                'durability_class' => 'nullable|string',
                'exposure_class' => 'nullable|string',
                'notes' => 'nullable|string',
                'compositions' => 'required|array|min:1',
                'compositions.*.material_type' => 'required|string',
                'compositions.*.material_name' => 'required|string',
                'compositions.*.percentage' => 'nullable|numeric|min:0|max:100',
                'compositions.*.weight_per_m3' => 'required|numeric|min:0',
                'compositions.*.unit_cost' => 'nullable|numeric|min:0',
                'compositions.*.is_admixture' => 'boolean',
                'compositions.*.is_water' => 'boolean',
                'compositions.*.is_cement' => 'boolean',
                'compositions.*.is_aggregate' => 'boolean',
                'compositions.*.aggregate_size' => 'nullable|string',
                'compositions.*.notes' => 'nullable|string'
            ]);

            $mixDesign = MixDesign::create([
                'mix_id' => $validated['mix_id'],
                'name' => $validated['name'],
                'class_strength' => $validated['class_strength'],
                'target_strength' => $validated['target_strength'],
                'slump' => $validated['slump'],
                'durability_class' => $validated['durability_class'] ?? null,
                'exposure_class' => $validated['exposure_class'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id()
            ]);

            // Create compositions
            foreach ($validated['compositions'] as $compositionData) {
                MixDesignComposition::create([
                    'mix_design_id' => $mixDesign->id,
                    'material_type' => $compositionData['material_type'],
                    'material_name' => $compositionData['material_name'],
                    'percentage' => $compositionData['percentage'] ?? null,
                    'weight_per_m3' => $compositionData['weight_per_m3'],
                    'unit_cost' => $compositionData['unit_cost'] ?? 0,
                    'is_admixture' => $compositionData['is_admixture'] ?? false,
                    'is_water' => $compositionData['is_water'] ?? false,
                    'is_cement' => $compositionData['is_cement'] ?? false,
                    'is_aggregate' => $compositionData['is_aggregate'] ?? false,
                    'aggregate_size' => $compositionData['aggregate_size'] ?? null,
                    'notes' => $compositionData['notes'] ?? null
                ]);
            }

            $mixDesign->load(['compositions', 'createdBy']);

            return response()->json([
                'success' => true,
                'data' => $mixDesign,
                'message' => 'Mix design created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create mix design',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified mix design
     */
    public function show(MixDesign $mixDesign): JsonResponse
    {
        try {
            $mixDesign->load(['compositions', 'createdBy', 'approvedBy']);

            return response()->json([
                'success' => true,
                'data' => $mixDesign,
                'message' => 'Mix design retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve mix design',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified mix design
     */
    public function update(Request $request, MixDesign $mixDesign): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'class_strength' => 'sometimes|string|in:' . implode(',', array_keys(MixDesign::getStrengthClasses())),
                'target_strength' => 'sometimes|numeric|min:0',
                'slump' => 'sometimes|numeric|min:0',
                'durability_class' => 'nullable|string',
                'exposure_class' => 'nullable|string',
                'is_active' => 'boolean',
                'notes' => 'nullable|string'
            ]);

            $mixDesign->update($validated);
            $mixDesign->load(['compositions', 'createdBy', 'approvedBy']);

            return response()->json([
                'success' => true,
                'data' => $mixDesign,
                'message' => 'Mix design updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update mix design',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified mix design
     */
    public function destroy(MixDesign $mixDesign): JsonResponse
    {
        try {
            $mixDesign->delete();

            return response()->json([
                'success' => true,
                'message' => 'Mix design deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete mix design',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate cost per cubic meter for a mix design
     */
    public function calculateCost(MixDesign $mixDesign): JsonResponse
    {
        try {
            $costPerM3 = $mixDesign->calculateCostPerM3();
            $compositions = $mixDesign->compositions()->get();

            $costBreakdown = $compositions->map(function ($composition) {
                return [
                    'material_name' => $composition->material_name,
                    'weight_per_m3' => $composition->weight_per_m3,
                    'unit_cost' => $composition->unit_cost,
                    'total_cost' => $composition->calculateCost()
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'mix_design' => $mixDesign,
                    'total_cost_per_m3' => $costPerM3,
                    'cost_breakdown' => $costBreakdown
                ],
                'message' => 'Cost calculation completed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate cost',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get mix design statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = [
                'total_mix_designs' => MixDesign::count(),
                'active_mix_designs' => MixDesign::where('is_active', true)->count(),
                'by_strength_class' => MixDesign::selectRaw('class_strength, COUNT(*) as count')
                    ->groupBy('class_strength')
                    ->get(),
                'by_creator' => MixDesign::with('createdBy')
                    ->selectRaw('created_by, COUNT(*) as count')
                    ->groupBy('created_by')
                    ->get(),
                'average_cost_per_m3' => MixDesign::with('compositions')
                    ->get()
                    ->map(function ($mixDesign) {
                        return $mixDesign->calculateCostPerM3();
                    })
                    ->avg()
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Statistics retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
