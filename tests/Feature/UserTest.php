<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Booking;
use App\Models\MasterClass;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_has_bookings_relationship(): void
    {
        $user = User::factory()->create();
        $masterClass = MasterClass::factory()->create();

        Booking::factory()->create([
            'user_id' => $user->id,
            'master_class_id' => $masterClass->id,
        ]);

        $this->assertEquals(1, $user->bookings->count());
    }

    /** @test */
    public function user_can_be_visitor(): void
    {
        $user = User::factory()->visitor()->create();
        
        $this->assertEquals('visitor', $user->role);
    }

    /** @test */
    public function user_can_be_instructor(): void
    {
        $user = User::factory()->instructor()->create();
        
        $this->assertEquals('instructor', $user->role);
    }

    /** @test */
    public function user_password_is_hashed(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $this->assertTrue(Hash::check('password123', $user->password));
    }

    /** @test */
    public function user_has_full_name(): void
    {
        $user = User::factory()->create([
            'full_name' => 'Иван Иванов',
        ]);

        $this->assertEquals('Иван Иванов', $user->full_name);
    }

    /** @test */
    public function user_has_phone(): void
    {
        $user = User::factory()->create([
            'phone' => '+79991234567',
        ]);

        $this->assertEquals('+79991234567', $user->phone);
    }

    /** @test */
    public function user_email_must_be_unique(): void
    {
        User::factory()->create(['email' => 'test@example.com']);

        $this->expectException(\Illuminate\Database\QueryException::class);
        User::factory()->create(['email' => 'test@example.com']);
    }

    /** @test */
    public function user_can_be_created(): void
    {
        $user = User::create([
            'full_name' => 'Тест Пользователь',
            'email' => 'newuser@example.com',
            'password' => Hash::make('password'),
            'role' => 'visitor',
            'phone' => '+79991112233',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'newuser@example.com',
        ]);
    }

    /** @test */
    public function user_can_be_updated(): void
    {
        $user = User::factory()->create();
        
        $user->update([
            'full_name' => 'Новое Имя',
            'phone' => '+79999999999',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'full_name' => 'Новое Имя',
        ]);
    }

    /** @test */
    public function user_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $user->delete();

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }
}