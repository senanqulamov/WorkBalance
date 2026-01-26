<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeamFactory extends Factory
{
    protected $model = Team::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name' => $this->faker->randomElement([
                'Engineering Team',
                'Design Team',
                'Marketing Team',
                'Sales Team',
                'Support Team',
                'Product Team',
                'Operations Team',
            ]) . ' ' . $this->faker->numberBetween(1, 10),
            'description' => $this->faker->optional(0.7)->paragraph(),
            'manager_id' => User::factory(),
            'is_active' => true,
        ];
    }
}
