<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\MasterClass;
use App\Models\User;
use App\Models\CreativityType;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class MasterClassTest extends TestCase
{
    use RefreshDatabase;

    protected User $instructor;
    protected CreativityType $type;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->instructor = User::factory()->instructor()->create();
        $this->type = CreativityType::factory()->create();
    }

    public function test_master_class_has_instructor_relationship(): void
    {
        $masterClass = MasterClass::factory()->create([
            'instructor_id' => $this->instructor->id,
        ]);

        $this->assertEquals($this->instructor->id, $masterClass->instructor->id);
    }

    public function test_master_class_has_type_relationship(): void
    {
        $masterClass = MasterClass::factory()->create([
            'type_id' => $this->type->id,
        ]);

        $this->assertEquals($this->type->id, $masterClass->type->id);
    }

    public function test_master_class_has_bookings_relationship(): void
    {
        $masterClass = MasterClass::factory()->create([
            'max_participants' => 10,
        ]);

        Booking::factory()->count(3)->create([
            'master_class_id' => $masterClass->id,
        ]);

        $this->assertEquals(3, $masterClass->bookings->count());
    }

    public function test_free_seats_calculated_correctly(): void
    {
        $masterClass = MasterClass::factory()->create([
            'max_participants' => 10,
        ]);

        Booking::factory()->count(3)->create([
            'master_class_id' => $masterClass->id,
        ]);

        $masterClass->refresh();
        $this->assertEquals(7, $masterClass->free_seats);
    }

    public function test_master_class_can_be_created(): void
    {
        $masterClass = MasterClass::create([
            'instructor_id' => $this->instructor->id,
            'type_id' => $this->type->id,
            'title' => 'Тестовый МК',
            'description' => 'Описание',
            'date' => Carbon::now()->addDays(5)->format('Y-m-d'),
            'start_time' => '09:00',
            'max_participants' => 15,
            'price' => 2000,
        ]);

        $this->assertDatabaseHas('master_classes', [
            'id' => $masterClass->id,
            'title' => 'Тестовый МК',
        ]);
    }

    public function test_master_class_can_be_updated(): void
    {
        $masterClass = MasterClass::factory()->create();
        
        $masterClass->update([
            'title' => 'Новое название',
            'price' => 3000,
        ]);

        $this->assertDatabaseHas('master_classes', [
            'id' => $masterClass->id,
            'title' => 'Новое название',
            'price' => 3000,
        ]);
    }

    public function test_master_class_can_be_deleted(): void
    {
        $masterClass = MasterClass::factory()->create();
        $masterClass->delete();

        $this->assertDatabaseMissing('master_classes', [
            'id' => $masterClass->id,
        ]);
    }
}