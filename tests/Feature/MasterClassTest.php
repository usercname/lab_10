<?php

namespace Tests\Unit;

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

    public function test_master_class_model_can_be_created(): void
    {
        $instructor = User::factory()->instructor()->create();
        $type = CreativityType::factory()->create();

        $masterClass = MasterClass::create([
            'instructor_id' => $instructor->id,
            'type_id' => $type->id,
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

    public function test_master_class_has_instructor_relationship(): void
    {
        $instructor = User::factory()->instructor()->create();
        $masterClass = MasterClass::factory()->create([
            'instructor_id' => $instructor->id,
        ]);

        $this->assertInstanceOf(User::class, $masterClass->instructor);
        $this->assertEquals($instructor->id, $masterClass->instructor->id);
    }

    public function test_master_class_has_type_relationship(): void
    {
        $type = CreativityType::factory()->create();
        $masterClass = MasterClass::factory()->create([
            'type_id' => $type->id,
        ]);

        $this->assertInstanceOf(CreativityType::class, $masterClass->type);
        $this->assertEquals($type->id, $masterClass->type->id);
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
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $masterClass->bookings);
    }

    public function test_free_seats_accessor(): void
    {
        $masterClass = MasterClass::factory()->create([
            'max_participants' => 10,
        ]);

        Booking::factory()->count(3)->create([
            'master_class_id' => $masterClass->id,
        ]);

        $masterClass->refresh();
        
        $this->assertEquals(7, $masterClass->free_seats);
        $this->assertIsInt($masterClass->free_seats);
    }

    public function test_free_seats_zero_when_fully_booked(): void
    {
        $masterClass = MasterClass::factory()->create([
            'max_participants' => 5,
        ]);

        Booking::factory()->count(5)->create([
            'master_class_id' => $masterClass->id,
        ]);

        $masterClass->refresh();
        
        $this->assertEquals(0, $masterClass->free_seats);
    }

    public function test_master_class_fillable_attributes(): void
    {
        $masterClass = new MasterClass();
        
        $fillable = $masterClass->getFillable();
        
        $this->assertContains('instructor_id', $fillable);
        $this->assertContains('type_id', $fillable);
        $this->assertContains('title', $fillable);
        $this->assertContains('description', $fillable);
        $this->assertContains('date', $fillable);
        $this->assertContains('start_time', $fillable);
        $this->assertContains('max_participants', $fillable);
        $this->assertContains('price', $fillable);
    }

    public function test_master_class_timestamps(): void
    {
        $masterClass = MasterClass::factory()->create();
        
        $this->assertNotNull($masterClass->created_at);
        $this->assertNotNull($masterClass->updated_at);
    }

    public function test_master_class_can_be_updated(): void
    {
        $masterClass = MasterClass::factory()->create();
        
        $masterClass->update([
            'title' => 'Новое название',
            'price' => 3000,
            'max_participants' => 20,
        ]);

        $this->assertEquals('Новое название', $masterClass->title);
        $this->assertEquals(3000, $masterClass->price);
        $this->assertEquals(20, $masterClass->max_participants);
    }

    public function test_master_class_can_be_deleted(): void
    {
        $masterClass = MasterClass::factory()->create();
        $id = $masterClass->id;
        
        $masterClass->delete();

        $this->assertDatabaseMissing('master_classes', ['id' => $id]);
    }
}