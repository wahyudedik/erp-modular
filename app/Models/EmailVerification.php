<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class EmailVerification extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'user_id',
        'email',
        'token',
        'expires_at',
        'is_verified',
        'verified_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'is_verified' => 'boolean',
    ];

    /**
     * Get the user that owns the verification
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if verification is expired
     */
    public function isExpired()
    {
        return $this->expires_at->isPast();
    }

    /**
     * Mark as verified
     */
    public function markAsVerified()
    {
        $this->update([
            'is_verified' => true,
            'verified_at' => now(),
        ]);
    }

    /**
     * Generate verification token
     */
    public static function generateToken()
    {
        return \Illuminate\Support\Str::random(64);
    }

    /**
     * Create verification record
     */
    public static function createForUser(User $user, string $email = null)
    {
        $email = $email ?? $user->email;
        $token = self::generateToken();
        $expiresAt = now()->addHours(24);

        return self::create([
            'user_id' => $user->id,
            'email' => $email,
            'token' => $token,
            'expires_at' => $expiresAt,
        ]);
    }
}
