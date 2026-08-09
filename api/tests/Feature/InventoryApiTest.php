<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_list_inventory_items(): void
    {
        $this->getJson('/api/inventories')->assertUnauthorized();
    }

    public function test_guest_cannot_create_inventory_item(): void
    {
        $this->postJson('/api/inventories', [
            'name' => 'Produto sem acesso',
        ])->assertUnauthorized();
    }

    public function test_authenticated_user_can_list_inventory_items(): void
    {
        $user = User::factory()->create();
        Inventory::factory()->count(2)->create();

        $token = $user->createToken('auth_token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/inventories')
            ->assertOk()
            ->assertJsonCount(2, 'inventories')
            ->assertJsonStructure([
                'inventories' => [
                    '*' => ['id', 'name', 'sku', 'unit', 'quantity', 'minimum_quantity', 'is_active'],
                ],
            ]);
    }

    public function test_authenticated_user_can_show_inventory_item(): void
    {
        $user = User::factory()->create();
        $inventory = Inventory::factory()->create([
            'name' => 'Mouse sem fio',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/inventories/'.$inventory->id)
            ->assertOk()
            ->assertJsonPath('inventory.name', 'Mouse sem fio');
    }

    public function test_authenticated_user_can_create_inventory_item(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/inventories', [
                'name' => 'Notebook Pro 14',
                'sku' => 'NOTEBOOK-PRO-14',
                'unit' => 'un',
                'quantity' => 12,
                'minimum_quantity' => 3,
                'cost_price' => 4200.50,
                'sale_price' => 5799.90,
                'location' => 'A1-04',
                'notes' => 'Lote principal do showroom.',
            ])
            ->assertCreated()
            ->assertJsonPath('inventory.name', 'Notebook Pro 14')
            ->assertJsonPath('inventory.created_by', $user->id);

        $this->assertDatabaseHas('inventories', [
            'name' => 'Notebook Pro 14',
            'sku' => 'NOTEBOOK-PRO-14',
            'created_by' => $user->id,
        ]);
    }

    public function test_inventory_creation_validates_required_and_unique_fields(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;
        Inventory::factory()->create([
            'sku' => 'SKU-UNICO-001',
        ]);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/inventories', [
                'sku' => 'SKU-UNICO-001',
                'quantity' => -1,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'sku', 'unit', 'quantity']);
    }

    public function test_authenticated_user_can_update_inventory_item(): void
    {
        $user = User::factory()->create();
        $inventory = Inventory::factory()->create([
            'name' => 'Teclado Basico',
            'sku' => 'TEC-001',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/inventories/'.$inventory->id, [
                'name' => 'Teclado Mecanico',
                'sku' => 'TEC-001',
                'unit' => 'un',
                'quantity' => 25,
                'minimum_quantity' => 5,
                'cost_price' => 180.00,
                'sale_price' => 289.90,
                'location' => 'B2-01',
                'notes' => 'Produto de alto giro.',
                'is_active' => false,
            ])
            ->assertOk()
            ->assertJsonPath('inventory.name', 'Teclado Mecanico')
            ->assertJsonPath('inventory.is_active', false);

        $this->assertDatabaseHas('inventories', [
            'id' => $inventory->id,
            'name' => 'Teclado Mecanico',
            'location' => 'B2-01',
            'is_active' => false,
        ]);
    }
}
