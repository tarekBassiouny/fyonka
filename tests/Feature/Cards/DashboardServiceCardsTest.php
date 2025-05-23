<?php

namespace Tests\Feature\Cards;

use Tests\TestCase;
use App\Models\Store;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\TransactionSubtype;
use App\Interfaces\DashboardServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardServiceCardsTest extends TestCase
{
    use RefreshDatabase;

    protected DashboardServiceInterface $service;
    protected array $filters;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedTestData();
        $this->service = app(DashboardServiceInterface::class);

        $this->filters = [
            'date_from' => now()->subDays(7)->toDateString(),
            'date_to' => now()->toDateString(),
        ];
    }

    private function seedTestData(): void
    {
        $store = Store::factory()->create(['name' => 'Test Store']);

        $incomeType = TransactionType::factory()->create(['id' => 1, 'name' => 'income']);
        $outcomeType = TransactionType::factory()->create(['id' => 2, 'name' => 'outcome']);

        $incomeSubtype = TransactionSubtype::factory()->create([
            'name' => 'Sales',
            'transaction_type_id' => $incomeType->id,
        ]);

        $outcomeSubtype = TransactionSubtype::factory()->create([
            'name' => 'Expenses',
            'transaction_type_id' => $outcomeType->id,
        ]);

        foreach (range(1, 7) as $i) {
            $date = now()->subDays(7 - $i);

            Transaction::create([
                'type_id' => $incomeType->id,
                'subtype_id' => $incomeSubtype->id,
                'store_id' => $store->id,
                'amount' => 1000,
                'description' => 'Income',
                'date' => $date,
                'is_temp' => false,
            ]);

            Transaction::create([
                'type_id' => $outcomeType->id,
                'subtype_id' => $outcomeSubtype->id,
                'store_id' => $store->id,
                'amount' => -400,
                'description' => 'Expense',
                'date' => $date,
                'is_temp' => false,
            ]);
        }
    }

    /** @test */
    public function it_returns_all_card_keys_with_expected_structure()
    {
        $summary = $this->service->getCardSummary($this->filters);

        foreach (['revenue', 'gross_profit', 'net_margin', 'expenses'] as $key) {
            $this->assertArrayHasKey($key, $summary);

            $this->assertArrayHasKey('value', $summary[$key]);
            $this->assertArrayHasKey('change', $summary[$key]);
            $this->assertArrayHasKey('trend', $summary[$key]);

            $this->assertIsNumeric($summary[$key]['value']);
            $this->assertIsNumeric($summary[$key]['change']);
            $this->assertIsArray($summary[$key]['trend']);
        }
    }

    /** @test */
    public function it_returns_correct_card_values()
    {
        $summary = $this->service->getCardSummary($this->filters);
        
        $this->assertEquals(7000, $summary['revenue']['value']);
        $this->assertEquals(-2800, $summary['expenses']['value']);
        $this->assertEquals(4200, $summary['gross_profit']['value']);
        $this->assertEquals(60.0, $summary['net_margin']['value']);
    }

    /** @test */
    public function it_computes_valid_percentage_change()
    {
        $summary = $this->service->getCardSummary($this->filters);

        foreach (['revenue', 'gross_profit', 'expenses'] as $key) {
            $this->assertIsNumeric($summary[$key]['change']);
            $this->assertLessThanOrEqual(100, abs($summary[$key]['change']));
        }
    }

    /** @test */
    public function it_returns_non_empty_trend_arrays()
    {
        $summary = $this->service->getCardSummary($this->filters);

        foreach (['revenue', 'gross_profit', 'expenses', 'net_margin'] as $key) {
            $this->assertCount(6, $summary[$key]['trend']);
            foreach ($summary[$key]['trend'] as $point) {
                $this->assertIsNumeric($point);
            }
        }
    }

    /** @test */
    public function it_applies_filters_correctly()
    {
        $filters = $this->filters;
        $filters['store_id'] = Store::first()->id;

        $summary = $this->service->getCardSummary($filters);
        $this->assertEquals(7000, $summary['revenue']['value']); // filtered but still 7k
    }

    /** @test */
    public function it_applies_date_filter_correctly()
    {
        $filters = [
            'date_from' => now()->subDays(3)->toDateString(),
            'date_to' => now()->toDateString(),
        ];

        $summary = $this->service->getCardSummary($filters);

        // 4 days of 1000 income = 4000, 400 * 4 = 1600
        $this->assertEquals(4000, $summary['revenue']['value']);
        $this->assertEquals(-1600, $summary['expenses']['value']);
    }

    /** @test */
    public function it_applies_type_filter_correctly()
    {
        $filters = $this->filters;
        $filters['type_id'] = TransactionType::where('name', 'income')->value('id');

        $summary = $this->service->getCardSummary($filters);

        $this->assertEquals(7000, $summary['revenue']['value']);
        $this->assertEquals(0, $summary['expenses']['value']);
    }

    /** @test */
    public function it_applies_subtype_filter_correctly()
    {
        $filters = $this->filters;
        $filters['subtype_id'] = TransactionSubtype::where('name', 'Sales')->value('id');

        $summary = $this->service->getCardSummary($filters);

        $this->assertEquals(7000, $summary['revenue']['value']);
        $this->assertEquals(0, $summary['expenses']['value']);
    }

    /** @test */
    public function it_returns_valid_trend_array_structure()
    {
        $summary = $this->service->getCardSummary($this->filters);

        foreach (['revenue', 'gross_profit', 'expenses', 'net_margin'] as $key) {
            $trend = $summary[$key]['trend'];

            $this->assertIsArray($trend);
            $this->assertCount(6, $trend); // assuming 6 time steps
            foreach ($trend as $point) {
                $this->assertIsNumeric($point);
            }
        }
    }

    /** @test */
    public function trend_data_always_returns_6_points()
    {
        $summary = $this->service->getCardSummary([
            'date_from' => now()->subDays(30)->toDateString(),
            'date_to' => now()->toDateString(),
        ]);

        foreach (['revenue', 'gross_profit', 'expenses', 'net_margin'] as $key) {
            $this->assertCount(6, $summary[$key]['trend']);
        }
    }
}
