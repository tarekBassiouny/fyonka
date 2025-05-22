<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\TransactionAddRequest;
use App\Http\Requests\TransactioApproveRequest;
use App\Http\Requests\TransactionUpdateRequest;
use App\Http\Requests\BulkApproveRequest;
use App\Models\Transaction;
use App\Interfaces\TransactionServiceInterface;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\BulkRejectRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;

class TransactionController extends Controller
{
    public function __construct(private TransactionServiceInterface $service)
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $transactions = $this->service->list();
        return view('transactions.index', compact('transactions'));
    }

    public function store(TransactionAddRequest $request): JsonResponse
    {
        try {
            $this->service->create($request->validated());
            return response()->json([
                'status' => 'success',
                'message' => __('transaction.created'),
            ], 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'status' => 'error',
                'message' => __('transaction.error_creating'),
            ], 500);
        }
    }

    public function update(TransactionUpdateRequest $request, Transaction $transaction): JsonResponse
    {
        try {
            $this->service->update($transaction, $request->validated());
            return response()->json([
                'status' => 'success',
                'message' => __('transaction.updated'),
            ], 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'status' => 'error',
                'message' => __('transaction.error_updating'),
            ], 500);
        }
    }

    public function destroy(Transaction $transaction): JsonResponse
    {
        try {
            $this->service->delete($transaction);
            return response()->json([
                'status' => 'success',
                'message' => __('transaction.deleted'),
            ], 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'status' => 'error',
                'message' => __('transaction.error_deleting'),
            ], 500);
        }
    }

    public function approve(Transaction $transaction, TransactioApproveRequest $request): JsonResponse
    {
        try {
            $this->service->approve($transaction, $request->validated());
            return response()->json([
                'status' => 'success',
                'message' => __('transaction.approved'),
            ], 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'status' => 'error',
                'message' => __('transaction.error_approving'),
            ], 500);
        }
    }

    public function reject(Transaction $transaction): JsonResponse
    {
        try {
            $this->service->reject($transaction);
            return response()->json([
                'status' => 'success',
                'message' => __('transaction.rejected'),
            ], 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'status' => 'error',
                'message' => __('transaction.error_rejecting'),
            ], 500);
        }
    }

    public function bulkApprove(BulkApproveRequest $request): JsonResponse
    {
        try {
            $result = $this->service->bulkApprove($request->validated()['transactions']);
            return response()->json([
                'status' => 'success',
                'message' => __('transaction.bulk_approved'),
                'data' => $result
            ], 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'status' => 'error',
                'message' => __('transaction.error_bulk_approving'),
            ], 500);
        }
    }

    public function bulkReject(BulkRejectRequest $request): JsonResponse
    {
        try {
            $this->service->bulkReject($request->validated()['ids']);
            return response()->json([
                'status' => 'success',
                'message' => __('transaction.bulk_rejected'),
            ], 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'status' => 'error',
                'message' => __('transaction.error_bulk_rejected'),
            ], 500);
        }
    }
}
