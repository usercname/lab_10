<?php
namespace Tests\Unit;

use Tests\TestCase;
use App\Models\CreativityType;

class CreativityTypeTest extends TestCase
{
    public function test_creativity_type_can_be_created()
    {
        $type = CreativityType::factory()->create(['name' => 'Test Type']);
        $this->assertDatabaseHas('creativity_types', ['name' => 'Test Type']);
    }

    public function test_creativity_type_update()
    {
        $type = CreativityType::factory()->create();
        $type->update(['name' => 'New Name', 'description' => 'New Desc']);
        
        $this->assertDatabaseHas('creativity_types', [
            'id' => $type->id,
            'name' => 'New Name',
        ]);
    }

    public function test_creativity_type_delete()
    {
        $type = CreativityType::factory()->create();
        $id = $type->id;
        $type->delete();

        $this->assertDatabaseMissing('creativity_types', ['id' => $id]);
    }
}