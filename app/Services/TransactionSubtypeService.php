<?php

namespace App\Services;

use App\Models\TransactionSubtype;
use App\Interfaces\TransactionSubtypeServiceInterface;

class TransactionSubtypeService implements TransactionSubtypeServiceInterface
{
    public function list(array $data)
    {
        $query = TransactionSubtype::with('type');

        if (!empty($data['name'])) {
            $query->where('name', 'like', '%' . $data['name'] . '%');
        }

        if (!empty($data['type_id'])) {
            $query->where('transaction_type_id', $data['type_id']);
        }

        $perPage = $data['per_page'] ?? 10;

        return $query->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(array $data): TransactionSubtype
    {
        return TransactionSubtype::create($data);
    }

    public function update(TransactionSubtype $subtype, array $data): bool
    {
        return $subtype->update($data);
    }

    public function delete(TransactionSubtype $subtype): ?bool
    {
        return $subtype->delete();
    }

    public function listByType($typeId)
    {
        return TransactionSubtype::where('transaction_type_id', $typeId)
            ->get(['id', 'name', 'transaction_type_id']);
    }
}
