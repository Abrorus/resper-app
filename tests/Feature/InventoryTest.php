<?php

namespace Tests\Feature;

use App\Models\Inventory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $override = []): array
    {
        return array_merge([
            'name' => 'Beras',
            'code' => '502623313',
            'total' => 512,
            'date' => '2026-01-03',
            'manager_name' => 'Budi',
            'position' => 'Manager',
        ], $override);
    }

    public function test_index_lists_inventory(): void
    {
        Inventory::create($this->payload());

        $this->get('/inventaris')->assertOk()->assertSee('Beras')->assertSee('502623313');
    }

    public function test_show_returns_current_data_as_json(): void
    {
        $item = Inventory::create($this->payload());

        $this->getJson("/inventaris/{$item->id}")->assertOk()->assertJson(['code' => '502623313', 'date' => '2026-01-03']);
    }

    public function test_can_create_inventory(): void
    {
        $this->post('/inventaris', $this->payload())->assertRedirect('/inventaris');
        $this->assertDatabaseHas('inventories', ['code' => '502623313', 'total' => 512]);
    }

    public function test_create_validates_input(): void
    {
        $this->post('/inventaris', $this->payload(['name' => '', 'total' => 'abc']))
            ->assertSessionHasErrors(['name', 'total'], errorBag: 'create');
        $this->assertDatabaseCount('inventories', 0);
    }

    public function test_can_update_inventory(): void
    {
        $item = Inventory::create($this->payload());

        $this->put("/inventaris/{$item->id}", $this->payload(['total' => 700]))->assertRedirect();
        $this->assertDatabaseHas('inventories', ['id' => $item->id, 'total' => 700]);
    }

    public function test_can_delete_inventory(): void
    {
        $item = Inventory::create($this->payload());

        $this->delete("/inventaris/{$item->id}")->assertRedirect();
        $this->assertDatabaseMissing('inventories', ['id' => $item->id]);
    }
}

