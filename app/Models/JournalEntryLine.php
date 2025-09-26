<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalEntryLine extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'journal_entry_id',
        'account_id',
        'description',
        'debit_amount',
        'credit_amount',
        'line_number',
    ];

    protected $casts = [
        'debit_amount' => 'decimal:2',
        'credit_amount' => 'decimal:2',
    ];

    /**
     * Get the journal entry.
     */
    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    /**
     * Get the account.
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the line amount (positive for debit, negative for credit).
     */
    public function getAmountAttribute()
    {
        return $this->debit_amount - $this->credit_amount;
    }

    /**
     * Check if line is a debit.
     */
    public function isDebit()
    {
        return $this->debit_amount > 0 && $this->credit_amount == 0;
    }

    /**
     * Check if line is a credit.
     */
    public function isCredit()
    {
        return $this->credit_amount > 0 && $this->debit_amount == 0;
    }

    /**
     * Get the line type.
     */
    public function getTypeAttribute()
    {
        if ($this->isDebit()) {
            return 'debit';
        } elseif ($this->isCredit()) {
            return 'credit';
        }

        return 'mixed';
    }

    /**
     * Scope for debit lines.
     */
    public function scopeDebit($query)
    {
        return $query->where('debit_amount', '>', 0)->where('credit_amount', 0);
    }

    /**
     * Scope for credit lines.
     */
    public function scopeCredit($query)
    {
        return $query->where('credit_amount', '>', 0)->where('debit_amount', 0);
    }

    /**
     * Scope for lines by account.
     */
    public function scopeByAccount($query, $accountId)
    {
        return $query->where('account_id', $accountId);
    }

    /**
     * Boot method to handle model events.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($line) {
            if (!$line->line_number) {
                $maxLineNumber = JournalEntryLine::where('journal_entry_id', $line->journal_entry_id)
                    ->max('line_number') ?? 0;
                $line->line_number = $maxLineNumber + 1;
            }
        });

        static::saved(function ($line) {
            // Recalculate journal entry totals
            $line->journalEntry->calculateTotals();
        });

        static::deleted(function ($line) {
            // Recalculate journal entry totals
            $line->journalEntry->calculateTotals();
        });
    }
}
