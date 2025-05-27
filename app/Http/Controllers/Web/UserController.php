<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UserFilterRequest;
use App\Interfaces\UserServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class UserController extends Controller
{
    public function __construct(private UserServiceInterface $userService)
    {
        $this->middleware('auth');
    }

    public function index(UserFilterRequest $request): View
    {
        $users = $this->userService->getAll($request->validated());
        return view('users.index', compact('users'));
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        try {
            $this->userService->create($request->validated());
            return response()->json([
                'status' => 'success',
                'message' => __('user.created'),
            ]);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'status' => 'error',
                'message' => __('user.error_creating'),
            ], 500);
        }
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        try {
            $this->userService->update($user->id, $request->validated());
            return response()->json([
                'status' => 'success',
                'message' => __('user.updated'),
                'redirect' => route('users.index'),
            ]);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'status' => 'error',
                'message' => __('user.error_updating'),
            ], 500);
        }
    }

    public function destroy(User $user): JsonResponse
    {
        try {
            $this->userService->delete($user->id);
            return response()->json([
                'status' => 'success',
                'message' => __('user.deleted'),
                'redirect' => route('users.index'),
            ]);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'status' => 'error',
                'message' => __('user.error_deleting'),
            ], 500);
        }
    }
}
