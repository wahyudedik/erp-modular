<?php

namespace Database\Seeders;

use App\Models\BusinessType;
use App\Models\Module;
use App\Models\ModuleRecommendation;
use Illuminate\Database\Seeder;

class ModuleRecommendationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recommendations = [
            // Pabrik Beton
            'concrete-factory' => [
                // Core modules (required)
                ['accounting', 1, true],
                ['inventory', 1, true],
                ['sales', 1, true],
                ['purchase', 1, true],
                ['hr-payroll', 1, true],

                // Manufacturing modules (high priority)
                ['production', 1, false],
                ['quality-control', 1, false],
                ['maintenance', 1, false],

                // Industry specific (high priority)
                ['mix-design', 1, false],
                ['concrete-testing', 1, false],
                ['delivery-management', 1, false],

                // Additional modules (medium priority)
                ['project-management', 2, false],
                ['fleet-management', 2, false],
            ],

            // Pabrik Roti
            'bakery-factory' => [
                // Core modules (required)
                ['accounting', 1, true],
                ['inventory', 1, true],
                ['sales', 1, true],
                ['purchase', 1, true],
                ['hr-payroll', 1, true],

                // Manufacturing modules (high priority)
                ['production', 1, false],
                ['quality-control', 1, false],
                ['maintenance', 1, false],

                // Industry specific (high priority)
                ['recipe-management', 1, false],
                ['expiry-tracking', 1, false],
                ['batch-management', 1, false],

                // Additional modules (medium priority)
                ['point-of-sale', 2, false],
                ['customer-management', 2, false],
            ],

            // Retail/Perdagangan
            'retail-trade' => [
                // Core modules (required)
                ['accounting', 1, true],
                ['inventory', 1, true],
                ['sales', 1, true],
                ['purchase', 1, true],
                ['hr-payroll', 1, true],

                // Retail modules (high priority)
                ['point-of-sale', 1, false],
                ['customer-management', 1, false],
                ['promotions', 1, false],

                // Additional modules (medium priority)
                ['batch-management', 2, false],
            ],

            // Konstruksi
            'construction' => [
                // Core modules (required)
                ['accounting', 1, true],
                ['inventory', 1, true],
                ['sales', 1, true],
                ['purchase', 1, true],
                ['hr-payroll', 1, true],

                // Construction modules (high priority)
                ['project-management', 1, false],
                ['equipment-management', 1, false],
                ['maintenance', 1, false],

                // Additional modules (medium priority)
                ['quality-control', 2, false],
                ['fleet-management', 2, false],
            ],

            // Logistik/Transportasi
            'logistics-transportation' => [
                // Core modules (required)
                ['accounting', 1, true],
                ['inventory', 1, true],
                ['sales', 1, true],
                ['purchase', 1, true],
                ['hr-payroll', 1, true],

                // Logistics modules (high priority)
                ['fleet-management', 1, false],
                ['route-optimization', 1, false],

                // Additional modules (medium priority)
                ['customer-management', 2, false],
                ['maintenance', 2, false],
            ],

            // Kesehatan
            'healthcare' => [
                // Core modules (required)
                ['accounting', 1, true],
                ['inventory', 1, true],
                ['sales', 1, true],
                ['purchase', 1, true],
                ['hr-payroll', 1, true],

                // Healthcare specific (high priority)
                ['customer-management', 1, false], // Patient management
                ['batch-management', 1, false], // Medicine tracking
                ['expiry-tracking', 1, false], // Medicine expiry

                // Additional modules (medium priority)
                ['quality-control', 2, false],
                ['project-management', 2, false],
            ],

            // Pendidikan
            'education' => [
                // Core modules (required)
                ['accounting', 1, true],
                ['inventory', 1, true],
                ['sales', 1, true],
                ['purchase', 1, true],
                ['hr-payroll', 1, true],

                // Education specific (high priority)
                ['customer-management', 1, false], // Student management
                ['project-management', 1, false], // Course management

                // Additional modules (medium priority)
                ['quality-control', 2, false],
                ['batch-management', 2, false],
            ],

            // Pertanian
            'agriculture' => [
                // Core modules (required)
                ['accounting', 1, true],
                ['inventory', 1, true],
                ['sales', 1, true],
                ['purchase', 1, true],
                ['hr-payroll', 1, true],

                // Agriculture specific (high priority)
                ['batch-management', 1, false], // Crop tracking
                ['expiry-tracking', 1, false], // Harvest tracking
                ['quality-control', 1, false],

                // Additional modules (medium priority)
                ['production', 2, false],
                ['maintenance', 2, false],
            ],

            // Teknologi
            'technology' => [
                // Core modules (required)
                ['accounting', 1, true],
                ['inventory', 1, true],
                ['sales', 1, true],
                ['purchase', 1, true],
                ['hr-payroll', 1, true],

                // Technology specific (high priority)
                ['project-management', 1, false], // Project management
                ['customer-management', 1, false], // Client management

                // Additional modules (medium priority)
                ['quality-control', 2, false],
                ['batch-management', 2, false],
            ],

            // Manufaktur Umum
            'general-manufacturing' => [
                // Core modules (required)
                ['accounting', 1, true],
                ['inventory', 1, true],
                ['sales', 1, true],
                ['purchase', 1, true],
                ['hr-payroll', 1, true],

                // Manufacturing modules (high priority)
                ['production', 1, false],
                ['quality-control', 1, false],
                ['maintenance', 1, false],

                // Additional modules (medium priority)
                ['batch-management', 2, false],
                ['customer-management', 2, false],
            ],
        ];

        foreach ($recommendations as $businessTypeSlug => $moduleRecommendations) {
            $businessType = BusinessType::where('slug', $businessTypeSlug)->first();

            if (!$businessType) {
                continue;
            }

            foreach ($moduleRecommendations as $recommendation) {
                [$moduleSlug, $priority, $isRequired] = $recommendation;

                $module = Module::where('slug', $moduleSlug)->first();

                if (!$module) {
                    continue;
                }

                ModuleRecommendation::create([
                    'business_type_id' => $businessType->id,
                    'module_id' => $module->id,
                    'priority' => $priority,
                    'is_required' => $isRequired,
                ]);
            }
        }
    }
}
