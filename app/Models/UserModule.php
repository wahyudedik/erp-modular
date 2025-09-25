<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserModule extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'module_id',
        'is_active',
        'activated_at',
        'configuration',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'activated_at' => 'datetime',
        'configuration' => 'array',
    ];

    /**
     * Get the user that owns this module activation.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the module that is activated.
     */
    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Scope to get only active modules.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get modules by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->whereHas('module', function ($q) use ($category) {
            $q->where('category', $category);
        });
    }

    /**
     * Boot method to handle model events.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($userModule) {
            if ($userModule->is_active && !$userModule->activated_at) {
                $userModule->activated_at = now();
            }
        });

        static::updating(function ($userModule) {
            if ($userModule->is_active && !$userModule->activated_at) {
                $userModule->activated_at = now();
            }
        });
    }

    /**
     * Activate the module.
     */
    public function activate()
    {
        $this->update([
            'is_active' => true,
            'activated_at' => now(),
        ]);
    }

    /**
     * Deactivate the module.
     */
    public function deactivate()
    {
        $this->update([
            'is_active' => false,
        ]);
    }

    /**
     * Update module configuration.
     */
    public function updateConfiguration(array $configuration)
    {
        $this->update(['configuration' => $configuration]);
    }

    /**
     * Get configuration value.
     */
    public function getConfiguration($key, $default = null)
    {
        return data_get($this->configuration, $key, $default);
    }

    /**
     * Set configuration value.
     */
    public function setConfiguration($key, $value)
    {
        $configuration = $this->configuration ?? [];
        data_set($configuration, $key, $value);
        $this->updateConfiguration($configuration);
    }
}
