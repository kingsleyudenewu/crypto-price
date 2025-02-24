<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CryptoPair>
 */
class CryptoPairFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pair' => 'BTCUSD',
            'price_change' => $this->faker->randomFloat(2, 100, 1000),
            'average_price' => $this->faker->randomFloat(2, 100, 1000),
            'last_updated' => now(),
        ];
    }
}
