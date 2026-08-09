<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        return response()->json($this->authService->register($request->validated()), 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        return response()->json($this->authService->login($request->validated()));
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json($this->authService->me($request));
    }

    public function logout(Request $request): JsonResponse
    {
        return response()->json($this->authService->logout($request));
    }
}