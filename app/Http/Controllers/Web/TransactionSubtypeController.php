<?php

namespace App\Http\Controllers\Web;

use App\Models\TransactionSubtype;
use App\Interfaces\TransactionSubtypeServiceInterface;
use App\Http\Requests\TransactionSubtypeAddRequest;
use App\Http\Requests\TransactionSubtypeUpdateRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubtypeFilterRequest;
use App\Models\TransactionType;

class TransactionSubtypeController extends Controller
{

    public function __construct(private TransactionSubtypeServiceInterface $service)
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(SubtypeFilterRequest $request)
    {
        $subtypes = $this->service->list($request->validated());
        $types = TransactionType::select('id', 'name')->get();

        return view('subtype.index', compact('subtypes', 'types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TransactionSubtypeAddRequest $request)
    {
        try {
            $this->service->create($request->validated());
            return response()->json([
                'status' => 'success',
                'message' => __('subtype.created'),
            ], 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'status' => 'error',
                'message' => __('subtype.error_creating'),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TransactionSubtypeUpdateRequest $request, TransactionSubtype $subtype)
    {
        try {
            $this->service->update($subtype, $request->validated());
            return response()->json([
                'status' => 'success',
                'message' => __('subtype.updated'),
            ], 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'status' => 'error',
                'message' => __('subtype.error_updating'),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransactionSubtype $subtype)
    {
        try {
            $this->service->delete($subtype);
            return response()->json([
                'status' => 'success',
                'message' => __('subtype.deleted'),
            ], 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'status' => 'error',
                'message' => __('subtype.error_deleting'),
            ], 500);
        }
    }

    public function byType($typeId)
    {
        $subtypes = $this->service->listByType($typeId);
        return response()->json($subtypes);
    }
}
