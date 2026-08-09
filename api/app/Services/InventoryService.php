<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\User;

class InventoryService
{
    /**
     * @return array{inventories: \Illuminate\Database\Eloquent\Collection<int, Inventory>}
     */
    public function index(): array
    {
        return [
            'inventories' => Inventory::query()
                ->latest()
                ->get(),
        ];
    }

    /**
     * @return array{inventory: Inventory}
     */
    public function show(Inventory $inventory): array
    {
        return [
            'inventory' => $inventory,
        ];
    }

    /**
     * @param array<string, mixed> $data
     * @return array{message: string, inventory: Inventory}
     */
    public function store(array $data, User $user): array
    {
        $inventory = Inventory::create([
            'created_by' => $user->id,
            'name' => $data['name'],
            'sku' => $data['sku'],
            'unit' => $data['unit'],
            'quantity' => $data['quantity'],
            'minimum_quantity' => $data['minimum_quantity'] ?? 0,
            'cost_price' => $data['cost_price'] ?? null,
            'sale_price' => $data['sale_price'] ?? null,
            'location' => $data['location'] ?? null,
            'notes' => $data['notes'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);

        return [
            'message' => 'Item de estoque cadastrado com sucesso.',
            'inventory' => $inventory,
        ];
    }

    /**
     * @param array<string, mixed> $data
     * @return array{message: string, inventory: Inventory}
     */
    public function update(Inventory $inventory, array $data): array
    {
        $inventory->update([
            'name' => $data['name'],
            'sku' => $data['sku'],
            'unit' => $data['unit'],
            'quantity' => $data['quantity'],
            'minimum_quantity' => $data['minimum_quantity'] ?? 0,
            'cost_price' => $data['cost_price'] ?? null,
            'sale_price' => $data['sale_price'] ?? null,
            'location' => $data['location'] ?? null,
            'notes' => $data['notes'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);

        return [
            'message' => 'Item de estoque atualizado com sucesso.',
            'inventory' => $inventory->fresh(),
        ];
    }
}
