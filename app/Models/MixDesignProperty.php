<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MixDesignProperty extends Model
{
    use HasFactory;

    protected $fillable = [
        'mix_design_id',
        'property_name',
        'property_value',
        'unit',
        'notes'
    ];

    protected $casts = [
        'property_value' => 'decimal:4'
    ];

    /**
     * Get the mix design
     */
    public function mixDesign(): BelongsTo
    {
        return $this->belongsTo(MixDesign::class);
    }
}
