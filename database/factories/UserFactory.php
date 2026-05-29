<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
{
    return [
        'full_name' => $this->faker->name(),
        'email' => $this->faker->unique()->safeEmail(),
        'password' => static::$password ??= Hash::make('password'),
        'remember_token' => Str::random(10),
        'role' => $this->faker->randomElement(['visitor', 'instructor']),
        'phone' => '+7' . $this->faker->numerify('9#########'),
    ];
}

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
    
    // ✅ Состояние для инструктора
    public function instructor(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'instructor',
        ]);
    }
    
    // ✅ Состояние для посетителя
    public function visitor(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'visitor',
        ]);
    }
}