<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JournalEntry extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'entry_number',
        'entry_date',
        'description',
        'reference',
        'status',
        'total_debit',
        'total_credit',
        'created_by',
        'approved_by',
        'posted_at',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'total_debit' => 'decimal:2',
        'total_credit' => 'decimal:2',
        'posted_at' => 'datetime',
    ];

    /**
     * Get the user who created this entry.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved this entry.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the journal entry lines.
     */
    public function lines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    /**
     * Scope for posted entries.
     */
    public function scopePosted($query)
    {
        return $query->where('status', 'posted');
    }

    /**
     * Scope for draft entries.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope for entries by date range.
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('entry_date', [$startDate, $endDate]);
    }

    /**
     * Check if entry is balanced.
     */
    public function isBalanced()
    {
        return $this->total_debit == $this->total_credit;
    }

    /**
     * Post the journal entry.
     */
    public function post()
    {
        if (!$this->isBalanced()) {
            throw new \Exception('Journal entry is not balanced. Debits must equal credits.');
        }

        if ($this->status !== 'draft') {
            throw new \Exception('Only draft entries can be posted.');
        }

        $this->update([
            'status' => 'posted',
            'posted_at' => now(),
        ]);
    }

    /**
     * Reverse the journal entry.
     */
    public function reverse()
    {
        if ($this->status !== 'posted') {
            throw new \Exception('Only posted entries can be reversed.');
        }

        $this->update([
            'status' => 'reversed',
        ]);
    }

    /**
     * Calculate totals from lines.
     */
    public function calculateTotals()
    {
        $totalDebit = $this->lines()->sum('debit_amount');
        $totalCredit = $this->lines()->sum('credit_amount');

        $this->update([
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
        ]);

        return $this;
    }

    /**
     * Generate entry number.
     */
    public static function generateEntryNumber()
    {
        $lastEntry = self::orderBy('id', 'desc')->first();
        $lastNumber = $lastEntry ? (int) substr($lastEntry->entry_number, 3) : 0;

        return 'JE' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Boot method to handle model events.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($journalEntry) {
            if (!$journalEntry->entry_number) {
                $journalEntry->entry_number = self::generateEntryNumber();
            }
        });
    }
}
