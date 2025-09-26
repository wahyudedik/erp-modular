<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MixDesignVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'mix_design_id',
        'version',
        'changes',
        'created_by',
        'approved_by',
        'approved_at',
        'is_active'
    ];

    protected $casts = [
        'changes' => 'array',
        'approved_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    /**
     * Get the mix design
     */
    public function mixDesign(): BelongsTo
    {
        return $this->belongsTo(MixDesign::class);
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
}
