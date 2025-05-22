<?php

namespace App\Http\Controllers\Web;

use App\Interfaces\UploadedFileServiceInterface;
use App\Imports\TransactionImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\UploadFileRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\DashboardFilterRequest;
use App\Interfaces\DashboardServiceInterface;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\CardResource;
use App\Http\Resources\ChartResource;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function __construct(private DashboardServiceInterface $dashboardService)
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('dashboard');
    }

    public function cards(DashboardFilterRequest $request)
    {
        $summary = $this->dashboardService->getCardSummary($request->validatedFilters());

        return new CardResource($summary);
    }

    public function charts(DashboardFilterRequest $request)
    {
        $data = $this->dashboardService->getChartData($request->validatedFilters());

        return new ChartResource($data);
    }

    public function uploadTransactions(UploadFileRequest $request, UploadedFileServiceInterface $fileService)
    {
        try {
            $uploadedFile = $fileService->storeUploadedFile($request->file('file'));
            Excel::import(
                new TransactionImport($uploadedFile->id, $request->file('file')->getClientOriginalName()),
                $request->file('file')
            );

            return response()->json([
                'status' => 'success',
                'message' => __('transaction.imported'),
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Transaction import failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => __('transaction.import_failed'),
            ], 500);
        }
    }

    public function transactions(DashboardFilterRequest $request)
    {
        $page = $request->input('per_page', 10);
        $transactions = $this->dashboardService->paginateTransactions($request->validatedFilters(), $page);
        
        return TransactionResource::collection($transactions);
    }
}
