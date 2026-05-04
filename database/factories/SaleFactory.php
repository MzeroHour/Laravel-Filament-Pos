<?php

namespace Database\Factories;

use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sale>
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
        return [
            //
            // 'customer_id' => null, // You can set this to a valid customer ID if needed
            // 'payment_method_id' => null, // You can set this to a valid payment method ID if needed
            // 'total_amount' => $this->faker->randomFloat(2, 0, 1000),
            // 'paid_amount' => $this->faker->randomFloat(2, 0, 1000),
            // 'discount_amount' => $this->faker->randomFloat(2, 0, 100),
            // 'tax_amount' => $this->faker->randomFloat(2, 0, 100),
            // 'is_paid' => $this->faker->boolean(),
        ];
    }
}
