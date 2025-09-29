<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Services\AuthService;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function signin(AuthRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $result = $this->authService->login($credentials);

        if (!$result) {
            return response()->json(['error' => 'Email atau password salah'], 401);
        }

        $expiresIn = Auth::factory()->getTTL() * 60;

        return response()->json([
            'user' => new UserResource($result['user']),
            'access_token' => $result['token'],
            'token_type' => 'Bearer',
            'expires_in' => $expiresIn,
        ])->cookie(
            'access_token',
            $result['token'],
            $expiresIn / 60,
            '/',
            null,
            true,
            true,
            false,
            'Lax'
        );
    }

    public function signup(AuthRequest $request)
    {
        $data = $request->only('name', 'email', 'password');
        $result = $this->authService->register($data);
        $expiresIn = Auth::factory()->getTTL() * 60;

        return response()->json([
            'user' => new UserResource($result['user']),
            'access_token' => $result['token'],
            'token_type' => 'Bearer',
            'expires_in' => $expiresIn,
        ])->cookie(
            'access_token',
            $result['token'],
            $expiresIn / 60,
            '/',
            null,
            true,
            true,
            false,
            'Lax'
        );
    }

    public function refresh()
    {
        $result = $this->authService->refresh();
        $expiresIn = Auth::factory()->getTTL() * 60;

        return response()->json([
            'user' => new UserResource($result['user']),
            'access_token' => $result['token'],
            'token_type' => 'Bearer',
            'expires_in' => $expiresIn,
        ])->cookie(
            'access_token',
            $result['token'],
            $expiresIn / 60,
            '/',
            null,
            true,
            true,
            false,
            'Lax'
        );
    }

    public function signout()
    {
        try {
            $token = auth()->getToken(); // otomatis ambil dari header / cookie
            if ($token) {
                auth()->invalidate($token); // blacklist token
            }
        } catch (\Exception $e) {
            // ignore
        }

        return response()->json(['message' => 'Successfully logged out'])
            ->cookie('access_token', '', -1, '/', null, true, true);
    }

    public function me()
    {
        return response()->json(new UserResource($this->authService->me()));
    }
}
