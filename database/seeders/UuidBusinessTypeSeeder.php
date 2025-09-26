<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BusinessType;
use Illuminate\Support\Str;

class UuidBusinessTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $businessTypes = [
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Pabrik Beton',
                'slug' => 'concrete-factory',
                'description' => 'Industri manufaktur beton siap pakai, precast, dan material konstruksi',
                'icon' => 'building-office',
                'color' => '#3B82F6',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Pabrik Roti',
                'slug' => 'bakery-factory',
                'description' => 'Industri manufaktur roti, kue, dan produk bakery lainnya',
                'icon' => 'cake',
                'color' => '#F59E0B',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Retail/Perdagangan',
                'slug' => 'retail-trade',
                'description' => 'Bisnis retail, toko, dan perdagangan barang',
                'icon' => 'shopping-bag',
                'color' => '#10B981',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Konstruksi',
                'slug' => 'construction',
                'description' => 'Industri konstruksi, pembangunan, dan proyek infrastruktur',
                'icon' => 'building',
                'color' => '#EF4444',
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($businessTypes as $businessType) {
            BusinessType::create($businessType);
        }

        $this->command->info('Business types with UUID created successfully!');
    }
}
