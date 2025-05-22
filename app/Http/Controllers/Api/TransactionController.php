<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\TransactionAddRequest;
use App\Interfaces\TransactionServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class TransactionController extends Controller
{
    public function __construct(private TransactionServiceInterface $service)
    {
    }

    public function store(TransactionAddRequest $request): RedirectResponse
    {
        try {
            $this->service->create($request->validated());
            return redirect()->route('transactions.index')->with('success', __('transaction.created'));
        } catch (\Exception $e) {
            Log::error($e);
            return back()->withErrors(['error' => __('transaction.error_creating')])->withInput();
        }
    }
}
