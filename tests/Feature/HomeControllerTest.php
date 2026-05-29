<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Booking;
use App\Models\MasterClass;
use App\Models\CreativityType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $instructor;
    protected User $user;
    protected CreativityType $type;

    protected function setUp(): void
    {
        parent::setUp();

        $this->instructor = User::factory()->create(['role' => 'instructor']);
        $this->user = User::factory()->create(['role' => 'visitor']);
        $this->type = CreativityType::factory()->create();
    }

    public function test_guest_can_view_home_page(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertViewIs('home');
        $response->assertViewHas('types');
        $response->assertViewHas('allClasses');
        $response->assertViewHas('myBookings');
    }

    public function test_home_page_shows_all_creativity_types(): void
    {
        CreativityType::factory()->count(2)->create();

        $response = $this->get(route('home'));

        $types = $response->viewData('types');
        $this->assertEquals(3, $types->count());
    }

    public function test_home_page_shows_future_master_classes_only(): void
    {
        MasterClass::factory()->create([
            'instructor_id' => $this->instructor->id,
            'type_id' => $this->type->id,
            'date' => Carbon::now()->subDays(2)->format('Y-m-d'),
        ]);

        $futureClass1 = MasterClass::factory()->create([
            'instructor_id' => $this->instructor->id,
            'type_id' => $this->type->id,
            'date' => Carbon::now()->addDays(2)->format('Y-m-d'),
        ]);
        $futureClass2 = MasterClass::factory()->create([
            'instructor_id' => $this->instructor->id,
            'type_id' => $this->type->id,
            'date' => Carbon::now()->addDays(5)->format('Y-m-d'),
        ]);

        $response = $this->get(route('home'));

        $classes = $response->viewData('allClasses');

        $this->assertEquals(2, $classes->count());
        $this->assertTrue($classes->contains($futureClass1));
        $this->assertTrue($classes->contains($futureClass2));
    }

    public function test_home_page_orders_classes_by_date_and_start_time(): void
    {
        $classLater = MasterClass::factory()->create([
            'instructor_id' => $this->instructor->id,
            'type_id' => $this->type->id,
            'date' => Carbon::now()->addDays(5)->format('Y-m-d'),
            'start_time' => '09:00',
        ]);

        $classEarlier = MasterClass::factory()->create([
            'instructor_id' => $this->instructor->id,
            'type_id' => $this->type->id,
            'date' => Carbon::now()->addDays(2)->format('Y-m-d'),
            'start_time' => '11:00',
        ]);

        $response = $this->get(route('home'));
        $classes = $response->viewData('allClasses');

        $this->assertEquals($classEarlier->id, $classes->first()->id);
        $this->assertEquals($classLater->id, $classes->last()->id);
    }

    public function test_guest_has_empty_bookings_collection(): void
    {
        $masterClass = MasterClass::factory()->create([
            'instructor_id' => $this->instructor->id,
            'type_id' => $this->type->id,
            'date' => Carbon::now()->addDays(1)->format('Y-m-d'),
        ]);
        Booking::factory()->create([
            'user_id' => $this->user->id,
            'master_class_id' => $masterClass->id,
        ]);

        $response = $this->get(route('home'));

        $myBookings = $response->viewData('myBookings');
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $myBookings);
        $this->assertEmpty($myBookings);
    }

    public function test_authenticated_user_sees_their_bookings(): void
    {
        $masterClass = MasterClass::factory()->create([
            'instructor_id' => $this->instructor->id,
            'type_id' => $this->type->id,
            'date' => Carbon::now()->addDays(1)->format('Y-m-d'),
        ]);

        $myBooking = Booking::factory()->create([
            'user_id' => $this->user->id,
            'master_class_id' => $masterClass->id,
        ]);

        $otherUser = User::factory()->create();
        Booking::factory()->create([
            'user_id' => $otherUser->id,
            'master_class_id' => $masterClass->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('home'));

        $myBookings = $response->viewData('myBookings');

        $this->assertEquals(1, $myBookings->count());
        $this->assertTrue($myBookings->contains($myBooking));
    }

    public function test_home_page_loads_relationships_to_prevent_n_plus_one(): void
    {
        $masterClass = MasterClass::factory()->create([
            'instructor_id' => $this->instructor->id,
            'type_id' => $this->type->id,
            'date' => Carbon::now()->addDays(1)->format('Y-m-d'),
        ]);
        Booking::factory()->create([
            'user_id' => $this->user->id,
            'master_class_id' => $masterClass->id,
        ]);

        \DB::enableQueryLog();
        $response = $this->actingAs($this->user)->get(route('home'));
        $queries = \DB::getQueryLog();
        \DB::disableQueryLog();

        $response->assertStatus(200);

        $this->assertLessThanOrEqual(15, count($queries));
    }
}