<?php

namespace Database\Factories;

use App\Models\Log;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LogFactory extends Factory
{
    protected $model = Log::class;

    public function definition(): array
    {
        $types = ['info', 'warning', 'error', 'success', 'create', 'update', 'delete', 'login', 'logout'];
        $type = $this->faker->randomElement($types);

        $actions = [
            'users.create', 'users.update', 'users.delete',
            'products.create', 'products.update', 'products.delete',
            'orders.create', 'orders.update', 'orders.delete',
            'markets.create', 'markets.update', 'markets.delete',
            'settings.update', 'auth.login', 'auth.logout',
        ];

        $models = ['User', 'Product', 'Order', 'Market', null];

        return [
            'user_id' => null, // Should be set explicitly by seeder for better control
            'type' => $type,
            'action' => $this->faker->randomElement($actions),
            'model' => $this->faker->randomElement($models),
            'model_id' => $this->faker->boolean(60) ? $this->faker->numberBetween(1, 100) : null,
            'message' => $this->faker->sentence(12),
            'metadata' => $this->faker->boolean(40) ? [
                'changes' => ['field' => $this->faker->word(), 'old' => $this->faker->word(), 'new' => $this->faker->word()],
                'additional_info' => $this->faker->sentence(),
            ] : null,
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'created_at' => $this->faker->dateTimeBetween('-60 days', 'now'),
        ];
    }
}
