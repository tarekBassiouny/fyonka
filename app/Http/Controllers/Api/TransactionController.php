<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\TransactionAPIAddRequest;
use App\Interfaces\TransactionServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionAPIResource;

class TransactionController extends Controller
{
    public function __construct(private TransactionServiceInterface $service)
    {
    }

    public function store(TransactionAPIAddRequest $request): JsonResponse
    {
        try {
            $transaction = $this->service->create($request->validated(), true);
            return response()->json([
                'status' => 'success',
                'message' => __('transaction.created'),
                'data' => new TransactionAPIResource($transaction),
            ], 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'status' => 'error',
                'message' => __('transaction.error_creating'),
            ], 500);
        }
    }
}
