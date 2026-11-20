<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Enums\AccountStatus;
use App\Enums\UserRole;

/**
 * @extends Factory<User>
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
        ];
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

    public function manager(): static
{
    return $this->state(fn (array $attributes) => ['role' => UserRole::Manager->value]);
}

public function cashier(): static
{
    return $this->state(fn (array $attributes) => ['role' => UserRole::Cashier->value]);
}

public function staff(): static
{
    return $this->state(fn (array $attributes) => ['role' => UserRole::Staff->value]);
}

public function blocked(): static
{
    return $this->state(fn (array $attributes) => ['account_status' => AccountStatus::Blocked->value]);
}
}
