<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuid, Auditable;

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

    /**
     * Get the user's two factor authentication.
     */
    public function twoFactorAuthentication()
    {
        return $this->hasOne(TwoFactorAuthentication::class);
    }

    /**
     * Get the user's sessions.
     */
    public function userSessions()
    {
        return $this->hasMany(UserSession::class);
    }

    /**
     * Get the user's security events.
     */
    public function securityEvents()
    {
        return $this->hasMany(SecurityEvent::class);
    }

    /**
     * Get the user's password resets.
     */
    public function passwordResets()
    {
        return $this->hasMany(PasswordReset::class, 'email', 'email');
    }

    /**
     * Get user roles
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    /**
     * Get user permissions
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions');
    }

    /**
     * Check if user has role
     */
    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles()->where('name', $role)->exists();
        }

        return $this->roles()->where('role_id', $role->id)->exists();
    }

    /**
     * Check if user has permission
     */
    public function hasPermission($permission)
    {
        // Check direct permissions
        if (is_string($permission)) {
            $hasDirectPermission = $this->permissions()->where('name', $permission)->exists();
        } else {
            $hasDirectPermission = $this->permissions()->where('permission_id', $permission->id)->exists();
        }

        if ($hasDirectPermission) {
            return true;
        }

        // Check role permissions
        return $this->roles()->whereHas('permissions', function ($query) use ($permission) {
            if (is_string($permission)) {
                $query->where('name', $permission);
            } else {
                $query->where('permission_id', $permission->id);
            }
        })->exists();
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole($roles)
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }

        return $this->roles()->whereIn('name', $roles)->exists();
    }

    /**
     * Check if user has all of the given roles
     */
    public function hasAllRoles($roles)
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }

        $userRoleNames = $this->roles()->pluck('name')->toArray();
        return count(array_intersect($roles, $userRoleNames)) === count($roles);
    }

    /**
     * Assign role to user
     */
    public function assignRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->first();
        }

        if ($role && !$this->hasRole($role)) {
            $this->roles()->attach($role->id);
        }
    }

    /**
     * Remove role from user
     */
    public function removeRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->first();
        }

        if ($role) {
            $this->roles()->detach($role->id);
        }
    }

    /**
     * Give permission to user
     */
    public function givePermissionTo($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->first();
        }

        if ($permission && !$this->hasPermission($permission)) {
            $this->permissions()->attach($permission->id);
        }
    }

    /**
     * Revoke permission from user
     */
    public function revokePermissionTo($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->first();
        }

        if ($permission) {
            $this->permissions()->detach($permission->id);
        }
    }
}
