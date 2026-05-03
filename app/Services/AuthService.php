<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function register(array $data): User
    {
        $user = User::create([
            'name' => $data['name'] ?? null,
            'email' => $data['email'] ?? null,
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user);

        return $user;
    }

    public function login(array $credentials): bool
    {
        return Auth::attempt($credentials, $credentials['remember'] ?? false);
    }

    public function logout(): void
    {
        Auth::logout();
    }
}
