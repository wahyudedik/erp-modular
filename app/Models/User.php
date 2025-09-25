<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'business_type_id',
        'company_name',
        'phone',
        'address',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /**
     * Get the business type that the user belongs to.
     */
    public function businessType()
    {
        return $this->belongsTo(BusinessType::class);
    }

    /**
     * Get the modules that the user has activated.
     */
    public function userModules()
    {
        return $this->hasMany(UserModule::class);
    }

    /**
     * Get the active modules for the user.
     */
    public function activeModules()
    {
        return $this->userModules()->where('is_active', true);
    }

    /**
     * Check if user has access to a specific module.
     */
    public function hasModuleAccess($moduleSlug)
    {
        return $this->activeModules()
            ->whereHas('module', function ($query) use ($moduleSlug) {
                $query->where('slug', $moduleSlug);
            })
            ->exists();
    }

    /**
     * Get module recommendations for the user's business type.
     */
    public function getModuleRecommendations()
    {
        return ModuleRecommendation::where('business_type_id', $this->business_type_id)
            ->with('module')
            ->orderBy('priority')
            ->get();
    }
}
