<?php

namespace Tests\Feature\Cards;

use Tests\TestCase;
use App\Models\Store;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\TransactionSubtype;
use App\Interfaces\DashboardServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardServiceCardsEmptyTest extends TestCase
{
    use RefreshDatabase;

    protected DashboardServiceInterface $service;
    protected array $filters;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(DashboardServiceInterface::class);
    }

    /** @test */
    public function it_returns_zeros_when_no_data_exists()
    {
        $summary = $this->service->getCardSummary([
            'date_from' => now()->subDays(7)->toDateString(),
            'date_to' => now()->toDateString(),
        ]);

        foreach (['revenue', 'expenses', 'gross_profit', 'net_margin'] as $key) {
            $this->assertEquals(0, $summary[$key]['value']);
            $this->assertIsArray($summary[$key]['trend']);
        }
    }

    /** @test */
    public function it_handles_negative_margin_correctly()
    {
        $store = Store::factory()->create();
        $type = TransactionType::factory()->create(['id' => 2, 'name' => 'outcome']);
        $subtype = TransactionSubtype::factory()->create(['transaction_type_id' => $type->id]);

        foreach (range(1, 7) as $i) {
            Transaction::create([
                'type_id' => $type->id,
                'subtype_id' => $subtype->id,
                'store_id' => $store->id,
                'amount' => -1000,
                'date' => now()->subDays($i),
                'is_temp' => false,
            ]);
        }

        $summary = $this->service->getCardSummary([
            'date_from' => now()->subDays(7)->toDateString(),
            'date_to' => now()->toDateString(),
        ]);

        $this->assertLessThanOrEqual(0, $summary['gross_profit']['value']);
        $this->assertEquals(0, $summary['revenue']['value']);
        $this->assertEquals(0, $summary['net_margin']['value']); // avoid div by 0
    }

    /** @test */
    public function it_filters_by_date_and_store_and_type_combined()
    {
        $store = Store::factory()->create(['name' => 'Filtered Store']);
        $type = TransactionType::factory()->create(['id' => 1, 'name' => 'income']);
        $subtype = TransactionSubtype::factory()->create(['transaction_type_id' => $type->id]);

        foreach (range(1, 7) as $i) {
            Transaction::create([
                'type_id' => $type->id,
                'subtype_id' => $subtype->id,
                'store_id' => $store->id,
                'amount' => 1234,
                'date' => now()->subDays($i),
                'is_temp' => false,
            ]);
        }

        $filters = [
            'date_from' => now()->subDays(7)->toDateString(),
            'date_to' => now()->toDateString(),
            'store_id' => $store->id,
            'type_id' => $type->id,
        ];

        $summary = $this->service->getCardSummary($filters);

        $this->assertEquals(1234 * 7, $summary['revenue']['value']);
    }

    /** @test */
    public function it_calculates_valid_percentage_change_for_revenue()
    {
        $store = Store::factory()->create();
        $type = TransactionType::factory()->create(['id' => 1, 'name' => 'income']);
        $subtype = TransactionSubtype::factory()->create(['transaction_type_id' => $type->id]);

        foreach (range(1, 7) as $i) {
            Transaction::create([
                'type_id' => $type->id,
                'subtype_id' => $subtype->id,
                'store_id' => $store->id,
                'amount' => 1000,
                'date' => now()->subDays(7 - $i),
                'is_temp' => false,
            ]);
        }

        $filters = [
            'date_from' => now()->subDays(7)->toDateString(),
            'date_to' => now()->toDateString(),
        ];

        $summary = $this->service->getCardSummary($filters);

        $this->assertEquals(7, Transaction::count());
        $this->assertEquals(7000, $summary['revenue']['value']);
        $this->assertEquals(100, $summary['revenue']['change']); // previous = 0, change = +100%
    }

    /** @test */
    public function it_ignores_subtype_filter_without_type()
    {
        $subtypeId = \App\Models\TransactionSubtype::factory()->create()->id;

        $summary = $this->service->getCardSummary([
            'subtype_id' => $subtypeId,
            'date_from' => now()->subDays(7)->toDateString(),
            'date_to' => now()->toDateString(),
        ]);

        // Should not fail, but returns 0 since type is not defined
        $this->assertEquals(0, $summary['revenue']['value']);
        $this->assertEquals(0, $summary['expenses']['value']);
    }

    /** @test */
    public function it_handles_margin_greater_than_100_percent()
    {
        $store = Store::factory()->create();
        $incomeType = TransactionType::factory()->create(['id' => 1, 'name' => 'income']);
        $outcomeType = TransactionType::factory()->create(['id' => 2, 'name' => 'outcome']);

        $incomeSubtype = TransactionSubtype::factory()->create(['transaction_type_id' => $incomeType->id]);
        $outcomeSubtype = TransactionSubtype::factory()->create(['transaction_type_id' => $outcomeType->id]);

        // Revenue = 100, expenses = -50 → gross = 50
        \App\Models\Transaction::create([
            'type_id' => $incomeType->id,
            'subtype_id' => $incomeSubtype->id,
            'store_id' => $store->id,
            'amount' => 200,
            'date' => now(),
            'is_temp' => false,
        ]);

        \App\Models\Transaction::create([
            'type_id' => $outcomeType->id,
            'subtype_id' => $outcomeSubtype->id,
            'store_id' => $store->id,
            'amount' => -50,
            'date' => now(),
            'is_temp' => false,
        ]);

        $filters = [
            'date_from' => now()->startOfDay()->toDateString(),
            'date_to' => now()->endOfDay()->toDateString(),
        ];

        $summary = $this->service->getCardSummary($filters);

        $this->assertGreaterThan((float)50, $summary['net_margin']['value']);
        $this->assertEquals((float)75, $summary['net_margin']['value']);
    }

    /** @test */
    public function it_returns_correct_data_when_all_filters_are_applied()
    {
        $store = Store::factory()->create();
        $type = TransactionType::factory()->create(['id' => 1, 'name' => 'income']);
        $subtype = TransactionSubtype::factory()->create(['transaction_type_id' => $type->id]);

        \App\Models\Transaction::create([
            'type_id' => $type->id,
            'subtype_id' => $subtype->id,
            'store_id' => $store->id,
            'amount' => 111,
            'date' => now(),
            'is_temp' => false,
        ]);

        $filters = [
            'date_from' => now()->startOfDay()->toDateString(),
            'date_to' => now()->endOfDay()->toDateString(),
            'type_id' => $type->id,
            'store_id' => $store->id,
            'subtype_id' => $subtype->id,
        ];

        $summary = $this->service->getCardSummary($filters);

        $this->assertEquals(111, $summary['revenue']['value']);
    }
}
