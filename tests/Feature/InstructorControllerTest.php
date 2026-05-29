<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\MasterClass;
use App\Models\CreativityType;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InstructorControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_instructor_can_view_cabinet(): void
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        
        $response = $this->actingAs($instructor)->get(route('cabinet.index'));
        $response->assertStatus(200);
    }

    public function test_instructor_can_create_masterclass(): void
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $type = CreativityType::factory()->create();

        $response = $this->actingAs($instructor)->post(route('cabinet.store'), [
            'type_id' => $type->id,
            'title' => 'Тестовый МК',
            'description' => 'Описание длиной более десяти символов для теста',
            'date' => now()->addDay()->format('Y-m-d'),
            'start_time' => '09:00',
            'max_participants' => 10,
            'price' => 1500,
        ]);

        $response->assertRedirect(route('cabinet.index'));
        $this->assertDatabaseHas('master_classes', ['title' => 'Тестовый МК']);
    }

    public function test_instructor_can_edit_masterclass(): void
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $type = CreativityType::factory()->create();
        
        $masterClass = MasterClass::create([
            'instructor_id' => $instructor->id,
            'type_id' => $type->id,
            'title' => 'Старый МК',
            'description' => 'Описание длиной более десяти символов',
            'date' => now()->addDay()->format('Y-m-d'),
            'start_time' => '09:00',
            'max_participants' => 10,
            'price' => 1000,
        ]);

        $response = $this->actingAs($instructor)
            ->put(route('cabinet.update', $masterClass->id), [
                'description' => 'Новое описание для теста',
                'price' => 2000,
            ]);

        $response->assertRedirect(route('cabinet.index'));
        $this->assertDatabaseHas('master_classes', [
            'id' => $masterClass->id,
            'description' => 'Новое описание для теста',
            'price' => 2000,
        ]);
    }
}