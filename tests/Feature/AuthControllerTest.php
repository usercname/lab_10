<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $response = $this->post(route('register'), [
            'full_name' => 'Иван Тестов',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '+79991234567',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_user_can_login(): void
    {
    // ✅ Явно создаём посетителя (не инструктора!)
    $user = User::factory()
        ->visitor()  // ← или ->state(['role' => 'visitor'])
        ->create([
            'email' => 'login@example.com',
            'password' => bcrypt('password123'),
        ]);

    $response = $this->post(route('login'), [
        'email' => 'login@example.com',
        'password' => 'password123',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('home')); // ← теперь всегда сработает
}

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->post(route('logout'));
        
        $this->assertGuest();
        $response->assertRedirect('/');
    }
}