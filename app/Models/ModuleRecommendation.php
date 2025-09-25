<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleRecommendation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'business_type_id',
        'module_id',
        'priority',
        'is_required',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_required' => 'boolean',
    ];

    /**
     * Get the business type that this recommendation belongs to.
     */
    public function businessType()
    {
        return $this->belongsTo(BusinessType::class);
    }

    /**
     * Get the module that is recommended.
     */
    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Scope to get high priority recommendations.
     */
    public function scopeHighPriority($query)
    {
        return $query->where('priority', 1);
    }

    /**
     * Scope to get required recommendations.
     */
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    /**
     * Scope to order by priority.
     */
    public function scopeOrderedByPriority($query)
    {
        return $query->orderBy('priority');
    }

    /**
     * Get priority labels.
     */
    public static function getPriorityLabels()
    {
        return [
            1 => 'High Priority',
            2 => 'Medium Priority',
            3 => 'Low Priority',
        ];
    }

    /**
     * Get priority label for current recommendation.
     */
    public function getPriorityLabelAttribute()
    {
        return self::getPriorityLabels()[$this->priority] ?? 'Unknown';
    }
}
