<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use App\Interfaces\AuthServiceInterface;
use Illuminate\Http\Request;

class AuthService implements AuthServiceInterface
{
    public function attemptWeb(array $credentials, bool $remember = false): bool
    {
        return Auth::attempt($credentials, $remember);
    }

    public function attemptApi(array $credentials): User
    {
        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'error' => ['Invalid credentials'],
            ]);
        }

        $user = Auth::user();

        // Create token for third-party access
        $user->token = $user->createToken('token')->plainTextToken;

        return $user;
    }

    public function logout(Request $request): void
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
