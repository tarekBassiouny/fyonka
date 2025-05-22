<?php

namespace App\Interfaces;

use App\Models\User;
use Illuminate\Http\Request;

interface AuthServiceInterface
{
    public function attemptWeb(array $credentials, bool $remember = false): bool;
    public function attemptApi(array $credentials): User;
    public function logout(Request $request): void;
}
