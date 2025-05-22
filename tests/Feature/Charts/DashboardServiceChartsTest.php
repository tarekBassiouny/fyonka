<?php

namespace Tests\Feature\Charts;

use Tests\TestCase;
use App\Models\Store;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\TransactionSubtype;
use App\Interfaces\DashboardServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardServiceChartsTest extends TestCase
{
    use RefreshDatabase;

    protected DashboardServiceInterface $service;
    protected Store $store;
    protected int $incomeTypeId;
    protected int $outcomeTypeId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(DashboardServiceInterface::class);
        $this->store = Store::factory()->create();

        $this->incomeTypeId = TransactionType::factory()->create(['id' => 1, 'name' => 'income'])->id;
        $this->outcomeTypeId = TransactionType::factory()->create(['id' => 2, 'name' => 'outcome'])->id;

        $incomeSubtype = TransactionSubtype::factory()->create(['transaction_type_id' => $this->incomeTypeId]);
        $outcomeSubtype = TransactionSubtype::factory()->create(['transaction_type_id' => $this->outcomeTypeId]);

        foreach (range(1, 12) as $month) {
            Transaction::create([
                'type_id' => $this->incomeTypeId,
                'subtype_id' => $incomeSubtype->id,
                'store_id' => $this->store->id,
                'amount' => 1000,
                'date' => now()->startOfYear()->addMonths($month - 1)->startOfMonth(),
                'is_temp' => false,
            ]);

            Transaction::create([
                'type_id' => $this->outcomeTypeId,
                'subtype_id' => $outcomeSubtype->id,
                'store_id' => $this->store->id,
                'amount' => -400,
                'date' => now()->startOfYear()->addMonths($month - 1)->startOfMonth(),
                'is_temp' => false,
            ]);
        }
    }

    /** @test */
    public function it_returns_chart_data_for_all_12_months()
    {
        $data = $this->service->getChartData([
            'date_from' => now()->startOfYear()->toDateString(),
            'date_to' => now()->endOfYear()->toDateString(),
        ]);

        $this->assertCount(12, $data['months']);
        $this->assertArrayHasKey('net_income', $data);
        $this->assertArrayHasKey('income', $data);
        $this->assertArrayHasKey('outcome', $data);
    }

    /** @test */
    public function it_returns_correct_income_and_outcome_values_per_store()
    {
        $storeName = $this->store->name;

        $data = $this->service->getChartData([
            'date_from' => now()->startOfYear()->toDateString(),
            'date_to' => now()->endOfYear()->toDateString(),
        ]);

        $this->assertEquals(array_fill(0, 12, 1000), $data['income'][$storeName]);
        $this->assertEquals(array_fill(0, 12, -400), $data['outcome'][$storeName]);
        $this->assertEquals(array_fill(0, 12, 600), $data['net_income'][$storeName]);
    }

    /** @test */
    public function it_filters_chart_data_by_store()
    {
        $storeName = $this->store->name;

        $data = $this->service->getChartData([
            'store_id' => $this->store->id,
            'date_from' => now()->startOfYear()->toDateString(),
            'date_to' => now()->endOfYear()->toDateString(),
        ]);

        $this->assertEquals(array_fill(0, 12, 1000), $data['income'][$storeName]);
        $this->assertCount(1, $data['income']); // only one store
    }

    /** @test */
    public function it_filters_chart_data_by_type()
    {
        $storeName = $this->store->name;

        $data = $this->service->getChartData([
            'type_id' => $this->incomeTypeId,
            'date_from' => now()->startOfYear()->toDateString(),
            'date_to' => now()->endOfYear()->toDateString(),
        ]);

        $this->assertEquals(array_fill(0, 12, 1000), $data['income'][$storeName]);
        $this->assertEquals(array_fill(0, 12, 0), $data['outcome'][$storeName]); // filtered out
    }

    /** @test */
    public function it_filters_chart_data_by_subtype()
    {
        $incomeSubtype = TransactionSubtype::where('transaction_type_id', $this->incomeTypeId)->first();
        $storeName = $this->store->name;

        $data = $this->service->getChartData([
            'subtype_id' => $incomeSubtype->id,
            'date_from' => now()->startOfYear()->toDateString(),
            'date_to' => now()->endOfYear()->toDateString(),
        ]);

        $this->assertEquals(array_fill(0, 12, 1000), $data['income'][$storeName]);
        $this->assertEquals(array_fill(0, 12, 0), $data['outcome'][$storeName]);
    }

    /** @test */
    public function it_applies_combined_filters_correctly()
    {
        $incomeSubtype = TransactionSubtype::where('transaction_type_id', $this->incomeTypeId)->first();
        $storeName = $this->store->name;

        $data = $this->service->getChartData([
            'type_id' => $this->incomeTypeId,
            'subtype_id' => $incomeSubtype->id,
            'store_id' => $this->store->id,
            'date_from' => now()->startOfYear()->toDateString(),
            'date_to' => now()->endOfYear()->toDateString(),
        ]);

        $this->assertEquals(array_fill(0, 12, 1000), $data['income'][$storeName]);
        $this->assertEquals(array_fill(0, 12, 0), $data['outcome'][$storeName]);
    }
}
