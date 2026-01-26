<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OrganizationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'industry' => fake()->randomElement([
                'Technology',
                'Healthcare',
                'Finance',
                'Education',
                'Retail',
                'Manufacturing',
            ]),
            'size_range' => fake()->randomElement([
                '1-50',
                '51-200',
                '201-500',
                '501-1000',
                '1000+',
            ]),
        ];
    }
}
