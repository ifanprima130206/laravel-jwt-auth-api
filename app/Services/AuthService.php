<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function login(array $credentials)
    {
        if (!$token = Auth::attempt($credentials)) {
            return null;
        }

        return [
            'token' => $token,
            'user' => Auth::user()
        ];
    }

    public function register(array $data)
    {
        $plainPassword = $data['password'];
        $data['password'] = bcrypt($plainPassword);

        $user = User::create($data);

        $token = Auth::login($user);

        return [
            'token' => $token,
            'user' => $user,
            'password' => $plainPassword
        ];
    }

    public function logout()
    {
        Auth::logout();
    }

    public function refresh()
    {
        return [
            'token' => Auth::refresh(),
            'user' => Auth::user()
        ];
    }

    public function me()
    {
        return Auth::user();
    }
}
