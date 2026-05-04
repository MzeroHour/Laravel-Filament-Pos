<?php

namespace Database\Factories;

use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PaymentMethod>
 */
class PaymentMethodFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['Cash', 'Credit Card', 'Mobile Payment']),
            'description' => $this->faker->sentence(),
            'type' => $this->faker->randomElement(['cash', 'card', 'mobile']),
        ];
    }
}
