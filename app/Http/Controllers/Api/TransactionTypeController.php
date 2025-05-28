<?php

namespace App\Http\Controllers\Api;

use App\Interfaces\TransactionTypeServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionTypeResource;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\ErrorResource;

class TransactionTypeController extends Controller
{
    public function __construct(private TransactionTypeServiceInterface $service) {}

    public function index(): AnonymousResourceCollection
    {
        return TransactionTypeResource::collection($this->service->getAllTypes());
    }

    public function subtype($id): AnonymousResourceCollection|JsonResponse
    {
        try {
            $subtypes = $this->service->getSubtypesByType($id);
            
            return TransactionTypeResource::collection($subtypes);
        } catch (\Exception $e) {
            Log::error($e);
            return (new ErrorResource(['message' => __('type.invalid_type')]))
                ->response()->setStatusCode(500);
        }
    }
}
