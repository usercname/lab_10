<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\MasterClass;
use App\Models\CreativityType;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected User $visitor;
    protected User $instructor;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->visitor = User::factory()->visitor()->create();
        $this->instructor = User::factory()->instructor()->create();
    }

    /** @test */
    public function visitor_can_access_home_page(): void
    {
        $response = $this->actingAs($this->visitor)->get('/');
        $response->assertStatus(200);
    }

    /** @test */
    public function instructor_can_access_cabinet(): void
    {
        $response = $this->actingAs($this->instructor)->get(route('cabinet.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function visitor_cannot_access_cabinet(): void
    {
        $response = $this->actingAs($this->visitor)->get(route('cabinet.index'));
        $response->assertStatus(403); // или 302 redirect
    }

    /** @test */
    public function guest_redirected_to_login(): void
    {
        $response = $this->get(route('cabinet.index'));
        $response->assertRedirect(route('login'));
    }
}