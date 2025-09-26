<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessType extends Model
{
    use HasFactory, HasUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'is_active',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the users that belong to this business type.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the module recommendations for this business type.
     */
    public function moduleRecommendations()
    {
        return $this->hasMany(ModuleRecommendation::class);
    }

    /**
     * Get the recommended modules for this business type.
     */
    public function recommendedModules()
    {
        return $this->belongsToMany(Module::class, 'module_recommendations')
            ->withPivot(['priority', 'is_required'])
            ->orderBy('pivot_priority');
    }

    /**
     * Scope to get only active business types.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
