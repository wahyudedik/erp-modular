<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;

class ChartOfAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Assets
        $assets = Account::create([
            'code' => '1000',
            'name' => 'ASSETS',
            'type' => 'asset',
            'sub_type' => 'current_asset',
            'level' => 0,
            'is_active' => true,
            'is_system' => true,
            'normal_balance' => 'debit',
        ]);

        // Current Assets
        $currentAssets = Account::create([
            'code' => '1100',
            'name' => 'Current Assets',
            'type' => 'asset',
            'sub_type' => 'current_asset',
            'parent_id' => $assets->id,
            'level' => 1,
            'is_active' => true,
            'is_system' => true,
            'normal_balance' => 'debit',
        ]);

        // Cash and Cash Equivalents
        Account::create([
            'code' => '1110',
            'name' => 'Cash and Cash Equivalents',
            'type' => 'asset',
            'sub_type' => 'current_asset',
            'parent_id' => $currentAssets->id,
            'level' => 2,
            'is_active' => true,
            'is_system' => true,
            'normal_balance' => 'debit',
        ]);

        Account::create([
            'code' => '1111',
            'name' => 'Cash on Hand',
            'type' => 'asset',
            'sub_type' => 'current_asset',
            'parent_id' => $currentAssets->id,
            'level' => 3,
            'is_active' => true,
            'is_system' => true,
            'normal_balance' => 'debit',
        ]);

        Account::create([
            'code' => '1112',
            'name' => 'Cash in Bank',
            'type' => 'asset',
            'sub_type' => 'current_asset',
            'parent_id' => $currentAssets->id,
            'level' => 3,
            'is_active' => true,
            'is_system' => true,
            'normal_balance' => 'debit',
        ]);

        // Accounts Receivable
        Account::create([
            'code' => '1120',
            'name' => 'Accounts Receivable',
            'type' => 'asset',
            'sub_type' => 'current_asset',
            'parent_id' => $currentAssets->id,
            'level' => 2,
            'is_active' => true,
            'is_system' => true,
            'normal_balance' => 'debit',
        ]);

        // Inventory
        Account::create([
            'code' => '1130',
            'name' => 'Inventory',
            'type' => 'asset',
            'sub_type' => 'current_asset',
            'parent_id' => $currentAssets->id,
            'level' => 2,
            'is_active' => true,
            'is_system' => true,
            'normal_balance' => 'debit',
        ]);

        // Fixed Assets
        $fixedAssets = Account::create([
            'code' => '1200',
            'name' => 'Fixed Assets',
            'type' => 'asset',
            'sub_type' => 'fixed_asset',
            'parent_id' => $assets->id,
            'level' => 1,
            'is_active' => true,
            'is_system' => true,
            'normal_balance' => 'debit',
        ]);

        Account::create([
            'code' => '1210',
            'name' => 'Equipment',
            'type' => 'asset',
            'sub_type' => 'fixed_asset',
            'parent_id' => $fixedAssets->id,
            'level' => 2,
            'is_active' => true,
            'is_system' => true,
            'normal_balance' => 'debit',
        ]);

        Account::create([
            'code' => '1211',
            'name' => 'Accumulated Depreciation - Equipment',
            'type' => 'asset',
            'sub_type' => 'fixed_asset',
            'parent_id' => $fixedAssets->id,
            'level' => 3,
            'is_active' => true,
            'is_system' => true,
            'normal_balance' => 'credit',
        ]);

        // Liabilities
        $liabilities = Account::create([
            'code' => '2000',
            'name' => 'LIABILITIES',
            'type' => 'liability',
            'sub_type' => 'current_liability',
            'level' => 0,
            'is_active' => true,
            'is_system' => true,
            'normal_balance' => 'credit',
        ]);

        // Current Liabilities
        $currentLiabilities = Account::create([
            'code' => '2100',
            'name' => 'Current Liabilities',
            'type' => 'liability',
            'sub_type' => 'current_liability',
            'parent_id' => $liabilities->id,
            'level' => 1,
            'is_active' => true,
            'is_system' => true,
            'normal_balance' => 'credit',
        ]);

        Account::create([
            'code' => '2110',
            'name' => 'Accounts Payable',
            'type' => 'liability',
            'sub_type' => 'current_liability',
            'parent_id' => $currentLiabilities->id,
            'level' => 2,
            'is_active' => true,
            'is_system' => true,
            'normal_balance' => 'credit',
        ]);

        Account::create([
            'code' => '2120',
            'name' => 'Accrued Expenses',
            'type' => 'liability',
            'sub_type' => 'current_liability',
            'parent_id' => $currentLiabilities->id,
            'level' => 2,
            'is_active' => true,
            'is_system' => true,
            'normal_balance' => 'credit',
        ]);

        // Equity
        $equity = Account::create([
            'code' => '3000',
            'name' => 'EQUITY',
            'type' => 'equity',
            'sub_type' => 'owner_equity',
            'level' => 0,
            'is_active' => true,
            'is_system' => true,
            'normal_balance' => 'credit',
        ]);

        Account::create([
            'code' => '3100',
            'name' => 'Owner Equity',
            'type' => 'equity',
            'sub_type' => 'owner_equity',
            'parent_id' => $equity->id,
            'level' => 1,
            'is_active' => true,
            'is_system' => true,
            'normal_balance' => 'credit',
        ]);

        Account::create([
            'code' => '3200',
            'name' => 'Retained Earnings',
            'type' => 'equity',
            'sub_type' => 'retained_earnings',
            'parent_id' => $equity->id,
            'level' => 1,
            'is_active' => true,
            'is_system' => true,
            'normal_balance' => 'credit',
        ]);

        // Revenue
        $revenue = Account::create([
            'code' => '4000',
            'name' => 'REVENUE',
            'type' => 'revenue',
            'sub_type' => 'operating_revenue',
            'level' => 0,
            'is_active' => true,
            'is_system' => true,
            'normal_balance' => 'credit',
        ]);

        Account::create([
            'code' => '4100',
            'name' => 'Sales Revenue',
            'type' => 'revenue',
            'sub_type' => 'operating_revenue',
            'parent_id' => $revenue->id,
            'level' => 1,
            'is_active' => true,
            'is_system' => true,
            'normal_balance' => 'credit',
        ]);

        // Expenses
        $expenses = Account::create([
            'code' => '5000',
            'name' => 'EXPENSES',
            'type' => 'expense',
            'sub_type' => 'operating_expense',
            'level' => 0,
            'is_active' => true,
            'is_system' => true,
            'normal_balance' => 'debit',
        ]);

        Account::create([
            'code' => '5100',
            'name' => 'Cost of Goods Sold',
            'type' => 'expense',
            'sub_type' => 'operating_expense',
            'parent_id' => $expenses->id,
            'level' => 1,
            'is_active' => true,
            'is_system' => true,
            'normal_balance' => 'debit',
        ]);

        Account::create([
            'code' => '5200',
            'name' => 'Operating Expenses',
            'type' => 'expense',
            'sub_type' => 'operating_expense',
            'parent_id' => $expenses->id,
            'level' => 1,
            'is_active' => true,
            'is_system' => true,
            'normal_balance' => 'debit',
        ]);

        Account::create([
            'code' => '5210',
            'name' => 'Salaries and Wages',
            'type' => 'expense',
            'sub_type' => 'operating_expense',
            'parent_id' => $expenses->id,
            'level' => 2,
            'is_active' => true,
            'is_system' => true,
            'normal_balance' => 'debit',
        ]);

        Account::create([
            'code' => '5220',
            'name' => 'Rent Expense',
            'type' => 'expense',
            'sub_type' => 'operating_expense',
            'parent_id' => $expenses->id,
            'level' => 2,
            'is_active' => true,
            'is_system' => true,
            'normal_balance' => 'debit',
        ]);

        Account::create([
            'code' => '5230',
            'name' => 'Utilities Expense',
            'type' => 'expense',
            'sub_type' => 'operating_expense',
            'parent_id' => $expenses->id,
            'level' => 2,
            'is_active' => true,
            'is_system' => true,
            'normal_balance' => 'debit',
        ]);
    }
}
