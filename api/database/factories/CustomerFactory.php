<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'created_by' => User::factory(),
            'name' => fake()->company(),
            'trade_name' => fake()->companySuffix(),
            'document' => fake()->unique()->numerify('###########'),
            'email' => fake()->companyEmail(),
            'phone' => fake()->numerify('###########'),
            'city' => fake()->city(),
            'state' => fake()->stateAbbr(),
            'notes' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
