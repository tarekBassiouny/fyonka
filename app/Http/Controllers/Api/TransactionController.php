<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\TransactionAPIAddRequest;
use App\Interfaces\TransactionServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionAPIResource;
use App\Http\Resources\SuccessResource;
use App\Http\Resources\ErrorResource;

class TransactionController extends Controller
{
    public function __construct(private TransactionServiceInterface $service) {}

    public function store(TransactionAPIAddRequest $request): SuccessResource|JsonResponse
    {
        try {
            $transaction = $this->service->create($request->validated(), true);

            return new SuccessResource([
                'message' => __('transaction.created'),
                'data' => new TransactionAPIResource($transaction),
            ]);
        } catch (\Exception $e) {
            Log::error($e);
            return (new ErrorResource(['message' => __('transaction.error_creating')]))
                ->response()->setStatusCode(500);
        }
    }
}
