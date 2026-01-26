<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrganizationFactory extends Factory
{
    protected $model = Organization::class;

    public function definition(): array
    {
        $name = $this->faker->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'industry' => $this->faker->randomElement([
                'Technology',
                'Healthcare',
                'Finance',
                'Education',
                'Manufacturing',
                'Retail',
                'Consulting',
            ]),
            'size' => $this->faker->randomElement(['1-10', '11-50', '51-200', '201-500', '500+']),
            'primary_contact_email' => $this->faker->companyEmail(),
            'settings' => [
                'minimum_cohort_size' => 5,
                'enable_burnout_alerts' => true,
                'enable_stress_tracking' => true,
            ],
        ];
    }
}
