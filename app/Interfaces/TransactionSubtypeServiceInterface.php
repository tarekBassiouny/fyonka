<?php

namespace App\Interfaces;

use App\Models\TransactionSubtype;

interface TransactionSubtypeServiceInterface
{
    public function list(array $data);
    public function create(array $data): TransactionSubtype;
    public function update(TransactionSubtype $subtype, array $data): bool;
    public function delete(TransactionSubtype $subtype): ?bool;
    public function listByType($typeId);
}
