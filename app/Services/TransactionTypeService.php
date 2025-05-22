<?php

namespace App\Services;

use App\Models\TransactionType;
use App\Models\TransactionSubtype;
use Illuminate\Support\Collection;
use App\Interfaces\TransactionTypeServiceInterface;

class TransactionTypeService implements TransactionTypeServiceInterface
{
    public function list()
    {
        return TransactionType::all();
    }

    public function create(array $data): TransactionType
    {
        return TransactionType::create($data);
    }

    public function update(TransactionType $type, array $data): bool
    {
        return $type->update($data);
    }

    public function delete(TransactionType $type): ?bool
    {
        return $type->delete();
    }

    public function getAllTypes(): Collection
    {
        return TransactionType::select('id', 'name')->get();
    }

    public function getSubtypesByType(int $typeId): Collection
    {
        return TransactionSubtype::where('transaction_type_id', $typeId)
            ->select('id', 'name')
            ->get();
    }
}
