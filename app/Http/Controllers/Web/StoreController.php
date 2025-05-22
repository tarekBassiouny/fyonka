<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\StoreAddRequest;
use App\Http\Requests\StoreUpdateRequest;
use App\Models\Store;
use App\Interfaces\StoreServiceInterface;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreFilterRequest;
use Illuminate\Contracts\View\View;

class StoreController extends Controller
{
    public function __construct(private StoreServiceInterface $storeService)
    {
        $this->middleware('auth');
    }

    public function index(StoreFilterRequest $request): View
    {
        $stores = $this->storeService->list($request->validated());
        return view('stores.index', compact('stores'));
    }

    public function store(StoreAddRequest $request): JsonResponse
    {
        try {
            $this->storeService->create($request->validated(), $request->file('image'));
            return response()->json([
                'status' => 'success',
                'message' => __('store.created'),
            ], 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'status' => 'error',
                'message' => __('store.error_creating'),
            ], 500);
        }
    }

    public function update(StoreUpdateRequest $request, Store $store): JsonResponse
    {
        try {
            $this->storeService->update($store, $request->validated(), $request->file('image'));
            return response()->json([
                'status' => 'success',
                'message' => __('store.updated'),
                'redirect' => route('stores.index')
            ], 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'status' => 'error',
                'message' => __('store.error_updating'),
            ], 500);
        }
    }

    public function destroy(Store $store): JsonResponse
    {
        try {
            $this->storeService->delete($store);
            return response()->json([
                'status' => 'success',
                'message' => __('store.deleted'),
                'redirect' => route('stores.index')
            ], 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'status' => 'error',
                'message' => __('store.error_deleting'),
            ], 500);
        }
    }
}
