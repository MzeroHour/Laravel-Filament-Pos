<?php

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [

            'name' => $this->faker->word(),
            'price' => $this->faker->randomFloat(2, 0, 100),
            'sku' => Str::upper(Str::random(10)),
            'quantity' => $this->faker->numberBetween(1, 100),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'description' => $this->faker->sentence(),
        ];
    }
}
