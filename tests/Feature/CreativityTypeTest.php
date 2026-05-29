<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\CreativityType;
use App\Models\MasterClass;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreativityTypeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function creativity_type_has_master_classes_relationship(): void
    {
        $type = CreativityType::factory()->create([
            'name' => 'Рисование',
            'description' => 'Тестовое описание',
        ]);

        $masterClass = MasterClass::factory()->create([
            'type_id' => $type->id,
        ]);

        $this->assertTrue($type->masterClasses->contains($masterClass));
        $this->assertEquals(1, $type->masterClasses->count());
    }

    /** @test */
    public function creativity_type_can_be_created(): void
    {
        $type = CreativityType::create([
            'name' => 'Лепка',
            'description' => 'Описание лепки',
        ]);

        $this->assertDatabaseHas('creativity_types', [
            'id' => $type->id,
            'name' => 'Лепка',
        ]);
    }

    /** @test */
    public function creativity_type_can_be_updated(): void
    {
        $type = CreativityType::factory()->create();
        
        $type->update([
            'name' => 'Новое название',
            'description' => 'Новое описание',
        ]);

        $this->assertDatabaseHas('creativity_types', [
            'id' => $type->id,
            'name' => 'Новое название',
        ]);
    }

    /** @test */
    public function creativity_type_can_be_deleted(): void
    {
        $type = CreativityType::factory()->create();
        $type->delete();

        $this->assertDatabaseMissing('creativity_types', [
            'id' => $type->id,
        ]);
    }
}