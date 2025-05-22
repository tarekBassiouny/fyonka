<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\LoginResource;
use App\Interfaces\AuthServiceInterface;

class LoginController extends Controller
{
    public function __construct(private AuthServiceInterface $service) {}

    public function login(LoginRequest $request)
    {
        $user = $this->service->attemptApi($request->validated());

        return new loginResource($user);
    }
}
