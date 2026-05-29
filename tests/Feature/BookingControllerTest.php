<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Booking;
use App\Models\MasterClass;
use App\Models\CreativityType;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookingControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected MasterClass $masterClass;
    protected CreativityType $type;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Создаём тип творчества
        $this->type = CreativityType::factory()->create();
        
        // Создаём пользователя
        $this->user = User::factory()->create(['role' => 'visitor']);
        
        // Создаём мастер-класс с местами
        $this->masterClass = MasterClass::factory()->create([
            'type_id' => $this->type->id,
            'instructor_id' => User::factory()->create(['role' => 'instructor'])->id,
            'max_participants' => 10,
            'date' => now()->addDays(5)->format('Y-m-d'),
        ]);
    }

    /** @test */
    public function guest_cannot_access_confirm_page(): void
    {
        $response = $this->get(route('booking.confirm', $this->masterClass->id));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function user_can_view_confirm_page(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('booking.confirm', $this->masterClass->id));
        
        $response->assertStatus(200);
        $response->assertViewIs('confirm');
        $response->assertViewHas('masterClass', $this->masterClass);
    }

    /** @test */
    public function confirm_page_redirects_if_no_free_seats(): void
    {
        // Заполняем все места
        $this->masterClass->update(['max_participants' => 1]);
        Booking::factory()->create(['master_class_id' => $this->masterClass->id]);
        
        $response = $this->actingAs($this->user)
            ->get(route('booking.confirm', $this->masterClass->id));
        
        $response->assertRedirect();
        $response->assertSessionHas('error', 'К сожалению, свободных мест больше нет.');
    }

    /** @test */
    public function confirm_page_redirects_if_already_booked(): void
    {
        // Создаём бронь для этого пользователя
        Booking::factory()->create([
            'user_id' => $this->user->id,
            'master_class_id' => $this->masterClass->id,
        ]);
        
        $response = $this->actingAs($this->user)
            ->get(route('booking.confirm', $this->masterClass->id));
        
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Вы уже записаны на этот мастер-класс.');
    }

    /** @test */
    public function user_can_cancel_booking(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('booking.process', $this->masterClass->id), [
                'action' => 'cancel',
            ]);
        
        $response->assertRedirect(route('category.show', $this->masterClass->type_id));
        $response->assertSessionHas('message', 'Запись была отменена.');
    }

    /** @test */
    public function booking_fails_if_no_free_seats(): void
    {
        // Заполняем все места
        $this->masterClass->update(['max_participants' => 1]);
        Booking::factory()->create(['master_class_id' => $this->masterClass->id]);
        
        $response = $this->actingAs($this->user)
            ->post(route('booking.process', $this->masterClass->id));
        
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Мест больше нет. Попробуйте другой мастер-класс.');
    }

    /** @test */
    public function booking_fails_if_already_booked(): void
    {
        // Создаём бронь для этого пользователя
        Booking::factory()->create([
            'user_id' => $this->user->id,
            'master_class_id' => $this->masterClass->id,
        ]);
        
        $response = $this->actingAs($this->user)
            ->post(route('booking.process', $this->masterClass->id));
        
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Вы уже записаны на этот мастер-класс.');
    }

    /** @test */
    public function user_can_successfully_book_masterclass(): void
    {
        $this->assertDatabaseCount('bookings', 0);
        
        $response = $this->actingAs($this->user)
            ->post(route('booking.process', $this->masterClass->id));
        
        $response->assertRedirect(route('category.show', $this->masterClass->type_id));
        $response->assertSessionHas('message', 'Вы успешно записаны на мастер-класс!');
        
        $this->assertDatabaseHas('bookings', [
            'user_id' => $this->user->id,
            'master_class_id' => $this->masterClass->id,
        ]);
    }

    /** @test */
    public function booking_reduces_free_seats_count(): void
    {
        $initialSeats = $this->masterClass->free_seats;
        
        $this->actingAs($this->user)
            ->post(route('booking.process', $this->masterClass->id));
        
        $this->masterClass->refresh();
        $this->assertEquals($initialSeats - 1, $this->masterClass->free_seats);
    }
}