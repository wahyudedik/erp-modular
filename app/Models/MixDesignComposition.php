<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MixDesignComposition extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'mix_design_id',
        'material_type',
        'material_name',
        'percentage',
        'weight_per_m3',
        'unit_cost',
        'is_admixture',
        'is_water',
        'is_cement',
        'is_aggregate',
        'aggregate_size',
        'notes'
    ];

    protected $casts = [
        'percentage' => 'decimal:4',
        'weight_per_m3' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'is_admixture' => 'boolean',
        'is_water' => 'boolean',
        'is_cement' => 'boolean',
        'is_aggregate' => 'boolean'
    ];

    /**
     * Get the mix design
     */
    public function mixDesign(): BelongsTo
    {
        return $this->belongsTo(MixDesign::class);
    }

    /**
     * Calculate cost for this composition
     */
    public function calculateCost()
    {
        return $this->weight_per_m3 * $this->unit_cost;
    }

    /**
     * Get material types
     */
    public static function getMaterialTypes()
    {
        return [
            'cement' => 'Cement',
            'fine_aggregate' => 'Fine Aggregate',
            'coarse_aggregate' => 'Coarse Aggregate',
            'water' => 'Water',
            'admixture' => 'Admixture',
            'supplementary' => 'Supplementary Cementitious Material'
        ];
    }

    /**
     * Get aggregate sizes
     */
    public static function getAggregateSizes()
    {
        return [
            '0-5mm' => '0-5mm (Fine)',
            '5-10mm' => '5-10mm',
            '10-20mm' => '10-20mm',
            '20-40mm' => '20-40mm',
            '40-70mm' => '40-70mm (Coarse)'
        ];
    }

    /**
     * Scope for cement
     */
    public function scopeCement($query)
    {
        return $query->where('is_cement', true);
    }

    /**
     * Scope for aggregates
     */
    public function scopeAggregates($query)
    {
        return $query->where('is_aggregate', true);
    }

    /**
     * Scope for admixtures
     */
    public function scopeAdmixtures($query)
    {
        return $query->where('is_admixture', true);
    }

    /**
     * Scope for water
     */
    public function scopeWater($query)
    {
        return $query->where('is_water', true);
    }
}
