<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Interfaces\AuthServiceInterface;
use App\Http\Resources\Auth\SuccessLoginResource;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class LoginController extends Controller
{
    public function index()
    {
        return auth()->check() ? Redirect::route('dashboard.index') : Redirect::route('login.show');
    }

    public function show()
    {
        return auth()->check() ? Redirect::route('dashboard.index') : view('auth.login');
    }

    public function authenticate(LoginRequest $request, AuthServiceInterface $authService): JsonResponse|SuccessLoginResource
    {
        $user = $authService->attemptWeb($request->only('username', 'password'), $request->boolean('remember'));

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => __('auth.login_failed'),
            ], 401);
        }

        return new SuccessLoginResource(auth()->user());
    }

    public function logout(Request $request, AuthServiceInterface $authService)
    {
        $authService->logout($request);
        return Redirect::route('login.show');
    }
}
