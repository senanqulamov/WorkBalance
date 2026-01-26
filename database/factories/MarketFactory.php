<?php

namespace Database\Factories;

use App\Models\Market;
use Illuminate\Database\Eloquent\Factories\Factory;

class MarketFactory extends Factory
{
    protected $model = Market::class;

    public function definition(): array
    {
        return [
            'user_id' => null, // Must be set explicitly; do not auto-create users
            'name' => $this->faker->city(),
            'location' => $this->faker->city().', '.$this->faker->country(),
            'image_path' => null,
        ];
    }
}
