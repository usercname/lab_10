<?php
namespace Tests\Unit;

use Tests\TestCase;
use App\Models\MasterClass;
use App\Models\User;
use App\Models\CreativityType;

class MasterClassTest extends TestCase
{
    public function test_master_class_can_be_created()
    {
        $instructor = User::factory()->create();
        $type = CreativityType::factory()->create();

        $mc = MasterClass::factory()->create([
            'instructor_id' => $instructor->id,
            'type_id' => $type->id,
        ]);

        $this->assertDatabaseHas('master_classes', ['id' => $mc->id]);
    }

    public function test_master_class_accessors_work()
    {
        $mc = MasterClass::factory()->make(['max_participants' => 10]);
        // Проверяем, что аксессор free_seats не падает с ошибкой
        $seats = $mc->free_seats;
        $this->assertIsNumeric($seats);
    }

    public function test_master_class_update()
    {
        $mc = MasterClass::factory()->create();
        $mc->update(['title' => 'Updated Title', 'price' => 5000]);
        
        $this->assertDatabaseHas('master_classes', [
            'id' => $mc->id,
            'title' => 'Updated Title',
            'price' => 5000,
        ]);
    }
}