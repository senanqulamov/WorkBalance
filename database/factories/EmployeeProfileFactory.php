<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeProfileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'department_id' => Department::inRandomOrder()->first()?->id ?? Department::factory(),
            'role_title' => fake()->randomElement([
                'Software Engineer',
                'Senior Developer',
                'Product Manager',
                'Designer',
                'Marketing Specialist',
                'HR Coordinator',
                'Sales Representative',
                'Data Analyst',
            ]),
            'timezone' => fake()->randomElement(['UTC', 'America/New_York', 'Europe/London', 'Asia/Tokyo']),
            'locale' => fake()->randomElement(['en', 'es', 'fr', 'de']),
        ];
    }
}
