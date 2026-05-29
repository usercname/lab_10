<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Booking;
use App\Models\MasterClass;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class CreativityTypeTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_model_can_be_created(): void
    {
        $user = User::create([
            'full_name' => 'Иван Тестов',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'role' => 'visitor',
            'phone' => '+79991234567',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'test@example.com',
        ]);
    }

    public function test_user_password_is_hashed(): void
    {
        $user = User::factory()->create([
            'password' => 'plain_password',
        ]);

        $this->assertTrue(Hash::check('plain_password', $user->password));
        $this->assertNotEquals('plain_password', $user->password);
    }

    public function test_user_visitor_state(): void
    {
        $user = User::factory()->visitor()->create();
        
        $this->assertEquals('visitor', $user->role);
    }

    public function test_user_instructor_state(): void
    {
        $user = User::factory()->instructor()->create();
        
        $this->assertEquals('instructor', $user->role);
    }

    public function test_user_fillable_attributes(): void
    {
        $user = new User();
        
        $fillable = $user->getFillable();
        
        $this->assertContains('full_name', $fillable);
        $this->assertContains('email', $fillable);
        $this->assertContains('password', $fillable);
        $this->assertContains('role', $fillable);
        $this->assertContains('phone', $fillable);
    }

    public function test_user_hidden_attributes(): void
    {
        $user = new User();
        
        $hidden = $user->getHidden();
        
        $this->assertContains('password', $hidden);
        $this->assertContains('remember_token', $hidden);
    }

    public function test_user_timestamps(): void
    {
        $user = User::factory()->create();
        
        $this->assertNotNull($user->created_at);
        $this->assertNotNull($user->updated_at);
    }

    public function test_user_can_be_updated(): void
    {
        $user = User::factory()->create();
        
        $user->update([
            'full_name' => 'Новое Имя',
            'phone' => '+79999999999',
        ]);

        $this->assertEquals('Новое Имя', $user->full_name);
        $this->assertEquals('+79999999999', $user->phone);
    }

    public function test_user_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $id = $user->id;
        
        $user->delete();

        $this->assertDatabaseMissing('users', ['id' => $id]);
    }

    public function test_user_email_must_be_unique(): void
    {
        User::factory()->create(['email' => 'unique@example.com']);

        $this->expectException(\Illuminate\Database\QueryException::class);
        User::factory()->create(['email' => 'unique@example.com']);
    }

    public function test_user_has_remember_token(): void
    {
        $user = User::factory()->create();
        
        $this->assertNotNull($user->remember_token);
        $this->assertIsString($user->remember_token);
    }
}