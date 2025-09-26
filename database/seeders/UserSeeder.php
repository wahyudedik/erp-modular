<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@erp-modular.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'business_type_id' => 1, // Concrete Factory
            'company_name' => 'ERP Modular Company',
            'phone' => '+6281234567890',
            'address' => 'Jakarta, Indonesia',
        ]);

        // Create test user
        User::create([
            'name' => 'Test User',
            'email' => 'test@erp-modular.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'business_type_id' => 1, // Concrete Factory
            'company_name' => 'Test Company',
            'phone' => '+6281234567891',
            'address' => 'Bandung, Indonesia',
        ]);

        $this->command->info('Users created successfully!');
    }
}
