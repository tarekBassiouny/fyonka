<?php

namespace App\Http\Controllers\Api;

use App\Interfaces\StoreServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\StoreResource;

class StoreController extends Controller
{
    public function __construct(private StoreServiceInterface $storeService) {}

    public function index()
    {
        return StoreResource::collection($this->storeService->getAllStores());
    }
}
