<?php

namespace Tests\Feature\Admin\Product;

use App\Modules\Product\Domain\Models\AttributesGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Admin\TestAdminCase;

final class AttributesGroupCrudTest extends TestAdminCase
{
    use RefreshDatabase;

    public function test_can_create_attributes_group()
    {
        $response = $this->postJson('/api/attributes-group', [
            'name' => 'Camisetas',
        ]);

        $response->assertStatus(201);
    }

    public function test_can_list_attributes_group()
    {
        AttributesGroup::factory()->count(2)->create();

        $response = $this->getJson('/api/attributes-group');

        $response->assertStatus(200)->assertJsonCount(2, 'data');
    }

    public function test_can_show_attributes_group()
    {
        $attributesGroup = AttributesGroup::factory()->create();

        $response = $this->getJson("/api/attributes-group/" . $attributesGroup->id);

        $response->assertStatus(200)->assertJsonFragment(['id' => $attributesGroup->id]);
    }

    public function test_can_update_attributes_group()
    {
        $attributesGroup = AttributesGroup::factory()->create();

        $response = $this->putJson("/api/attributes-group/" . $attributesGroup->id, [
            'name' => 'Teste atualizado',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('attributes_groups', [
            'id' => $attributesGroup->id,
            'name' => 'Teste atualizado',
        ]);
    }

    public function test_can_delete_attributes_group()
    {
        $attributesGroup = AttributesGroup::factory()->create();

        $response = $this->deleteJson("/api/attributes-group/" . $attributesGroup->id);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('attributes_groups', [
            'id' => $attributesGroup->id,
        ]);
    }
}