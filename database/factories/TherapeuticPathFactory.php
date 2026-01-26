<?php

namespace Database\Factories;

use App\Models\PathCategory;
use App\Models\TherapeuticPath;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TherapeuticPathFactory extends Factory
{
    protected $model = TherapeuticPath::class;

    public function definition(): array
    {
        $name = $this->faker->randomElement([
            'Deadline Pressure Management',
            'Conflict Resolution Flow',
            'Burnout Prevention Path',
            'Motivation Recovery',
            'Anxiety Regulation',
            'Emotional Exhaustion Support',
        ]);

        return [
            'path_category_id' => PathCategory::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->paragraph(),
            'situation_trigger' => $this->faker->sentence(),
            'steps_data' => [
                ['type' => 'validation', 'content' => $this->faker->sentence()],
                ['type' => 'regulation', 'content' => $this->faker->sentence()],
                ['type' => 'insight', 'content' => $this->faker->sentence()],
                ['type' => 'action', 'content' => $this->faker->sentence()],
            ],
            'estimated_duration_minutes' => $this->faker->numberBetween(5, 20),
            'is_active' => true,
        ];
    }
}
