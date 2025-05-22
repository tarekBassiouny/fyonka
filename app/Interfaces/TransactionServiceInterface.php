<?php

namespace App\Interfaces;

use App\Models\Transaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TransactionServiceInterface
{
    public function list(): LengthAwarePaginator;
    public function create(array $data, bool $isAPI = false): Transaction;
    public function update(Transaction $transaction, array $data): bool;
    public function delete(Transaction $transaction): ?bool;

    public function approve(Transaction $transaction, array $data): bool;
    public function reject(Transaction $transaction): ?bool;

    public function bulkApprove(array $ids): int;
    public function bulkReject(array $ids): void;
}
