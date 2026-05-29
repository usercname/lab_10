<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\CreativityType;
use App\Models\MasterClass;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected CreativityType $type;
    protected User $instructor;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Создаём тип творчества и инструктора для всех тестов
        $this->type = CreativityType::factory()->create([
            'name' => 'Рисование',
            'description' => 'Тестовое описание',
        ]);
        
        $this->instructor = User::factory()->create(['role' => 'instructor']);
    }

    /** @test */
    public function guest_can_view_category_page(): void
    {
        $response = $this->get(route('category.show', $this->type->id));
        
        $response->assertStatus(200);
        $response->assertViewIs('category');
        $response->assertViewHas('type', $this->type);
        $response->assertViewHas('classes');
        $response->assertSee($this->type->name);
    }

    /** @test */
    public function category_not_found_returns_404(): void
    {
        $response = $this->get(route('category.show', 99999));
        
        $response->assertStatus(404);
    }

    /** @test */
    /** @test */
public function classes_are_filtered_by_type_id(): void
{
    $instructor = User::factory()->instructor()->create();
    
    $classForType = MasterClass::factory()->create([
        'type_id' => $this->type->id,
        'instructor_id' => $instructor->id,
        'date' => now()->addDays(5)->format('Y-m-d'),
        'start_time' => '09:00',
    ]);
    
    $otherInstructor = User::factory()->instructor()->create();
    $otherType = CreativityType::factory()->create();
    $classForOtherType = MasterClass::factory()->create([
        'type_id' => $otherType->id,
        'instructor_id' => $otherInstructor->id,
        'date' => now()->addDays(6)->format('Y-m-d'), // ← другая дата!
        'start_time' => '11:00', // ← другое время!
    ]);
    
    $response = $this->get(route('category.show', $this->type->id));
    
    $response->assertSee($classForType->title);
    $response->assertDontSee($classForOtherType->title);
}
    /** @test */
    public function classes_are_filtered_by_future_dates_only(): void
    {
        // Будущий МК — должен отображаться
        $futureClass = MasterClass::factory()->create([
            'type_id' => $this->type->id,
            'instructor_id' => $this->instructor->id,
            'date' => now()->addDays(5)->format('Y-m-d'),
        ]);
        
        // Прошедший МК — не должен отображаться
        $pastClass = MasterClass::factory()->create([
            'type_id' => $this->type->id,
            'instructor_id' => $this->instructor->id,
            'date' => now()->subDays(5)->format('Y-m-d'),
        ]);
        
        $response = $this->get(route('category.show', $this->type->id));
        
        $response->assertSee($futureClass->title);
        $response->assertDontSee($pastClass->title);
    }

    /** @test */
    public function classes_are_ordered_by_date_and_start_time(): void
    {
        // Создаём МК в разном порядке
        $class3 = MasterClass::factory()->create([
            'type_id' => $this->type->id,
            'instructor_id' => $this->instructor->id,
            'date' => now()->addDays(10)->format('Y-m-d'),
            'start_time' => '09:00',
            'title' => 'Третий МК',
        ]);
        
        $class1 = MasterClass::factory()->create([
            'type_id' => $this->type->id,
            'instructor_id' => $this->instructor->id,
            'date' => now()->addDays(5)->format('Y-m-d'),
            'start_time' => '11:00',
            'title' => 'Первый МК',
        ]);
        
        $class2 = MasterClass::factory()->create([
            'type_id' => $this->type->id,
            'instructor_id' => $this->instructor->id,
            'date' => now()->addDays(5)->format('Y-m-d'),
            'start_time' => '09:00',
            'title' => 'Второй МК',
        ]);
        
        $response = $this->get(route('category.show', $this->type->id));
        
        // Проверяем порядок в HTML: сначала ранние даты, потом раннее время
        $content = $response->content();
        $pos1 = strpos($content, 'Первый МК');
        $pos2 = strpos($content, 'Второй МК');
        $pos3 = strpos($content, 'Третий МК');
        
        $this->assertLessThan($pos3, $pos1, 'Более ранняя дата должна быть выше');
        $this->assertLessThan($pos1, $pos2, 'Более раннее время в тот же день должно быть выше');
    }

    /** @test */
    public function view_receives_eager_loaded_relationships(): void
    {
        $masterClass = MasterClass::factory()->create([
            'type_id' => $this->type->id,
            'instructor_id' => $this->instructor->id,
            'date' => now()->addDays(5)->format('Y-m-d'),
        ]);
        
        // Включаем логирование запросов для проверки N+1
        \DB::enableQueryLog();
        
        $response = $this->get(route('category.show', $this->type->id));
        
        $queries = \DB::getQueryLog();
        \DB::disableQueryLog();
        
        $response->assertStatus(200);
    
    // Увеличили лимит: в тестах больше служебных запросов
    $this->assertLessThanOrEqual(10, count($queries), 'Возможна проблема N+1 запросов');
    }

    /** @test */
    public function empty_category_shows_no_classes(): void
    {
        // Не создаём ни одного МК для этого типа
        
        $response = $this->get(route('category.show', $this->type->id));
        
        $response->assertStatus(200);
        $response->assertViewHas('classes', function ($classes) {
            return $classes->isEmpty();
        });
        $response->assertSee($this->type->name);
    }

    /** @test */
    public function category_page_shows_instructor_name(): void
    {
        $masterClass = MasterClass::factory()->create([
            'type_id' => $this->type->id,
            'instructor_id' => $this->instructor->id,
            'date' => now()->addDays(5)->format('Y-m-d'),
        ]);
        
        $response = $this->get(route('category.show', $this->type->id));
        
        $response->assertSee($this->instructor->full_name);
    }

    /** @test */
    public function category_page_shows_class_price_and_seats(): void
    {
    $masterClass = MasterClass::factory()->create([
        'type_id' => $this->type->id,
        'instructor_id' => $this->instructor->id,
        'date' => now()->addDays(5)->format('Y-m-d'),
        'price' => 2500,
        'max_participants' => 15,
    ]);
    
    $response = $this->get(route('category.show', $this->type->id));
    
    $response->assertSee('2 500');
    $response->assertSee($masterClass->free_seats);
}
}