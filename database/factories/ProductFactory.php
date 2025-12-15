<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::inRandomOrder()->first()?->id ?? Category::factory(),
            'name' => fake()->words(3, true),
            'sku' => strtoupper(fake()->unique()->bothify('???-####')),
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 10, 500),
            'cost' => fake()->randomFloat(2, 5, 250),
            'stock' => fake()->numberBetween(0, 200),
            'min_stock' => fake()->numberBetween(5, 20),
            'is_active' => true,
        ];
    }
}
