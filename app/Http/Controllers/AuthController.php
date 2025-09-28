<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

    public function signin(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $result = $this->authService->login($credentials);

        if (!$result) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'access_token' => $result['token'],
            'token_type' => 'Bearer',
            'expires_in' => Auth::factory()->getTTL() * 60,
            'user' => new UserResource($result['user'])
        ]);
    }

    public function signup(Request $request)
    {
        $data = $request->only('name', 'email', 'password');
        $result = $this->authService->register($data);

        return response()->json([
            'access_token' => $result['token'],
            'token_type' => 'Bearer',
            'expires_in' => Auth::factory()->getTTL() * 60,
            'user' => new UserResource($result['user']),
            'password' => $result['password'] // testing only
        ]);
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
        $result = $this->authService->refresh();

        return response()->json([
            'access_token' => $result['token'],
            'token_type' => 'Bearer',
            'expires_in' => Auth::factory()->getTTL() * 60,
            'user' => new UserResource($result['user'])
        ]);
    }
}
