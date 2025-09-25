<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            // Core Modules
            [
                'name' => 'Akuntansi',
                'slug' => 'accounting',
                'description' => 'Sistem akuntansi lengkap dengan general ledger, accounts payable, accounts receivable',
                'category' => 'core',
                'icon' => 'calculator',
                'is_core' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Inventori',
                'slug' => 'inventory',
                'description' => 'Manajemen stok, warehouse, dan inventory tracking',
                'category' => 'core',
                'icon' => 'archive-box',
                'is_core' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Penjualan',
                'slug' => 'sales',
                'description' => 'Manajemen penjualan, customer, dan CRM',
                'category' => 'core',
                'icon' => 'currency-dollar',
                'is_core' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Pembelian',
                'slug' => 'purchase',
                'description' => 'Manajemen pembelian, supplier, dan procurement',
                'category' => 'core',
                'icon' => 'shopping-cart',
                'is_core' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'HR & Payroll',
                'slug' => 'hr-payroll',
                'description' => 'Manajemen sumber daya manusia dan payroll',
                'category' => 'core',
                'icon' => 'users',
                'is_core' => true,
                'sort_order' => 5,
            ],

            // Manufacturing Modules
            [
                'name' => 'Produksi',
                'slug' => 'production',
                'description' => 'Planning produksi, work orders, dan production tracking',
                'category' => 'manufacturing',
                'icon' => 'cog-6-tooth',
                'is_core' => false,
                'sort_order' => 10,
            ],
            [
                'name' => 'Quality Control',
                'slug' => 'quality-control',
                'description' => 'Quality control, inspection, dan quality management',
                'category' => 'manufacturing',
                'icon' => 'check-circle',
                'is_core' => false,
                'sort_order' => 11,
            ],
            [
                'name' => 'Maintenance',
                'slug' => 'maintenance',
                'description' => 'Maintenance management dan equipment tracking',
                'category' => 'manufacturing',
                'icon' => 'wrench-screwdriver',
                'is_core' => false,
                'sort_order' => 12,
            ],

            // Industry Specific - Concrete
            [
                'name' => 'Mix Design',
                'slug' => 'mix-design',
                'description' => 'Manajemen mix design beton dan formula',
                'category' => 'industry',
                'icon' => 'beaker',
                'is_core' => false,
                'sort_order' => 20,
            ],
            [
                'name' => 'Concrete Testing',
                'slug' => 'concrete-testing',
                'description' => 'Testing beton dan quality assurance',
                'category' => 'industry',
                'icon' => 'clipboard-document-check',
                'is_core' => false,
                'sort_order' => 21,
            ],
            [
                'name' => 'Delivery Management',
                'slug' => 'delivery-management',
                'description' => 'Manajemen pengiriman beton dan truck tracking',
                'category' => 'industry',
                'icon' => 'truck',
                'is_core' => false,
                'sort_order' => 22,
            ],

            // Industry Specific - Bakery
            [
                'name' => 'Recipe Management',
                'slug' => 'recipe-management',
                'description' => 'Manajemen resep dan formula produk',
                'category' => 'industry',
                'icon' => 'book-open',
                'is_core' => false,
                'sort_order' => 30,
            ],
            [
                'name' => 'Expiry Tracking',
                'slug' => 'expiry-tracking',
                'description' => 'Tracking expiry date dan FIFO management',
                'category' => 'industry',
                'icon' => 'clock',
                'is_core' => false,
                'sort_order' => 31,
            ],
            [
                'name' => 'Batch Management',
                'slug' => 'batch-management',
                'description' => 'Manajemen batch produksi dan traceability',
                'category' => 'industry',
                'icon' => 'queue-list',
                'is_core' => false,
                'sort_order' => 32,
            ],

            // Retail Modules
            [
                'name' => 'Point of Sale',
                'slug' => 'point-of-sale',
                'description' => 'Sistem kasir dan point of sale',
                'category' => 'retail',
                'icon' => 'credit-card',
                'is_core' => false,
                'sort_order' => 40,
            ],
            [
                'name' => 'Customer Management',
                'slug' => 'customer-management',
                'description' => 'CRM dan customer relationship management',
                'category' => 'retail',
                'icon' => 'user-group',
                'is_core' => false,
                'sort_order' => 41,
            ],
            [
                'name' => 'Promotions',
                'slug' => 'promotions',
                'description' => 'Manajemen promosi dan discount',
                'category' => 'retail',
                'icon' => 'tag',
                'is_core' => false,
                'sort_order' => 42,
            ],

            // Construction Modules
            [
                'name' => 'Project Management',
                'slug' => 'project-management',
                'description' => 'Project management dan progress tracking',
                'category' => 'construction',
                'icon' => 'clipboard-document-list',
                'is_core' => false,
                'sort_order' => 50,
            ],
            [
                'name' => 'Equipment Management',
                'slug' => 'equipment-management',
                'description' => 'Manajemen equipment dan asset tracking',
                'category' => 'construction',
                'icon' => 'wrench',
                'is_core' => false,
                'sort_order' => 51,
            ],

            // Logistics Modules
            [
                'name' => 'Fleet Management',
                'slug' => 'fleet-management',
                'description' => 'Manajemen fleet dan vehicle tracking',
                'category' => 'logistics',
                'icon' => 'truck',
                'is_core' => false,
                'sort_order' => 60,
            ],
            [
                'name' => 'Route Optimization',
                'slug' => 'route-optimization',
                'description' => 'Optimasi rute dan delivery planning',
                'category' => 'logistics',
                'icon' => 'map',
                'is_core' => false,
                'sort_order' => 61,
            ],
        ];

        foreach ($modules as $module) {
            Module::create($module);
        }
    }
}
