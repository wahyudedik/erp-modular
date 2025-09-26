<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class MagicLink extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'user_id',
        'email',
        'token',
        'expires_at',
        'is_used',
        'used_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    /**
     * Get the user that owns the magic link
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if magic link is expired
     */
    public function isExpired()
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if magic link is valid
     */
    public function isValid()
    {
        return !$this->is_used && !$this->isExpired();
    }

    /**
     * Mark as used
     */
    public function markAsUsed()
    {
        $this->update([
            'is_used' => true,
            'used_at' => now(),
        ]);
    }

    /**
     * Generate magic link token
     */
    public static function generateToken()
    {
        return \Illuminate\Support\Str::random(64);
    }

    /**
     * Create magic link for email
     */
    public static function createForEmail(string $email, int $expiryHours = 1)
    {
        $user = User::where('email', $email)->first();
        $token = self::generateToken();
        $expiresAt = now()->addHours($expiryHours);

        return self::create([
            'user_id' => $user?->id,
            'email' => $email,
            'token' => $token,
            'expires_at' => $expiresAt,
        ]);
    }

    /**
     * Find valid magic link by token
     */
    public static function findValidByToken(string $token)
    {
        return self::where('token', $token)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();
    }
}
