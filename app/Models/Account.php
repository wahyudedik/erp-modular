<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'sub_type',
        'parent_id',
        'level',
        'is_active',
        'is_system',
        'opening_balance',
        'normal_balance',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_system' => 'boolean',
        'opening_balance' => 'decimal:2',
    ];

    /**
     * Get the parent account.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    /**
     * Get the child accounts.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    /**
     * Get all descendants recursively.
     */
    public function descendants(): HasMany
    {
        return $this->children()->with('descendants');
    }

    /**
     * Get journal entry lines for this account.
     */
    public function journalEntryLines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    /**
     * Scope for active accounts.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for accounts by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for accounts by sub type.
     */
    public function scopeBySubType($query, $subType)
    {
        return $query->where('sub_type', $subType);
    }

    /**
     * Scope for root accounts (no parent).
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope for leaf accounts (no children).
     */
    public function scopeLeaf($query)
    {
        return $query->whereDoesntHave('children');
    }

    /**
     * Get account balance.
     */
    public function getBalance()
    {
        $debitTotal = $this->journalEntryLines()
            ->whereHas('journalEntry', function ($query) {
                $query->where('status', 'posted');
            })
            ->sum('debit_amount');

        $creditTotal = $this->journalEntryLines()
            ->whereHas('journalEntry', function ($query) {
                $query->where('status', 'posted');
            })
            ->sum('credit_amount');

        $balance = $debitTotal - $creditTotal;

        // Adjust for normal balance
        if ($this->normal_balance === 'credit') {
            $balance = $creditTotal - $debitTotal;
        }

        return $balance + $this->opening_balance;
    }

    /**
     * Get account hierarchy path.
     */
    public function getHierarchyPath()
    {
        $path = [$this->name];
        $parent = $this->parent;

        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parent;
        }

        return implode(' > ', $path);
    }

    /**
     * Check if account is a parent of another account.
     */
    public function isParentOf(Account $account)
    {
        return $account->parent_id === $this->id;
    }

    /**
     * Check if account is a child of another account.
     */
    public function isChildOf(Account $account)
    {
        return $this->parent_id === $account->id;
    }

    /**
     * Get account types.
     */
    public static function getTypes()
    {
        return [
            'asset' => 'Asset',
            'liability' => 'Liability',
            'equity' => 'Equity',
            'revenue' => 'Revenue',
            'expense' => 'Expense',
        ];
    }

    /**
     * Get account sub types.
     */
    public static function getSubTypes()
    {
        return [
            'current_asset' => 'Current Asset',
            'fixed_asset' => 'Fixed Asset',
            'intangible_asset' => 'Intangible Asset',
            'current_liability' => 'Current Liability',
            'long_term_liability' => 'Long Term Liability',
            'owner_equity' => 'Owner Equity',
            'retained_earnings' => 'Retained Earnings',
            'operating_revenue' => 'Operating Revenue',
            'other_revenue' => 'Other Revenue',
            'operating_expense' => 'Operating Expense',
            'other_expense' => 'Other Expense',
        ];
    }
}
