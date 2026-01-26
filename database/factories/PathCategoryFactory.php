<?php

namespace Database\Factories;

use App\Models\PathCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PathCategoryFactory extends Factory
{
    protected $model = PathCategory::class;

    public function definition(): array
    {
        $name = $this->faker->randomElement([
            'Stress Management',
            'Conflict Resolution',
            'Motivation & Energy',
            'Work-Life Balance',
            'Communication Skills',
            'Emotional Regulation',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->paragraph(),
            'icon' => $this->faker->randomElement(['heart', 'star', 'shield', 'sun', 'moon']),
            'color' => $this->faker->randomElement(['blue', 'green', 'purple', 'indigo', 'cyan']),
            'sort_order' => $this->faker->numberBetween(0, 100),
        ];
    }
}
