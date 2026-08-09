<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Maria Silva',
            'email' => 'maria@example.com',
            'document' => '12345678900',
            'phone' => '11999999999',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'manager',
        ]);

        $response
            ->assertCreated()
            ->assertJsonStructure([
                'message',
                'token',
                'user' => ['id', 'name', 'email', 'document', 'phone', 'role', 'is_active'],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'maria@example.com',
            'document' => '12345678900',
            'phone' => '11999999999',
            'role' => 'manager',
            'is_active' => true,
        ]);
    }

    public function test_register_applies_default_role_and_active_status(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Ana Souza',
            'email' => 'ana@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('user.role', 'operator')
            ->assertJsonPath('user.is_active', true);

        $this->assertDatabaseHas('users', [
            'email' => 'ana@example.com',
            'role' => 'operator',
            'is_active' => true,
        ]);
    }

    public function test_register_requires_required_fields(): void
    {
        $response = $this->postJson('/api/auth/register', []);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_register_requires_password_confirmation_to_match(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Carlos Mendes',
            'email' => 'carlos@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different-password',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);
    }

    public function test_register_rejects_duplicate_email_and_document(): void
    {
        User::factory()->create([
            'email' => 'maria@example.com',
            'document' => '12345678900',
        ]);

        $response = $this->postJson('/api/auth/register', [
            'name' => 'Maria Silva',
            'email' => 'maria@example.com',
            'document' => '12345678900',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'document']);
    }

    public function test_register_rejects_invalid_role(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Paula Rocha',
            'email' => 'paula@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'supervisor',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['role']);
    }

    public function test_user_can_login_and_access_profile(): void
    {
        $user = User::factory()->create([
            'email' => 'joao@example.com',
            'password' => 'password123',
        ]);

        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => 'joao@example.com',
            'password' => 'password123',
        ]);

        $token = $loginResponse->json('token');

        $loginResponse
            ->assertOk()
            ->assertJsonPath('user.id', $user->id);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/auth/me')
            ->assertOk()
            ->assertJsonPath('user.email', 'joao@example.com');
    }

    public function test_login_requires_email_and_password(): void
    {
        $response = $this->postJson('/api/auth/login', []);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_login_rejects_invalid_email_format(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'invalid-email',
            'password' => 'password123',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_login_rejects_unknown_email(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'ghost@example.com',
            'password' => 'password123',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email'])
            ->assertJsonPath('errors.email.0', 'As credenciais informadas sao invalidas.');
    }

    public function test_login_rejects_wrong_password(): void
    {
        User::factory()->create([
            'email' => 'joao@example.com',
            'password' => 'password123',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'joao@example.com',
            'password' => 'wrong-password',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email'])
            ->assertJsonPath('errors.email.0', 'As credenciais informadas sao invalidas.');
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/auth/logout')
            ->assertOk()
            ->assertJsonPath('message', 'Logout realizado com sucesso.');

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
