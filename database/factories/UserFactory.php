<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
            'is_admin' => false,
            'is_active' => true,
            // WorkBalance employee profile fields
            'phone' => fake()->optional()->phoneNumber(),
            'timezone' => fake()->timezone(),
            'language' => 'en',
        ];
    }

    /**
     * Configure the factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function ($user) {
            // Support multi-role users via `roles_to_attach` (array of role names).
            $roleNames = [];

            if (!empty($user->roles_to_attach) && is_array($user->roles_to_attach)) {
                $roleNames = $user->roles_to_attach;
            }

            // Ensure unique + normalized
            $roleNames = collect($roleNames)
                ->filter()
                ->map(fn ($r) => strtolower((string) $r))
                ->unique()
                ->values()
                ->all();

            if (!empty($roleNames)) {
                foreach ($roleNames as $roleName) {
                    $role = Role::where('name', $roleName)->first();

                    if (!$role) {
                        throw new \RuntimeException(
                            "UserFactory: Role '{$roleName}' not found for user ID {$user->id}. " .
                            "Ensure RolesAndPermissionsSeeder runs first."
                        );
                    }

                    if (!$user->roles()->where('roles.id', $role->id)->exists()) {
                        $user->roles()->attach($role->id);
                    }

                    // Sync is_admin legacy flag
                    if ($roleName === 'admin' && !$user->is_admin) {
                        $user->forceFill(['is_admin' => true])->saveQuietly();
                    }
                }
            }
        });
    }

    /**
     * Attach multiple roles (pivot role_user) to the user after creation.
     */
    public function roles(array $roles): static
    {
        return $this->state(fn (array $attributes) => [
            // Stored as a transient attribute; used in configure()->afterCreating()
            'roles_to_attach' => $roles,
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the model has two-factor authentication configured.
     */
    public function withTwoFactor(): static
    {
        return $this->state(fn (array $attributes) => [
            'two_factor_secret' => Str::random(10),
            'two_factor_recovery_codes' => Str::random(10),
            'two_factor_confirmed_at' => now(),
        ]);
    }

    /**
     * Create an employee user.
     */
    public function employee(): static
    {
        return $this->roles(['employee']);
    }

    /**
     * Create a manager user.
     */
    public function manager(): static
    {
        return $this->roles(['manager']);
    }

    /**
     * Create an owner user.
     */
    public function owner(): static
    {
        return $this->roles(['owner']);
    }

    /**
     * Create an admin user.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_admin' => true,
        ])->roles(['admin']);
    }

    /**
     * Create an inactive user.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
