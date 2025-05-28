<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Interfaces\UserServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserService implements UserServiceInterface
{
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        return User::query()
            ->when($filters['name'] ?? null, fn($q, $v) => $q->where('name', 'like', "%$v%"))
            ->when($filters['email'] ?? null, fn($q, $v) => $q->where('email', 'like', "%$v%"))
            ->when($filters['username'] ?? null, fn($q, $v) => $q->where('username', 'like', "%$v%"))
            ->when($filters['role'] ?? null, fn($q, $v) => $q->where('role', $v))
            ->orderBy('id', 'desc')
            ->paginate($filters['per_page'] ?? 10)
            ->withQueryString();
    }

    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    public function create(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        return User::create($data);
    }

    public function update(int $id, array $data): User
    {
        $user = $this->findById($id);

        if (isset($data['password']) && $data['password']) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);
        return $user;
    }

    public function delete(int $id): void
    {
        $this->findById($id)?->delete();
    }
}
