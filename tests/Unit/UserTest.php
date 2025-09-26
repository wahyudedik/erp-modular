<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\BusinessType;
use App\Models\UserModule;
use App\Models\Module;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_created()
    {
        $businessType = BusinessType::factory()->create();

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'business_type_id' => $businessType->id,
            'company_name' => 'Test Company',
            'phone' => '1234567890',
            'address' => 'Test Address',
            'is_active' => true,
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertTrue($user->is_active);
    }

    public function test_user_belongs_to_business_type()
    {
        $businessType = BusinessType::factory()->create();
        $user = User::factory()->create(['business_type_id' => $businessType->id]);

        $this->assertInstanceOf(BusinessType::class, $user->businessType);
        $this->assertEquals($businessType->id, $user->businessType->id);
    }

    public function test_user_has_many_user_modules()
    {
        $user = User::factory()->create();
        $module = Module::factory()->create();

        $userModule = UserModule::create([
            'user_id' => $user->id,
            'module_id' => $module->id,
            'is_active' => true,
            'activated_at' => now(),
        ]);

        $this->assertTrue($user->userModules->contains($userModule));
    }

    public function test_user_has_active_modules()
    {
        $user = User::factory()->create();
        $module1 = Module::factory()->create();
        $module2 = Module::factory()->create();

        // Create active module
        UserModule::create([
            'user_id' => $user->id,
            'module_id' => $module1->id,
            'is_active' => true,
            'activated_at' => now(),
        ]);

        // Create inactive module
        UserModule::create([
            'user_id' => $user->id,
            'module_id' => $module2->id,
            'is_active' => false,
        ]);

        $activeModules = $user->activeModules;
        $this->assertCount(1, $activeModules);
        $this->assertEquals($module1->id, $activeModules->first()->module_id);
    }

    public function test_user_has_module_access()
    {
        $user = User::factory()->create();
        $module = Module::factory()->create(['slug' => 'test-module']);

        UserModule::create([
            'user_id' => $user->id,
            'module_id' => $module->id,
            'is_active' => true,
            'activated_at' => now(),
        ]);

        $this->assertTrue($user->hasModuleAccess('test-module'));
        $this->assertFalse($user->hasModuleAccess('non-existent-module'));
    }

    public function test_user_can_get_module_recommendations()
    {
        $businessType = BusinessType::factory()->create();
        $user = User::factory()->create(['business_type_id' => $businessType->id]);
        $module = Module::factory()->create();

        // Create module recommendation
        $businessType->moduleRecommendations()->create([
            'module_id' => $module->id,
            'priority' => 1,
            'is_required' => true,
        ]);

        $recommendations = $user->getModuleRecommendations();
        $this->assertCount(1, $recommendations);
        $this->assertEquals($module->id, $recommendations->first()->module_id);
    }
}
