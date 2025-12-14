<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sale>
 */
class SaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 50, 1000);
        $tax = $subtotal * 0.10;
        $discount = fake()->randomFloat(2, 0, 50);
        
        return [
            'user_id' => \App\Models\User::factory(),
            'customer_id' => fake()->boolean(70) ? \App\Models\Customer::factory() : null,
            'invoice_number' => 'INV-' . date('Ymd') . '-' . str_pad(fake()->unique()->numberBetween(1, 999999), 6, '0', STR_PAD_LEFT),
            'subtotal' => $subtotal,
            'tax' => $tax,
            'discount' => $discount,
            'total' => $subtotal + $tax - $discount,
            'payment_method' => fake()->randomElement(['cash', 'card', 'transfer']),
            'status' => fake()->randomElement(['pending', 'completed', 'cancelled']),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
