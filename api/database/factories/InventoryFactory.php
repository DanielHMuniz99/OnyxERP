<?php

namespace Database\Factories;

use App\Models\Inventory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Inventory>
 */
class InventoryFactory extends Factory
{
    protected $model = Inventory::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'created_by' => User::factory(),
            'name' => fake()->words(3, true),
            'sku' => strtoupper(fake()->bothify('SKU-####-??')),
            'unit' => fake()->randomElement(['un', 'cx', 'kg', 'lt']),
            'quantity' => fake()->randomFloat(2, 0, 150),
            'minimum_quantity' => fake()->randomFloat(2, 0, 25),
            'cost_price' => fake()->randomFloat(2, 5, 500),
            'sale_price' => fake()->randomFloat(2, 10, 900),
            'location' => fake()->randomElement(['A1', 'B3', 'C2', 'Deposito principal']),
            'notes' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
