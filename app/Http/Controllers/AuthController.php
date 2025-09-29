<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Services\AuthService;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function signin(AuthRequest $request)
    {
        try {
            $credentials = $request->only('email', 'password');
            $result = $this->authService->login($credentials);

            if (!$result) {
                return response()->json(['error' => 'Email atau password salah'], 401);
            }

            return response()->json([
                'user' => new UserResource($result['user']),
                'access_token' => $result['token'],
                'token_type' => 'Bearer',
                'expires_in' => Auth::factory()->getTTL() * 60,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function signup(AuthRequest $request)
    {
        try {
            $data = $request->only('name', 'email', 'password');
            $result = $this->authService->register($data);

            return response()->json([
                'user' => new UserResource($result['user']),
                'access_token' => $result['token'],
                'token_type' => 'Bearer',
                'expires_in' => Auth::factory()->getTTL() * 60,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function me()
    {
        return response()->json(new UserResource($this->authService->me()));
    }

    public function signout()
    {
        $this->authService->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        try {
            $result = $this->authService->refresh();

            return response()->json([
                'user' => new UserResource($result['user']),
                'access_token' => $result['token'],
                'token_type' => 'Bearer',
                'expires_in' => Auth::factory()->getTTL() * 60,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
