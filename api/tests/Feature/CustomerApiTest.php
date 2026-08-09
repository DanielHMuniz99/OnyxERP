<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_list_customers(): void
    {
        $this->getJson('/api/customers')
            ->assertUnauthorized();
    }

    public function test_guest_cannot_create_customer(): void
    {
        $this->postJson('/api/customers', [
            'name' => 'Cliente sem acesso',
        ])->assertUnauthorized();
    }

    public function test_authenticated_user_can_list_customers(): void
    {
        $user = User::factory()->create();
        Customer::factory()->count(2)->create();

        $token = $user->createToken('auth_token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/customers')
            ->assertOk()
            ->assertJsonCount(2, 'customers')
            ->assertJsonStructure([
                'customers' => [
                    '*' => ['id', 'name', 'document', 'email', 'phone', 'city', 'state', 'is_active'],
                ],
            ]);
    }

    public function test_authenticated_user_can_show_customer(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create([
            'name' => 'Cliente Detalhado',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/customers/'.$customer->id)
            ->assertOk()
            ->assertJsonPath('customer.name', 'Cliente Detalhado');
    }

    public function test_authenticated_user_can_create_customer(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/customers', [
                'name' => 'Mercado Central',
                'trade_name' => 'Mercado Central LTDA',
                'document' => '12345678000199',
                'email' => 'contato@mercadocentral.com',
                'phone' => '11999999999',
                'city' => 'Sao Paulo',
                'state' => 'SP',
                'notes' => 'Cliente com alto volume mensal.',
            ])
            ->assertCreated()
            ->assertJsonPath('customer.name', 'Mercado Central')
            ->assertJsonPath('customer.created_by', $user->id);

        $this->assertDatabaseHas('customers', [
            'name' => 'Mercado Central',
            'document' => '12345678000199',
            'created_by' => $user->id,
        ]);
    }

    public function test_customer_creation_validates_required_and_unique_fields(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;
        Customer::factory()->create([
            'document' => '12345678000199',
        ]);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/customers', [
                'document' => '12345678000199',
                'state' => 'Sao Paulo',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'document', 'state']);
    }

    public function test_authenticated_user_can_update_customer(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create([
            'name' => 'Loja Antiga',
            'document' => '12345678000199',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/customers/'.$customer->id, [
                'name' => 'Loja Atualizada',
                'trade_name' => 'Loja Atualizada LTDA',
                'document' => '12345678000199',
                'email' => 'novo@loja.com',
                'phone' => '11888887777',
                'city' => 'Campinas',
                'state' => 'SP',
                'notes' => 'Cadastro revisado.',
                'is_active' => false,
            ])
            ->assertOk()
            ->assertJsonPath('customer.name', 'Loja Atualizada')
            ->assertJsonPath('customer.is_active', false);

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'Loja Atualizada',
            'email' => 'novo@loja.com',
            'is_active' => false,
        ]);
    }
}
