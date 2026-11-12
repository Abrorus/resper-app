<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->manager()->create());
    }

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

        $this->get(route('inventory.index'))->assertOk()->assertSee('Beras')->assertSee('502623313');
    }

    public function test_show_returns_current_data_as_json(): void
    {
        $item = Inventory::create($this->payload());

        $this->getJson(route('inventory.show', $item))->assertOk()->assertJson(['code' => '502623313', 'date' => '2026-01-03']);
    }

    public function test_can_create_inventory(): void
    {
        $this->post(route('inventory.store'), $this->payload())->assertRedirect(route('inventory.index'));
        $this->assertDatabaseHas('inventories', ['code' => '502623313', 'total' => 512]);
    }

    public function test_create_validates_input(): void
    {
        $this->post(route('inventory.store'), $this->payload(['name' => '', 'total' => 'abc']))
            ->assertSessionHasErrors(['name', 'total'], errorBag: 'create');
        $this->assertDatabaseCount('inventories', 0);
    }

    public function test_can_update_inventory(): void
    {
        $item = Inventory::create($this->payload());

        $this->put(route('inventory.update', $item), $this->payload(['total' => 700]))->assertRedirect();
        $this->assertDatabaseHas('inventories', ['id' => $item->id, 'total' => 700]);
    }

    public function test_can_delete_inventory(): void
    {
        $item = Inventory::create($this->payload());

        $this->delete(route('inventory.destroy', $item))->assertRedirect();
        $this->assertDatabaseMissing('inventories', ['id' => $item->id]);
    }
}
