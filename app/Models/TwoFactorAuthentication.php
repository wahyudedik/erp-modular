<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TwoFactorAuthentication extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'user_id',
        'secret_key',
        'recovery_codes',
        'is_enabled',
        'enabled_at',
        'last_used_at',
        'backup_phone',
        'sms_enabled',
        'email_enabled',
        'app_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'sms_enabled' => 'boolean',
        'email_enabled' => 'boolean',
        'app_enabled' => 'boolean',
        'enabled_at' => 'datetime',
        'last_used_at' => 'datetime',
        'recovery_codes' => 'array',
    ];

    /**
     * Get the user that owns the two factor authentication.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
