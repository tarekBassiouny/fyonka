<?php

namespace App\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface DashboardServiceInterface
{
    public function getCardSummary(array $filters): array;
    public function getChartData(array $filters): array;
    public function paginateTransactions(array $filters, int $perPage = 20): LengthAwarePaginator;
}
