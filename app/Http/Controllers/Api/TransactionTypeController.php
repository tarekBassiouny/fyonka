<?php

namespace App\Http\Controllers\Api;

use App\Interfaces\TransactionTypeServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionTypeResource;
use Illuminate\Support\Facades\Log;

class TransactionTypeController extends Controller
{
    public function __construct(private TransactionTypeServiceInterface $service) {}

    public function index()
    {
        return TransactionTypeResource::collection($this->service->getAllTypes());
    }

    public function subtype($id)
    {
        try {
            $subtypes = $this->service->getSubtypesByType($id);
            return TransactionTypeResource::collection($subtypes);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'status' => 'error',
                'message' => __('type.invalid_type'),
            ], 500);
        }
    }
}
