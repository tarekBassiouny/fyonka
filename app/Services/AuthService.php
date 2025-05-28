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
        if (!Auth::attempt($credentials, $remember)) {
            return false;
        }

        if (Auth::user()?->role === 'api') {
            Auth::logout();
            throw ValidationException::withMessages([
                'error' => __('auth.dashboard_login_not_allowed'),
            ]);
        }

        return true;
    }

    public function attemptApi(array $credentials): User
    {
        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'error' => __('auth.invalid_credentials'),
            ]);
        }

        $user = Auth::user();

        if ($user->role === 'dashboard') {
            Auth::logout();
            throw ValidationException::withMessages([
                'error' => [__('auth.api_login_not_allowed')],
            ]);
        }

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
