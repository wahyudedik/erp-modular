<?php

namespace Tests\Unit;

use App\Models\Module;
use App\Models\User;
use App\Models\UserModule;
use App\Models\BusinessType;
use App\Models\ModuleRecommendation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_module_can_be_created()
    {
        $module = Module::create([
            'name' => 'Test Module',
            'slug' => 'test-module',
            'description' => 'Test module description',
            'category' => 'core',
            'icon' => 'puzzle',
            'version' => '1.0.0',
            'is_core' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->assertInstanceOf(Module::class, $module);
        $this->assertEquals('Test Module', $module->name);
        $this->assertEquals('test-module', $module->slug);
        $this->assertTrue($module->is_core);
        $this->assertTrue($module->is_active);
    }

    public function test_module_has_many_user_modules()
    {
        $module = Module::factory()->create();
        $user = User::factory()->create();

        $userModule = UserModule::create([
            'user_id' => $user->id,
            'module_id' => $module->id,
            'is_active' => true,
            'activated_at' => now(),
        ]);

        $this->assertCount(1, $module->userModules);
        $this->assertTrue($module->userModules->contains($userModule));
    }

    public function test_module_has_many_module_recommendations()
    {
        $module = Module::factory()->create();
        $businessType = BusinessType::factory()->create();

        $recommendation = ModuleRecommendation::create([
            'business_type_id' => $businessType->id,
            'module_id' => $module->id,
            'priority' => 1,
            'is_required' => true,
        ]);

        $this->assertCount(1, $module->moduleRecommendations);
        $this->assertTrue($module->moduleRecommendations->contains($recommendation));
    }

    public function test_module_has_recommended_for_business_types()
    {
        $module = Module::factory()->create();
        $businessType1 = BusinessType::factory()->create();
        $businessType2 = BusinessType::factory()->create();

        // Create recommendations
        ModuleRecommendation::create([
            'business_type_id' => $businessType1->id,
            'module_id' => $module->id,
            'priority' => 1,
            'is_required' => true,
        ]);

        ModuleRecommendation::create([
            'business_type_id' => $businessType2->id,
            'module_id' => $module->id,
            'priority' => 2,
            'is_required' => false,
        ]);

        $recommendedBusinessTypes = $module->recommendedForBusinessTypes;
        $this->assertCount(2, $recommendedBusinessTypes);
        $this->assertTrue($recommendedBusinessTypes->contains($businessType1));
        $this->assertTrue($recommendedBusinessTypes->contains($businessType2));
    }

    public function test_module_active_scope()
    {
        Module::factory()->create(['is_active' => true]);
        Module::factory()->create(['is_active' => false]);
        Module::factory()->create(['is_active' => true]);

        $activeModules = Module::active()->get();
        $this->assertCount(2, $activeModules);
        $this->assertTrue($activeModules->every(fn($module) => $module->is_active));
    }

    public function test_module_core_scope()
    {
        Module::factory()->create(['is_core' => true]);
        Module::factory()->create(['is_core' => false]);
        Module::factory()->create(['is_core' => true]);

        $coreModules = Module::core()->get();
        $this->assertCount(2, $coreModules);
        $this->assertTrue($coreModules->every(fn($module) => $module->is_core));
    }

    public function test_module_by_category_scope()
    {
        Module::factory()->create(['category' => 'core']);
        Module::factory()->create(['category' => 'manufacturing']);
        Module::factory()->create(['category' => 'core']);

        $coreModules = Module::byCategory('core')->get();
        $this->assertCount(2, $coreModules);
        $this->assertTrue($coreModules->every(fn($module) => $module->category === 'core'));
    }

    public function test_module_ordered_scope()
    {
        Module::factory()->create(['sort_order' => 3]);
        Module::factory()->create(['sort_order' => 1]);
        Module::factory()->create(['sort_order' => 2]);

        $orderedModules = Module::ordered()->get();
        $this->assertEquals(1, $orderedModules->first()->sort_order);
        $this->assertEquals(3, $orderedModules->last()->sort_order);
    }

    public function test_module_route_key_name()
    {
        $module = Module::factory()->create(['slug' => 'test-slug']);
        $this->assertEquals('slug', $module->getRouteKeyName());
    }

    public function test_module_get_categories()
    {
        $categories = Module::getCategories();

        $this->assertIsArray($categories);
        $this->assertArrayHasKey('core', $categories);
        $this->assertArrayHasKey('manufacturing', $categories);
        $this->assertArrayHasKey('retail', $categories);
        $this->assertEquals('Core Modules', $categories['core']);
    }
}
