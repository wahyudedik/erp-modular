<?php

namespace Tests\Unit;

use App\Models\BusinessType;
use App\Models\User;
use App\Models\Module;
use App\Models\ModuleRecommendation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessTypeTest extends TestCase
{
    use RefreshDatabase;

    public function test_business_type_can_be_created()
    {
        $businessType = BusinessType::create([
            'name' => 'Test Business',
            'slug' => 'test-business',
            'description' => 'Test business description',
            'icon' => 'building',
            'color' => '#ff0000',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->assertInstanceOf(BusinessType::class, $businessType);
        $this->assertEquals('Test Business', $businessType->name);
        $this->assertEquals('test-business', $businessType->slug);
        $this->assertTrue($businessType->is_active);
    }

    public function test_business_type_has_many_users()
    {
        $businessType = BusinessType::factory()->create();
        $user1 = User::factory()->create(['business_type_id' => $businessType->id]);
        $user2 = User::factory()->create(['business_type_id' => $businessType->id]);

        $this->assertCount(2, $businessType->users);
        $this->assertTrue($businessType->users->contains($user1));
        $this->assertTrue($businessType->users->contains($user2));
    }

    public function test_business_type_has_many_module_recommendations()
    {
        $businessType = BusinessType::factory()->create();
        $module = Module::factory()->create();

        $recommendation = ModuleRecommendation::create([
            'business_type_id' => $businessType->id,
            'module_id' => $module->id,
            'priority' => 1,
            'is_required' => true,
        ]);

        $this->assertCount(1, $businessType->moduleRecommendations);
        $this->assertTrue($businessType->moduleRecommendations->contains($recommendation));
    }

    public function test_business_type_has_recommended_modules()
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

        $recommendedModules = $businessType->recommendedModules;
        $this->assertCount(2, $recommendedModules);
        $this->assertTrue($recommendedModules->contains($module1));
        $this->assertTrue($recommendedModules->contains($module2));
    }

    public function test_business_type_active_scope()
    {
        BusinessType::factory()->create(['is_active' => true]);
        BusinessType::factory()->create(['is_active' => false]);
        BusinessType::factory()->create(['is_active' => true]);

        $activeBusinessTypes = BusinessType::active()->get();
        $this->assertCount(2, $activeBusinessTypes);
        $this->assertTrue($activeBusinessTypes->every(fn($bt) => $bt->is_active));
    }

    public function test_business_type_ordered_scope()
    {
        BusinessType::factory()->create(['sort_order' => 3]);
        BusinessType::factory()->create(['sort_order' => 1]);
        BusinessType::factory()->create(['sort_order' => 2]);

        $orderedBusinessTypes = BusinessType::ordered()->get();
        $this->assertEquals(1, $orderedBusinessTypes->first()->sort_order);
        $this->assertEquals(3, $orderedBusinessTypes->last()->sort_order);
    }

    public function test_business_type_route_key_name()
    {
        $businessType = BusinessType::factory()->create(['slug' => 'test-slug']);
        $this->assertEquals('slug', $businessType->getRouteKeyName());
    }
}
