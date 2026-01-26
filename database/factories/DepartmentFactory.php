<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartmentFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->randomElement([
            'Engineering',
            'Product',
            'Marketing',
            'Sales',
            'Human Resources',
            'Finance',
            'Customer Success',
            'Operations',
        ]);

        return [
            'organization_id' => Organization::first()?->id ?? Organization::factory(),
            'name' => $name,
            'code' => strtoupper(substr($name, 0, 3)) . fake()->unique()->numberBetween(100, 999),
            'is_active' => true,
            'manager_id' => null, // Set after users are created
        ];
    }
}
