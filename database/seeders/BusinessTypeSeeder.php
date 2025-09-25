<?php

namespace Database\Seeders;

use App\Models\BusinessType;
use Illuminate\Database\Seeder;

class BusinessTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $businessTypes = [
            [
                'name' => 'Pabrik Beton',
                'slug' => 'concrete-factory',
                'description' => 'Industri manufaktur beton siap pakai, precast, dan material konstruksi',
                'icon' => 'building-office',
                'color' => '#3B82F6',
                'sort_order' => 1,
            ],
            [
                'name' => 'Pabrik Roti',
                'slug' => 'bakery-factory',
                'description' => 'Industri manufaktur roti, kue, dan produk bakery lainnya',
                'icon' => 'cake',
                'color' => '#F59E0B',
                'sort_order' => 2,
            ],
            [
                'name' => 'Retail/Perdagangan',
                'slug' => 'retail-trade',
                'description' => 'Bisnis retail, toko, dan perdagangan barang',
                'icon' => 'shopping-bag',
                'color' => '#10B981',
                'sort_order' => 3,
            ],
            [
                'name' => 'Konstruksi',
                'slug' => 'construction',
                'description' => 'Perusahaan konstruksi, kontraktor, dan pengembang',
                'icon' => 'building-library',
                'color' => '#8B5CF6',
                'sort_order' => 4,
            ],
            [
                'name' => 'Logistik/Transportasi',
                'slug' => 'logistics-transportation',
                'description' => 'Perusahaan logistik, transportasi, dan distribusi',
                'icon' => 'truck',
                'color' => '#EF4444',
                'sort_order' => 5,
            ],
            [
                'name' => 'Kesehatan',
                'slug' => 'healthcare',
                'description' => 'Rumah sakit, klinik, dan layanan kesehatan',
                'icon' => 'heart',
                'color' => '#EC4899',
                'sort_order' => 6,
            ],
            [
                'name' => 'Pendidikan',
                'slug' => 'education',
                'description' => 'Sekolah, universitas, dan institusi pendidikan',
                'icon' => 'academic-cap',
                'color' => '#06B6D4',
                'sort_order' => 7,
            ],
            [
                'name' => 'Pertanian',
                'slug' => 'agriculture',
                'description' => 'Perkebunan, pertanian, dan agribisnis',
                'icon' => 'leaf',
                'color' => '#84CC16',
                'sort_order' => 8,
            ],
            [
                'name' => 'Teknologi',
                'slug' => 'technology',
                'description' => 'Perusahaan teknologi, software, dan IT services',
                'icon' => 'computer-desktop',
                'color' => '#6366F1',
                'sort_order' => 9,
            ],
            [
                'name' => 'Manufaktur Umum',
                'slug' => 'general-manufacturing',
                'description' => 'Industri manufaktur umum dan produksi barang',
                'icon' => 'cog-6-tooth',
                'color' => '#6B7280',
                'sort_order' => 10,
            ],
        ];

        foreach ($businessTypes as $businessType) {
            BusinessType::create($businessType);
        }
    }
}
