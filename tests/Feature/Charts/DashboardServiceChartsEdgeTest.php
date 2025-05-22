<?php

namespace Tests\Feature\Charts;

use Tests\TestCase;
use App\Models\Store;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\TransactionSubtype;
use App\Interfaces\DashboardServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardServiceChartsEdgeTest extends TestCase
{
    use RefreshDatabase;

    protected DashboardServiceInterface $service;
    protected int $incomeTypeId;
    protected int $outcomeTypeId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(DashboardServiceInterface::class);
        $this->incomeTypeId = TransactionType::factory()->create(['id' => 1, 'name' => 'income'])->id;
        $this->outcomeTypeId = TransactionType::factory()->create(['id' => 2, 'name' => 'outcome'])->id;
    }

    /** @test */
    public function it_handles_store_with_only_income()
    {
        $store = Store::factory()->create();
        $subtype = TransactionSubtype::factory()->create(['transaction_type_id' => $this->incomeTypeId]);

        Transaction::create([
            'type_id' => $this->incomeTypeId,
            'subtype_id' => $subtype->id,
            'store_id' => $store->id,
            'amount' => 2000,
            'date' => now()->startOfYear(),
            'is_temp' => false,
        ]);

        $data = $this->service->getChartData([
            'date_from' => now()->startOfYear()->toDateString(),
            'date_to' => now()->endOfYear()->toDateString(),
        ]);

        $this->assertCount(12, $data['months']);
        $this->assertEquals(2000, $data['income'][$store->name][0]);
        $this->assertEquals(0, $data['outcome'][$store->name][0]);
        $this->assertEquals(2000, $data['net_income'][$store->name][0]);
    }

    /** @test */
    public function it_handles_multiple_stores_with_varied_data()
    {
        $store1 = Store::factory()->create();
        $store2 = Store::factory()->create();

        $subtype1 = TransactionSubtype::factory()->create(['transaction_type_id' => $this->incomeTypeId]);
        $subtype2 = TransactionSubtype::factory()->create(['transaction_type_id' => $this->outcomeTypeId]);

        // Store 1 — income only in Jan
        Transaction::create([
            'type_id' => $this->incomeTypeId,
            'subtype_id' => $subtype1->id,
            'store_id' => $store1->id,
            'amount' => 1000,
            'date' => now()->startOfYear(),
            'is_temp' => false,
        ]);

        // Store 2 — expenses only in Feb
        Transaction::create([
            'type_id' => $this->outcomeTypeId,
            'subtype_id' => $subtype2->id,
            'store_id' => $store2->id,
            'amount' => -600,
            'date' => now()->startOfYear()->addMonth(),
            'is_temp' => false,
        ]);

        $data = $this->service->getChartData([
            'date_from' => now()->startOfYear()->toDateString(),
            'date_to' => now()->endOfYear()->toDateString(),
        ]);

        // Store 1 checks
        $this->assertEquals(1000, $data['income'][$store1->name][0]);
        $this->assertEquals(0, $data['outcome'][$store1->name][0]);
        $this->assertEquals(1000, $data['net_income'][$store1->name][0]);

        // Store 2 checks
        $this->assertEquals(0, $data['income'][$store2->name][1]);
        $this->assertEquals(-600, $data['outcome'][$store2->name][1]);
        $this->assertEquals(-600, $data['net_income'][$store2->name][1]);
    }

    /** @test */
    public function it_handles_sparse_months_with_gap_in_data()
    {
        $store = Store::factory()->create();
        $subtype = TransactionSubtype::factory()->create(['transaction_type_id' => $this->incomeTypeId]);

        // Income only in Jan, June, Dec
        foreach ([1, 6, 12] as $month) {
            Transaction::create([
                'type_id' => $this->incomeTypeId,
                'subtype_id' => $subtype->id,
                'store_id' => $store->id,
                'amount' => 100 * $month,
                'date' => now()->startOfYear()->addMonths($month - 1),
                'is_temp' => false,
            ]);
        }

        $data = $this->service->getChartData([
            'date_from' => now()->startOfYear()->toDateString(),
            'date_to' => now()->endOfYear()->toDateString(),
        ]);

        $income = $data['income'][$store->name];
        $this->assertEquals(100, $income[0]);
        $this->assertEquals(600, $income[5]);
        $this->assertEquals(1200, $income[11]);

        // All other months should be 0
        foreach ([1, 2, 3, 4, 6, 7, 8, 9, 10] as $i) {
            $this->assertEquals(0, $income[$i]);
        }
    }
}
