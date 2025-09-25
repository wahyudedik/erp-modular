<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'category',
        'icon',
        'version',
        'is_core',
        'is_active',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_core' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the users that have this module activated.
     */
    public function userModules()
    {
        return $this->hasMany(UserModule::class);
    }

    /**
     * Get the business types that recommend this module.
     */
    public function moduleRecommendations()
    {
        return $this->hasMany(ModuleRecommendation::class);
    }

    /**
     * Get the business types that recommend this module.
     */
    public function recommendedForBusinessTypes()
    {
        return $this->belongsToMany(BusinessType::class, 'module_recommendations')
            ->withPivot(['priority', 'is_required']);
    }

    /**
     * Scope to get only active modules.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get core modules.
     */
    public function scopeCore($query)
    {
        return $query->where('is_core', true);
    }

    /**
     * Scope to get modules by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
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

    /**
     * Get module categories.
     */
    public static function getCategories()
    {
        return [
            'core' => 'Core Modules',
            'manufacturing' => 'Manufacturing',
            'retail' => 'Retail',
            'construction' => 'Construction',
            'logistics' => 'Logistics',
            'healthcare' => 'Healthcare',
            'education' => 'Education',
        ];
    }
}
