<?php

namespace Tests\Feature;

use App\Models\BusinessType;
use App\Models\Module;
use App\Models\ModuleRecommendation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessTypeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_business_types()
    {
        BusinessType::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/business-types');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'description',
                        'icon',
                        'color',
                        'is_active',
                        'sort_order'
                    ]
                ],
                'message'
            ])
            ->assertJson([
                'success' => true
            ]);

        $this->assertCount(3, $response->json('data'));
    }

    public function test_can_get_specific_business_type()
    {
        $businessType = BusinessType::factory()->create([
            'name' => 'Test Business Type',
            'slug' => 'test-business-type'
        ]);

        $response = $this->getJson("/api/v1/business-types/{$businessType->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'slug',
                    'description',
                    'recommended_modules'
                ],
                'message'
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $businessType->id,
                    'name' => 'Test Business Type',
                    'slug' => 'test-business-type'
                ]
            ]);
    }

    public function test_can_get_module_recommendations_for_business_type()
    {
        $businessType = BusinessType::factory()->create();
        $module1 = Module::factory()->create();
        $module2 = Module::factory()->create();

        // Create recommendations
        ModuleRecommendation::create([
            'business_type_id' => $businessType->id,
            'module_id' => $module1->id,
            'priority' => 1,
            'is_required' => true,
        ]);

        ModuleRecommendation::create([
            'business_type_id' => $businessType->id,
            'module_id' => $module2->id,
            'priority' => 2,
            'is_required' => false,
        ]);

        $response = $this->getJson("/api/v1/business-types/{$businessType->id}/module-recommendations");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'business_type_id',
                        'module_id',
                        'priority',
                        'is_required',
                        'module'
                    ]
                ],
                'message'
            ])
            ->assertJson([
                'success' => true
            ]);

        $this->assertCount(2, $response->json('data'));
    }

    public function test_returns_404_for_nonexistent_business_type()
    {
        $response = $this->getJson('/api/v1/business-types/999');

        $response->assertStatus(404);
    }

    public function test_business_types_are_ordered_by_sort_order()
    {
        BusinessType::factory()->create(['sort_order' => 3, 'name' => 'Third']);
        BusinessType::factory()->create(['sort_order' => 1, 'name' => 'First']);
        BusinessType::factory()->create(['sort_order' => 2, 'name' => 'Second']);

        $response = $this->getJson('/api/v1/business-types');

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertEquals('First', $data[0]['name']);
        $this->assertEquals('Second', $data[1]['name']);
        $this->assertEquals('Third', $data[2]['name']);
    }

    public function test_only_active_business_types_are_returned()
    {
        BusinessType::factory()->create(['is_active' => true, 'name' => 'Active Type']);
        BusinessType::factory()->create(['is_active' => false, 'name' => 'Inactive Type']);
        BusinessType::factory()->create(['is_active' => true, 'name' => 'Another Active Type']);

        $response = $this->getJson('/api/v1/business-types');

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(2, $data);

        $names = collect($data)->pluck('name')->toArray();
        $this->assertContains('Active Type', $names);
        $this->assertContains('Another Active Type', $names);
        $this->assertNotContains('Inactive Type', $names);
    }
}
