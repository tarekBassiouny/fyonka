<?php

namespace App\Http\Controllers\Api;

use App\Interfaces\TransactionTypeServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionTypeResource;

class TransactionTypeController extends Controller
{
    public function __construct(private TransactionTypeServiceInterface $service) {}

    public function index()
    {
        return TransactionTypeResource::collection($this->service->getAllTypes());
    }

    public function subtype($id)
    {
        return TransactionTypeResource::collection($this->service->getSubtypesByType($id));
    }
}
