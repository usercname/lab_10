<?php

namespace Database\Factories;

use App\Models\MasterClass;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->visitor(),
            'master_class_id' => MasterClass::factory(),
        ];
    }
}