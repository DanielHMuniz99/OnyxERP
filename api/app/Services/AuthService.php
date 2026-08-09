<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * @param array<string, mixed> $data
     * @return array{message: string, token: string, user: User}
     */
    public function register(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'document' => $data['document'] ?? null,
            'phone' => $data['phone'] ?? null,
            'password' => $data['password'],
            'role' => $data['role'] ?? 'operator',
            'is_active' => $data['is_active'] ?? true,
        ]);

        return [
            'message' => 'Usuario cadastrado com sucesso.',
            'token' => $this->createToken($user),
            'user' => $user,
        ];
    }

    /**
     * @param array<string, mixed> $credentials
     * @return array{message: string, token: string, user: User}
     */
    public function login(array $credentials): array
    {
        $user = User::query()->where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais informadas sao invalidas.'],
            ]);
        }

        return [
            'message' => 'Login realizado com sucesso.',
            'token' => $this->createToken($user),
            'user' => $user,
        ];
    }

    /**
     * @return array{user: mixed}
     */
    public function me(Request $request): array
    {
        return [
            'user' => $request->user(),
        ];
    }

    /**
     * @return array{message: string}
     */
    public function logout(Request $request): array
    {
        $request->user()?->currentAccessToken()?->delete();

        return [
            'message' => 'Logout realizado com sucesso.',
        ];
    }

    private function createToken(User $user): string
    {
        return $user->createToken('auth_token')->plainTextToken;
    }
}