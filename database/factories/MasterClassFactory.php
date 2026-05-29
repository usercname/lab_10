<?php

namespace Database\Factories;

use App\Models\CreativityType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class MasterClassFactory extends Factory
{
    public function definition(): array
{
    return [
        'instructor_id' => User::factory()->instructor(),
        'type_id' => CreativityType::factory(),
        'title' => $this->faker->unique()->sentence(3), // ← unique!
        'description' => $this->faker->paragraph(),
        'date' => now()->addDays(rand(1, 30))->format('Y-m-d'),
        'start_time' => $this->faker->randomElement(['09:00', '11:00', '13:00', '15:00']),
        'max_participants' => $this->faker->numberBetween(5, 20),
        'price' => $this->faker->numberBetween(500, 5000),
    ];
}
    
    // ✅ Состояние для прошедшего МК
    public function past(): static
    {
        return $this->state(fn (array $attributes) => [
            'date' => Carbon::now()->subDays(rand(1, 30))->format('Y-m-d'),
        ]);
    }
    
    // ✅ Состояние для будущего МК
    public function future(): static
    {
        return $this->state(fn (array $attributes) => [
            'date' => Carbon::now()->addDays(rand(1, 30))->format('Y-m-d'),
        ]);
    }
}