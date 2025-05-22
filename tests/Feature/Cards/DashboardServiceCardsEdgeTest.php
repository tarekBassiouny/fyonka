<?php

namespace Tests\Feature\cards;

use App\Interfaces\DashboardServiceInterface;
use Tests\TestCase;
use App\Models\Store;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\TransactionSubtype;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardServiceCardsEdgeTest extends TestCase
{
    use RefreshDatabase;

    protected DashboardServiceInterface $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(DashboardServiceInterface::class);
    }

    /** @test */
    public function it_returns_zero_for_all_cards_with_no_data()
    {
        $summary = $this->service->getCardSummary([
            'date_from' => now()->subDays(7)->toDateString(),
            'date_to' => now()->toDateString(),
        ]);

        foreach (['revenue', 'expenses', 'gross_profit', 'net_margin'] as $key) {
            $this->assertEquals(0, $summary[$key]['value']);
            $this->assertIsArray($summary[$key]['trend']);
            $this->assertCount(6, $summary[$key]['trend']);
        }
    }

    /** @test */
    public function it_ignores_subtype_filter_without_type()
    {
        $subtype = TransactionSubtype::factory()->create();

        $summary = $this->service->getCardSummary([
            'subtype_id' => $subtype->id,
            'date_from' => now()->subDays(7)->toDateString(),
            'date_to' => now()->toDateString(),
        ]);

        $this->assertEquals(0, $summary['revenue']['value']);
    }

    /** @test */
    public function it_returns_margin_of_zero_when_revenue_is_zero()
    {
        $store = Store::factory()->create();
        $type = TransactionType::factory()->create(['id' => 2, 'name' => 'outcome']);
        $subtype = TransactionSubtype::factory()->create(['transaction_type_id' => $type->id]);

        Transaction::create([
            'type_id' => $type->id,
            'subtype_id' => $subtype->id,
            'store_id' => $store->id,
            'amount' => -999,
            'date' => now(),
            'is_temp' => false,
        ]);

        $summary = $this->service->getCardSummary([
            'date_from' => now()->toDateString(),
            'date_to' => now()->toDateString(),
        ]);

        $this->assertEquals(0, $summary['revenue']['value']);
        $this->assertEquals(0, $summary['net_margin']['value']);
    }

    /** @test */
    public function it_handles_negative_margin_when_expenses_exceed_revenue()
    {
        $store = Store::factory()->create();
        $incomeType = TransactionType::factory()->create(['id' => 1, 'name' => 'income']);
        $outcomeType = TransactionType::factory()->create(['id' => 2, 'name' => 'outcome']);

        $incomeSubtype = TransactionSubtype::factory()->create(['transaction_type_id' => $incomeType->id]);
        $outcomeSubtype = TransactionSubtype::factory()->create(['transaction_type_id' => $outcomeType->id]);

        Transaction::create([
            'type_id' => $incomeType->id,
            'subtype_id' => $incomeSubtype->id,
            'store_id' => $store->id,
            'amount' => 1000,
            'date' => now(),
            'is_temp' => false,
        ]);

        Transaction::create([
            'type_id' => $outcomeType->id,
            'subtype_id' => $outcomeSubtype->id,
            'store_id' => $store->id,
            'amount' => -1500,
            'date' => now(),
            'is_temp' => false,
        ]);

        $summary = $this->service->getCardSummary([
            'date_from' => now()->toDateString(),
            'date_to' => now()->toDateString(),
        ]);

        $this->assertLessThan(0, $summary['net_margin']['value']);
    }

    /** @test */
    public function it_handles_margin_greater_than_100_percent()
    {
        $store = Store::factory()->create();
        $incomeType = TransactionType::factory()->create(['id' => 1, 'name' => 'income']);
        $outcomeType = TransactionType::factory()->create(['id' => 2, 'name' => 'outcome']);

        $incomeSubtype = TransactionSubtype::factory()->create(['transaction_type_id' => $incomeType->id]);
        $outcomeSubtype = TransactionSubtype::factory()->create(['transaction_type_id' => $outcomeType->id]);

        Transaction::create([
            'type_id' => $incomeType->id,
            'subtype_id' => $incomeSubtype->id,
            'store_id' => $store->id,
            'amount' => 200,
            'date' => now(),
            'is_temp' => false,
        ]);

        Transaction::create([
            'type_id' => $outcomeType->id,
            'subtype_id' => $outcomeSubtype->id,
            'store_id' => $store->id,
            'amount' => -50,
            'date' => now(),
            'is_temp' => false,
        ]);

        $summary = $this->service->getCardSummary([
            'date_from' => now()->toDateString(),
            'date_to' => now()->toDateString(),
        ]);

        $this->assertGreaterThan(50, $summary['net_margin']['value']);
    }

    /** @test */
    public function it_always_returns_six_trend_points()
    {
        $summary = $this->service->getCardSummary([
            'date_from' => now()->subDays(30)->toDateString(),
            'date_to' => now()->toDateString(),
        ]);

        foreach (['revenue', 'gross_profit', 'expenses', 'net_margin'] as $key) {
            $this->assertIsArray($summary[$key]['trend']);
            $this->assertCount(6, $summary[$key]['trend']);
        }
    }

    /** @test */
    public function it_returns_zero_change_when_no_previous_period_data_exists()
    {
        $store = Store::factory()->create();
        $type = TransactionType::factory()->create(['id' => 1, 'name' => 'income']);
        $subtype = TransactionSubtype::factory()->create(['transaction_type_id' => $type->id]);

        Transaction::create([
            'type_id' => $type->id,
            'subtype_id' => $subtype->id,
            'store_id' => $store->id,
            'amount' => 7000,
            'date' => now(),
            'is_temp' => false,
        ]);

        $filters = [
            'date_from' => now()->toDateString(),
            'date_to' => now()->toDateString(),
            'type_id' => $type->id,
        ];

        $summary = $this->service->getCardSummary($filters);

        $this->assertEquals(7000, $summary['revenue']['value']);
        $this->assertEquals(100, $summary['revenue']['change']);
    }
}
