<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MixDesign extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'mix_id',
        'name',
        'class_strength',
        'target_strength',
        'slump',
        'durability_class',
        'exposure_class',
        'is_active',
        'version',
        'created_by',
        'approved_by',
        'approved_at',
        'notes'
    ];

    protected $casts = [
        'target_strength' => 'decimal:2',
        'slump' => 'decimal:2',
        'is_active' => 'boolean',
        'approved_at' => 'datetime'
    ];

    /**
     * Get the mix design compositions
     */
    public function compositions(): HasMany
    {
        return $this->hasMany(MixDesignComposition::class);
    }

    /**
     * Get the mix design properties
     */
    public function properties(): HasMany
    {
        return $this->hasMany(MixDesignProperty::class);
    }

    /**
     * Get the creator
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the approver
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the mix design versions
     */
    public function versions(): HasMany
    {
        return $this->hasMany(MixDesignVersion::class);
    }

    /**
     * Get the latest version
     */
    public function latestVersion()
    {
        return $this->versions()->latest()->first();
    }

    /**
     * Scope for active mix designs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific strength class
     */
    public function scopeByStrengthClass($query, $class)
    {
        return $query->where('class_strength', $class);
    }

    /**
     * Get strength classes
     */
    public static function getStrengthClasses()
    {
        return [
            'K-175' => 'K-175 (fc\' = 15 MPa)',
            'K-200' => 'K-200 (fc\' = 17 MPa)',
            'K-225' => 'K-225 (fc\' = 19 MPa)',
            'K-250' => 'K-250 (fc\' = 21 MPa)',
            'K-300' => 'K-300 (fc\' = 25 MPa)',
            'K-350' => 'K-350 (fc\' = 29 MPa)',
            'K-400' => 'K-400 (fc\' = 33 MPa)',
            'K-450' => 'K-450 (fc\' = 37 MPa)',
            'K-500' => 'K-500 (fc\' = 41 MPa)',
        ];
    }

    /**
     * Get slump classes
     */
    public static function getSlumpClasses()
    {
        return [
            'S3' => 'S3 (0-3 cm)',
            'S5' => 'S5 (3-5 cm)',
            'S8' => 'S8 (5-8 cm)',
            'S10' => 'S10 (8-10 cm)',
            'S12' => 'S12 (10-12 cm)',
        ];
    }

    /**
     * Get exposure classes
     */
    public static function getExposureClasses()
    {
        return [
            'XC1' => 'XC1 - Dry',
            'XC2' => 'XC2 - Humid',
            'XC3' => 'XC3 - Wet',
            'XC4' => 'XC4 - Cyclic wet/dry',
            'XD1' => 'XD1 - Moderate humidity',
            'XD2' => 'XD2 - Wet',
            'XD3' => 'XD3 - Cyclic wet/dry',
            'XF1' => 'XF1 - Moderate water saturation',
            'XF2' => 'XF2 - High water saturation',
            'XF3' => 'XF3 - Cyclic freezing/thawing',
            'XF4' => 'XF4 - Cyclic freezing/thawing + de-icing',
            'XS1' => 'XS1 - Low chloride',
            'XS2' => 'XS2 - Moderate chloride',
            'XS3' => 'XS3 - High chloride',
        ];
    }

    /**
     * Calculate total cost per cubic meter
     */
    public function calculateCostPerM3()
    {
        $totalCost = 0;

        foreach ($this->compositions as $composition) {
            $totalCost += $composition->calculateCost();
        }

        return $totalCost;
    }

    /**
     * Get route key name
     */
    public function getRouteKeyName()
    {
        return 'mix_id';
    }
}
