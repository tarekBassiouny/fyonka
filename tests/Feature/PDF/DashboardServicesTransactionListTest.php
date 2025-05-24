<?php

namespace Tests\Feature\PDF;

use Tests\TestCase;
use App\Models\Store;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\TransactionSubtype;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardServicesTransactionListTest extends TestCase
{
    use RefreshDatabase;

    protected DashboardService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(DashboardService::class);
    }

    /** @test */
    public function it_can_return_all_non_temp_transactions(): void
    {
        Transaction::factory()->count(2)->create(['is_temp' => false]);
        Transaction::factory()->count(1)->create(['is_temp' => true]);

        $result = $this->service->transactionList([]);

        $this->assertCount(2, $result);
        $this->assertTrue($result->every(fn($trx) => !$trx->is_temp));
    }

    /** @test */
    public function it_can_filter_transactions_by_store(): void
    {
        $storeA = Store::factory()->create();
        $storeB = Store::factory()->create();

        Transaction::factory()->create(['store_id' => $storeA->id, 'is_temp' => false]);
        Transaction::factory()->create(['store_id' => $storeB->id, 'is_temp' => false]);

        $result = $this->service->transactionList(['store_id' => $storeA->id]);

        $this->assertCount(1, $result);
        $this->assertEquals($storeA->id, $result->first()->store_id);
    }

    /** @test */
    public function it_can_filter_transactions_by_date_range(): void
    {
        $inside = Transaction::factory()->create(['date' => now(), 'is_temp' => false]);
        Transaction::factory()->create(['date' => now()->subYears(2), 'is_temp' => false]);

        $filters = [
            'date_from' => now()->subDay()->toDateString(),
            'date_to' => now()->addDay()->toDateString(),
        ];

        $result = $this->service->transactionList($filters);

        $this->assertCount(1, $result);
        $this->assertTrue($result->first()->is($inside));
    }

    /** @test */
    public function it_can_filter_transactions_by_type_and_subtype(): void
    {
        $type = TransactionType::factory()->create();
        $subtype = TransactionSubtype::factory()->create(['transaction_type_id' => $type->id]);

        $included = Transaction::factory()->create([
            'type_id' => $type->id,
            'subtype_id' => $subtype->id,
            'is_temp' => false
        ]);
        Transaction::factory()->create(['is_temp' => false]);

        $filters = [
            'type_id' => $type->id,
            'subtype_id' => $subtype->id,
        ];

        $result = $this->service->transactionList($filters);

        $this->assertCount(1, $result);
        $this->assertTrue($result->first()->is($included));
    }

    /** @test */
    public function it_returns_empty_collection_if_filters_do_not_match(): void
    {
        Transaction::factory()->create(['is_temp' => false]);

        $filters = [
            'store_id' => 999, // non-existent
            'type_id' => 999,
            'subtype_id' => 999,
        ];

        $result = $this->service->transactionList($filters);

        $this->assertTrue($result->isEmpty());
    }
}
