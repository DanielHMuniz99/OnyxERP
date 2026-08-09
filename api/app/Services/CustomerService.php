<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\User;

class CustomerService
{
    /**
     * @return array{customers: \Illuminate\Database\Eloquent\Collection<int, Customer>}
     */
    public function index(): array
    {
        return [
            'customers' => Customer::query()
                ->latest()
                ->get(),
        ];
    }

    /**
     * @return array{customer: Customer}
     */
    public function show(Customer $customer): array
    {
        return [
            'customer' => $customer,
        ];
    }

    /**
     * @param array<string, mixed> $data
     * @return array{message: string, customer: Customer}
     */
    public function store(array $data, User $user): array
    {
        $customer = Customer::create([
            'created_by' => $user->id,
            'name' => $data['name'],
            'trade_name' => $data['trade_name'] ?? null,
            'document' => $data['document'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'city' => $data['city'] ?? null,
            'state' => $data['state'] ?? null,
            'notes' => $data['notes'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);

        return [
            'message' => 'Cliente cadastrado com sucesso.',
            'customer' => $customer,
        ];
    }

    /**
     * @param array<string, mixed> $data
     * @return array{message: string, customer: Customer}
     */
    public function update(Customer $customer, array $data): array
    {
        $customer->update([
            'name' => $data['name'],
            'trade_name' => $data['trade_name'] ?? null,
            'document' => $data['document'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'city' => $data['city'] ?? null,
            'state' => $data['state'] ?? null,
            'notes' => $data['notes'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);

        return [
            'message' => 'Cliente atualizado com sucesso.',
            'customer' => $customer->fresh(),
        ];
    }
}
