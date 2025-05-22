<?php

namespace App\Services;

use App\Models\Transaction;
use App\Interfaces\TransactionServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TransactionService implements TransactionServiceInterface
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        return Transaction::with(['store', 'transactionType', 'subtype', 'uploadedFile'])
            ->when(isset($filters['date_from']), fn($q) => $q->whereDate('date', '>=', $filters['date_from']))
            ->when(isset($filters['date_to']), fn($q) => $q->whereDate('date', '<=', $filters['date_to']))
            ->when(isset($filters['store_id']), fn($q) => $q->where('store_id', $filters['store_id']))
            ->when(isset($filters['type_id']), fn($q) => $q->where('type_id', $filters['type_id']))
            ->when(isset($filters['subtype_id']), fn($q) => $q->where('subtype_id', $filters['subtype_id']))
            ->orderBy('is_temp', 'desc')
            ->paginate($filters['per_page'] ?? 10)
            ->withQueryString();
    }

    public function create(array $data, $isAPI = false): Transaction
    {
        if ($isAPI) {
            $data['source'] = 'api';
        }
        $data['creator_id'] = auth()->user()?->id;
        return Transaction::create($data);
    }

    public function update(Transaction $transaction, array $data): bool
    {
        $data['creator_id'] = auth()->user()?->id;
        return $transaction->update($data);
    }

    public function delete(Transaction $transaction): ?bool
    {
        return $transaction->delete();
    }

    public function approve(Transaction $transaction, array $data): bool
    {
        $data['is_temp'] = false;
        $data['creator_id'] = auth()->user()?->id;
        return $transaction->update($data);
    }

    public function reject(Transaction $transaction): ?bool
    {
        return $transaction->delete(); // soft delete
    }

    public function bulkApprove(array $data): int
    {
        $ids = collect($data)->pluck('id')->all();
        $transactions = Transaction::whereIn('id', $ids)->get()->keyBy('id');
        $updatedId = [];

        foreach ($data as $txData) {
            if (!isset($transactions[$txData['id']])) {
                continue; // or throw error
            }

            $transaction = $transactions[$txData['id']];
            $transaction->fill($txData);
            $transaction->creator_id = auth()->user()?->id;
            $transaction->is_temp = false;
            $transaction->save();
            array_push($updatedId, $transaction->id);
        }

        return count($updatedId);
    }

    public function bulkReject(array $ids): void
    {
        Transaction::whereIn('id', $ids)->delete(); // soft delete
    }
}
