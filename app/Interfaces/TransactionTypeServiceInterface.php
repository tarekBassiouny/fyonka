<?php

namespace App\Interfaces;

use App\Models\TransactionType;
use Illuminate\Support\Collection;

interface TransactionTypeServiceInterface
{
    public function list();
    public function create(array $data): TransactionType;
    public function update(TransactionType $type, array $data): bool;
    public function delete(TransactionType $type): ?bool;

    public function getAllTypes(): Collection;
    public function getSubtypesByType(int $typeId): Collection;
}
