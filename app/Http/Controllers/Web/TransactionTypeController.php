<?php

namespace App\Http\Controllers\Web;

use App\Models\TransactionType;
use App\Interfaces\TransactionTypeServiceInterface;
use App\Http\Requests\TransactionTypeAddRequest;
use App\Http\Requests\TransactionTypeUpdateRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class TransactionTypeController extends Controller
{
    public function __construct(private TransactionTypeServiceInterface $service)
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $type = $this->service->list();
        return view('type.index', compact('type'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('type.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TransactionTypeAddRequest $request)
    {
        try {
            $this->service->create($request->validated());
            return back()->with('success', __('type.created'));
        } catch (\Exception $e) {
            Log::error($e);
            return back()->withErrors(['error' => __('type.error_creating')])->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TransactionType $transactionType)
    {
        return view('type.edit', compact('transactionType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TransactionTypeUpdateRequest $request, TransactionType $transactionType)
    {
        try {
            $this->service->update($transactionType, $request->validated());
            return redirect()->route('type.index')->with('success', __('type.updated'));
        } catch (\Exception $e) {
            Log::error($e);
            return back()->withErrors(['error' => __('type.error_updating')])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransactionType $transactionType)
    {
        try {
            $this->service->delete($transactionType);
            return redirect()->route('type.index')->with('success', __('type.deleted'));
        } catch (\Exception $e) {
            Log::error($e);
            return back()->withErrors(['error' => __('type.error_deleting')]);
        }
    }
}
