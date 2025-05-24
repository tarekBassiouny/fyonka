<?php

namespace App\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Barryvdh\DomPDF\PDF as pdfView;
use App\Models\Store;

interface DashboardServiceInterface
{
    public function getCardSummary(array $filters): array;
    public function getChartData(array $filters): array;
    public function paginateTransactions(array $filters, int $perPage = 20): LengthAwarePaginator;
    public function transactionList(array $filters): Collection;
    public function renderPDF(array $filters, ?Store $store): pdfView;
}
