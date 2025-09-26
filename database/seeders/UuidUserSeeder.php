<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\BusinessType;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UuidUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first business type (Concrete Factory)
        $businessType = BusinessType::where('slug', 'concrete-factory')->first();

        if (!$businessType) {
            $this->command->error('Business type not found. Please run BusinessTypeSeeder first.');
            return;
        }

        // Create admin user
        User::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Admin User',
            'email' => 'admin@erp-modular.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'business_type_id' => $businessType->id,
            'company_name' => 'ERP Modular Company',
            'phone' => '+6281234567890',
            'address' => 'Jakarta, Indonesia',
        ]);

        // Create test user
        User::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Test User',
            'email' => 'test@erp-modular.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'business_type_id' => $businessType->id,
            'company_name' => 'Test Company',
            'phone' => '+6281234567891',
            'address' => 'Bandung, Indonesia',
        ]);

        $this->command->info('Users with UUID created successfully!');
    }
}
