<?php
namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
{
    public function test_user_can_be_created()
    {
        $user = User::factory()->create(['password' => 'secret']);
        $this->assertDatabaseHas('users', ['email' => $user->email]);
    }

    public function test_user_password_is_hashed()
    {
        $user = User::factory()->create(['password' => 'secret']);
        $this->assertTrue(Hash::check('secret', $user->password));
    }

    public function test_user_role_assignment()
    {
        $visitor = User::factory()->visitor()->create();
        $instructor = User::factory()->instructor()->create();

        $this->assertEquals('visitor', $visitor->role);
        $this->assertEquals('instructor', $instructor->role);
    }

    public function test_user_fillable_fields()
    {
        $user = User::factory()->make();
        $this->assertNotNull($user->full_name);
        $this->assertNotNull($user->email);
        $this->assertNotNull($user->phone);
    }
}