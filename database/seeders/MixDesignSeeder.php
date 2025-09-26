<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MixDesign;
use App\Models\MixDesignComposition;
use App\Models\User;

class MixDesignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first user as creator
        $user = User::first();
        if (!$user) {
            $this->command->warn('No users found. Please run UserSeeder first.');
            return;
        }

        // Create sample mix designs
        $mixDesigns = [
            [
                'mix_id' => 'K300-S8-D2',
                'name' => 'K-300 Standard Mix',
                'class_strength' => 'K-300',
                'target_strength' => 25.0,
                'slump' => 8.0,
                'durability_class' => 'D2',
                'exposure_class' => 'XC3',
                'notes' => 'Standard mix design for general construction',
                'compositions' => [
                    [
                        'material_type' => 'cement',
                        'material_name' => 'Semen PPC',
                        'percentage' => 12.5,
                        'weight_per_m3' => 350.0,
                        'unit_cost' => 1450.0,
                        'is_cement' => true,
                        'notes' => 'Portland Pozzolan Cement'
                    ],
                    [
                        'material_type' => 'fine_aggregate',
                        'material_name' => 'Agregat Halus',
                        'percentage' => 24.3,
                        'weight_per_m3' => 680.0,
                        'unit_cost' => 180.0,
                        'is_aggregate' => true,
                        'aggregate_size' => '0-5mm',
                        'notes' => 'Natural sand'
                    ],
                    [
                        'material_type' => 'coarse_aggregate',
                        'material_name' => 'Agregat Kasar',
                        'percentage' => 37.5,
                        'weight_per_m3' => 1050.0,
                        'unit_cost' => 160.0,
                        'is_aggregate' => true,
                        'aggregate_size' => '10-20mm',
                        'notes' => 'Crushed stone'
                    ],
                    [
                        'material_type' => 'water',
                        'material_name' => 'Air',
                        'percentage' => 6.25,
                        'weight_per_m3' => 175.0,
                        'unit_cost' => 5.0,
                        'is_water' => true,
                        'notes' => 'Clean water'
                    ],
                    [
                        'material_type' => 'admixture',
                        'material_name' => 'Superplasticizer',
                        'percentage' => 0.125,
                        'weight_per_m3' => 3.5,
                        'unit_cost' => 8500.0,
                        'is_admixture' => true,
                        'notes' => 'High range water reducer'
                    ]
                ]
            ],
            [
                'mix_id' => 'K350-S5-D3',
                'name' => 'K-350 High Strength Mix',
                'class_strength' => 'K-350',
                'target_strength' => 29.0,
                'slump' => 5.0,
                'durability_class' => 'D3',
                'exposure_class' => 'XD2',
                'notes' => 'High strength mix for structural elements',
                'compositions' => [
                    [
                        'material_type' => 'cement',
                        'material_name' => 'Semen PPC',
                        'percentage' => 14.0,
                        'weight_per_m3' => 400.0,
                        'unit_cost' => 1450.0,
                        'is_cement' => true,
                        'notes' => 'Portland Pozzolan Cement'
                    ],
                    [
                        'material_type' => 'fine_aggregate',
                        'material_name' => 'Agregat Halus',
                        'percentage' => 22.4,
                        'weight_per_m3' => 640.0,
                        'unit_cost' => 180.0,
                        'is_aggregate' => true,
                        'aggregate_size' => '0-5mm',
                        'notes' => 'Natural sand'
                    ],
                    [
                        'material_type' => 'coarse_aggregate',
                        'material_name' => 'Agregat Kasar',
                        'percentage' => 35.0,
                        'weight_per_m3' => 1000.0,
                        'unit_cost' => 160.0,
                        'is_aggregate' => true,
                        'aggregate_size' => '10-20mm',
                        'notes' => 'Crushed stone'
                    ],
                    [
                        'material_type' => 'water',
                        'material_name' => 'Air',
                        'percentage' => 5.6,
                        'weight_per_m3' => 160.0,
                        'unit_cost' => 5.0,
                        'is_water' => true,
                        'notes' => 'Clean water'
                    ],
                    [
                        'material_type' => 'admixture',
                        'material_name' => 'Superplasticizer',
                        'percentage' => 0.14,
                        'weight_per_m3' => 4.0,
                        'unit_cost' => 8500.0,
                        'is_admixture' => true,
                        'notes' => 'High range water reducer'
                    ]
                ]
            ],
            [
                'mix_id' => 'K250-S10-D1',
                'name' => 'K-250 Pumpable Mix',
                'class_strength' => 'K-250',
                'target_strength' => 21.0,
                'slump' => 10.0,
                'durability_class' => 'D1',
                'exposure_class' => 'XC2',
                'notes' => 'Pumpable mix for high workability',
                'compositions' => [
                    [
                        'material_type' => 'cement',
                        'material_name' => 'Semen PPC',
                        'percentage' => 11.0,
                        'weight_per_m3' => 300.0,
                        'unit_cost' => 1450.0,
                        'is_cement' => true,
                        'notes' => 'Portland Pozzolan Cement'
                    ],
                    [
                        'material_type' => 'fine_aggregate',
                        'material_name' => 'Agregat Halus',
                        'percentage' => 26.4,
                        'weight_per_m3' => 720.0,
                        'unit_cost' => 180.0,
                        'is_aggregate' => true,
                        'aggregate_size' => '0-5mm',
                        'notes' => 'Natural sand'
                    ],
                    [
                        'material_type' => 'coarse_aggregate',
                        'material_name' => 'Agregat Kasar',
                        'percentage' => 40.0,
                        'weight_per_m3' => 1100.0,
                        'unit_cost' => 160.0,
                        'is_aggregate' => true,
                        'aggregate_size' => '10-20mm',
                        'notes' => 'Crushed stone'
                    ],
                    [
                        'material_type' => 'water',
                        'material_name' => 'Air',
                        'percentage' => 7.0,
                        'weight_per_m3' => 190.0,
                        'unit_cost' => 5.0,
                        'is_water' => true,
                        'notes' => 'Clean water'
                    ],
                    [
                        'material_type' => 'admixture',
                        'material_name' => 'Superplasticizer',
                        'percentage' => 0.15,
                        'weight_per_m3' => 4.2,
                        'unit_cost' => 8500.0,
                        'is_admixture' => true,
                        'notes' => 'High range water reducer'
                    ]
                ]
            ]
        ];

        foreach ($mixDesigns as $mixDesignData) {
            $compositions = $mixDesignData['compositions'];
            unset($mixDesignData['compositions']);

            $mixDesign = MixDesign::create([
                ...$mixDesignData,
                'created_by' => $user->id,
                'approved_by' => $user->id,
                'approved_at' => now()
            ]);

            foreach ($compositions as $compositionData) {
                MixDesignComposition::create([
                    'mix_design_id' => $mixDesign->id,
                    ...$compositionData
                ]);
            }

            $this->command->info("Created mix design: {$mixDesign->name} ({$mixDesign->mix_id})");
        }

        $this->command->info('Mix design seeding completed successfully!');
    }
}
