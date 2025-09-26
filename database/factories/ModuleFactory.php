<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Module>
 */
class ModuleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => ucwords($name),
            'slug' => \Illuminate\Support\Str::slug($name),
            'description' => fake()->paragraph(),
            'category' => fake()->randomElement(['core', 'manufacturing', 'retail', 'construction', 'logistics', 'healthcare', 'education']),
            'icon' => fake()->randomElement(['puzzle', 'chart', 'cog', 'database', 'users']),
            'version' => fake()->semver(),
            'is_core' => fake()->boolean(30), // 30% chance of being core
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 100),
        ];
    }

    /**
     * Indicate that the module is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the module is a core module.
     */
    public function core(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_core' => true,
        ]);
    }
}
