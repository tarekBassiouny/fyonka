<?php

namespace App\Services;

use App\Models\Transaction;
use App\Enums\TransactionTypeEnum;
use App\Interfaces\DashboardServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DashboardService implements DashboardServiceInterface
{
    private string $income;
    private string $outcome;

    public function __construct()
    {
        $this->income = TransactionTypeEnum::id('income');
        $this->outcome = TransactionTypeEnum::id('outcome');
    }

    public function getCardSummary(array $filters): array
    {
        $query = $this->applyFilters(Transaction::query()->where('is_temp', false), $filters);

        $revenue = (clone $query)->where('type_id', $this->income)->sum('amount');
        $expenses = (clone $query)->where('type_id', $this->outcome)->sum('amount');
        $gross = $revenue + $expenses;
        $margin = $revenue == 0 ? 0 : round(($gross / $revenue) * 100, 2);

        return [
            'revenue' => [
                'value' => $revenue,
                'change' => $this->getChange('income', $filters),
                'trend' => $this->getTrend('income', $filters),
            ],
            'gross_profit' => [
                'value' => $gross,
                'change' => $this->getChange('net', $filters),
                'trend' => $this->getTrend('net', $filters),
            ],
            'net_margin' => [
                'value' => $margin,
                'change' => $this->getChange('margin', $filters),
                'trend' => $this->getTrend('margin', $filters),
            ],
            'expenses' => [
                'value' => $expenses,
                'change' => $this->getChange('outcome', $filters),
                'trend' => $this->getTrend('outcome', $filters),
            ],
        ];
    }

    public function getChartData(array $filters): array
    {
        $query = $this->applyFilters(Transaction::query()->where('is_temp', false), $filters);

        $data = $query->selectRaw('YEAR(date) as year, MONTH(date) as month, store_id, type_id, SUM(amount) as total')
            ->with('store:id,name')
            ->groupByRaw('YEAR(date), MONTH(date), store_id, type_id')
            ->get();

        $storeNames = $data->pluck('store.name', 'store_id')->unique();

        $months = collect(range(1, 12))->map(fn($m) => date('M', mktime(0, 0, 0, $m, 1)));

        $incomeType = $this->income;
        $outcomeType = $this->outcome;

        $income = $outcome = $netIncome = [];

        foreach ($storeNames as $storeId => $storeName) {
            $income[$storeName] = $outcome[$storeName] = $netIncome[$storeName] = [];

            foreach (range(1, 12) as $month) {
                $incomeVal = $data->firstWhere(
                    fn($item) =>
                    $item->store_id == $storeId &&
                        $item->month == $month &&
                        $item->type_id == $incomeType
                )?->total ?? 0;

                $outcomeVal = $data->firstWhere(
                    fn($item) =>
                    $item->store_id == $storeId &&
                        $item->month == $month &&
                        $item->type_id == $outcomeType
                )?->total ?? 0;

                $income[$storeName][] = $incomeVal;
                $outcome[$storeName][] = $outcomeVal;
                $netIncome[$storeName][] = $incomeVal + $outcomeVal;
            }
        }

        return [
            'months' => $months,
            'stores' => $storeNames->values(),
            'net_income' => $netIncome,
            'income' => $income,
            'outcome' => $outcome,
        ];
    }

    public function paginateTransactions(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->applyFilters(
            Transaction::with(['store', 'transactionType', 'subtype'])
            ->orderBy('is_temp', 'desc')
            ->orderBy('id', 'desc'),
            $filters
        );
        return $query->paginate($perPage);
    }

    private function applyFilters($query, array $filters)
    {
        if (!empty($filters['date_from'])) {
            $query->where('date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('date', '<=', $filters['date_to']);
        }

        if (!empty($filters['type_id'])) {
            $query->where('type_id', $filters['type_id']);
        }

        if (!empty($filters['store_id'])) {
            $query->where('store_id', $filters['store_id']);
        }

        if (!empty($filters['subtype_id'])) {
            $query->where('subtype_id', $filters['subtype_id']);
        }

        return $query;
    }

    private function getChange(string $metric, array $filters): float
    {
        $previousFilters = $this->getPreviousPeriodFilters($filters);

        $previousQuery = $this->applyFilters(Transaction::query()->where('is_temp', false), $previousFilters);
        $currentQuery  = $this->applyFilters(Transaction::query()->where('is_temp', false), $filters);

        $previous = match ($metric) {
            'income' => (clone $previousQuery)->where('type_id', $this->income)->sum('amount'),
            'outcome' => abs((clone $previousQuery)->where('type_id', $this->outcome)->sum('amount')),
            'net' => (clone $previousQuery)->get()->reduce(
                fn($carry, $tx) => $carry + ($tx->type_id === $this->income ? $tx->amount : -$tx->amount),
                0
            ),
            'margin' => $this->getMarginValue($previousQuery),
            default => 0,
        };

        $current = match ($metric) {
            'income' => (clone $currentQuery)->where('type_id', $this->income)->sum('amount'),
            'outcome' => abs((clone $currentQuery)->where('type_id', $this->outcome)->sum('amount')),
            'net' => (clone $currentQuery)->get()->reduce(
                fn($carry, $tx) => $carry + ($tx->type_id === $this->income ? $tx->amount : -$tx->amount),
                0
            ),
            'margin' => $this->getMarginValue($currentQuery),
            default => 0,
        };

        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }

        return round((($current - $previous) / abs($previous)) * 100, 1);
    }

    private function getTrend(string $metric, array $filters): array
    {
        $query = $this->applyFilters(Transaction::query()->where('is_temp', false), $filters);

        $intervals = 6;
        $results = [];

        $startDate = isset($filters['date_from']) ? \Carbon\Carbon::parse($filters['date_from']) : now()->subMonths($intervals);
        $endDate = isset($filters['date_to']) ? \Carbon\Carbon::parse($filters['date_to']) : now();

        $totalDays = max(1, $endDate->diffInDays($startDate)); // avoid div by 0
        $daysPerInterval = (int) ceil($totalDays / $intervals);

        for ($i = 0; $i < $intervals; $i++) {
            $from = $startDate->copy()->addDays($i * $daysPerInterval)->startOfDay();
            $to   = $startDate->copy()->addDays(($i + 1) * $daysPerInterval - 1)->endOfDay();

            $segmentQuery = (clone $query)->whereBetween('date', [$from, $to]);

            $value = match ($metric) {
                'income' => $segmentQuery->where('type_id', $this->income)->sum('amount'),
                'outcome' => $segmentQuery->where('type_id', $this->outcome)->sum('amount'),
                'net' => $segmentQuery->get()->reduce(
                    fn($carry, $tx) => $carry + ($tx->type_id === $this->income ? $tx->amount : -$tx->amount),
                    0
                ),
                'margin' => $this->getMarginValue($segmentQuery),
                default => 0,
            };

            $results[] = round($value, 2);
        }

        return $results;
    }

    private function getPreviousPeriodFilters(array $filters): array
    {
        if (empty($filters['date_from']) || empty($filters['date_to'])) {
            // fallback to last full period (e.g. last month)
            $end = now()->startOfDay();
            $start = $end->copy()->subDays(30);
        } else {
            $start = \Carbon\Carbon::parse($filters['date_from'])->startOfDay();
            $end = \Carbon\Carbon::parse($filters['date_to'])->endOfDay();
        }

        $period = $start->diffInDays($end);
        $prevEnd = $start->copy()->subDay();
        $prevStart = $prevEnd->copy()->subDays($period);

        return array_merge($filters, [
            'date_from' => $prevStart->toDateString(),
            'date_to' => $prevEnd->toDateString(),
        ]);
    }

    private function getMarginValue($query): float
    {
        $income = (clone $query)->where('type_id', $this->income)->sum('amount');
        $outcome = (clone $query)->where('type_id', $this->outcome)->sum('amount');
        $net = $income - $outcome;

        return $income > 0 ? round(($net / $income) * 100, 2) : 0;
    }
}
