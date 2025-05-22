<?php

namespace Tests\Feature\Transaction;

use App\Interfaces\TransactionServiceInterface;
use App\Models\Store;
use App\Models\Transaction;
use App\Models\TransactionSubtype;
use App\Models\TransactionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class TransactionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TransactionServiceInterface $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(TransactionServiceInterface::class);
    }

    /** @test */
    public function it_returns_paginated_results_without_filters(): void
    {
        Transaction::factory()->count(15)->create();

        $result = $this->service->list();

        $this->assertInstanceOf(\Illuminate\Contracts\Pagination\LengthAwarePaginator::class, $result);
        $this->assertCount(10, $result->items()); // default per_page = 10
    }

    /** @test */
    public function it_filters_transactions_by_date_from(): void
    {
        Transaction::factory()->create(['date' => '2023-01-01']);
        Transaction::factory()->create(['date' => '2023-03-01']);

        $result = $this->service->list(['date_from' => '2023-02-01']);

        $this->assertCount(1, $result->items());
        $this->assertEquals('2023-03-01', $result->items()[0]->date->toDateString());
    }

    /** @test */
    public function it_filters_transactions_by_date_to(): void
    {
        Transaction::factory()->create(['date' => '2023-01-01']);
        Transaction::factory()->create(['date' => '2023-03-01']);

        $result = $this->service->list(['date_to' => '2023-02-01']);

        $this->assertCount(1, $result->items());
        $this->assertEquals('2023-01-01', $result->items()[0]->date->toDateString());
    }

    /** @test */
    public function it_filters_transactions_by_store_id(): void
    {
        $storeA = Store::factory()->create();
        $storeB = Store::factory()->create();

        Transaction::factory()->create(['store_id' => $storeA->id]);
        Transaction::factory()->create(['store_id' => $storeB->id]);

        $result = $this->service->list(['store_id' => $storeA->id]);

        $this->assertCount(1, $result->items());
        $this->assertEquals($storeA->id, $result->items()[0]->store_id);
    }

    /** @test */
    public function it_filters_transactions_by_transaction_type_id(): void
    {
        $typeIncome = TransactionType::factory()->create();
        $typeOutcome = TransactionType::factory()->create();

        Transaction::factory()->create(['type_id' => $typeIncome->id]);
        Transaction::factory()->create(['type_id' => $typeOutcome->id]);

        $result = $this->service->list(['type_id' => $typeIncome->id]);

        $this->assertCount(1, $result->items());
        $this->assertEquals($typeIncome->id, $result->items()[0]->type_id);
    }

    /** @test */
    public function it_filters_transactions_by_subtype_id(): void
    {
        $subtypeA = TransactionSubtype::factory()->create();
        $subtypeB = TransactionSubtype::factory()->create();

        Transaction::factory()->create(['subtype_id' => $subtypeA->id]);
        Transaction::factory()->create(['subtype_id' => $subtypeB->id]);

        $result = $this->service->list(['subtype_id' => $subtypeA->id]);

        $this->assertCount(1, $result->items());
        $this->assertEquals($subtypeA->id, $result->items()[0]->subtype_id);
    }

    /** @test */
    public function it_applies_all_filters_together(): void
    {
        $store = Store::factory()->create();
        $type = TransactionType::factory()->create();
        $subtype = TransactionSubtype::factory()->create(['transaction_type_id' => $type->id]);

        Transaction::factory()->create([
            'date' => '2023-03-01',
            'store_id' => $store->id,
            'type_id' => $type->id,
            'subtype_id' => $subtype->id,
        ]);

        // noise
        Transaction::factory()->count(2)->create();

        $result = $this->service->list([
            'date_from' => '2023-03-01',
            'date_to' => '2023-03-01',
            'store_id' => $store->id,
            'type_id' => $type->id,
            'subtype_id' => $subtype->id,
        ]);

        $this->assertCount(1, $result->items());
    }

    /** @test */
    public function it_limits_results_by_per_page(): void
    {
        Transaction::factory()->count(15)->create();

        $result = $this->service->list(['per_page' => 5]);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertCount(5, $result->items());
    }

    public function it_orders_transactions_with_is_temp_first(): void
    {
        Transaction::factory()->create(['is_temp' => false, 'date' => now()]);
        Transaction::factory()->create(['is_temp' => true, 'date' => now()->subDay()]);

        $result = $this->service->list();

        $this->assertTrue($result->items()[0]->is_temp);
    }


    /** @test */
    public function it_creates_a_transaction_with_valid_data(): void
    {
        $store = Store::factory()->create();
        $type = TransactionType::factory()->create();
        $subtype = TransactionSubtype::factory()->create(['transaction_type_id' => $type->id]);

        $data = [
            'amount' => 100.00,
            'description' => 'Test transaction',
            'date' => now()->toDateString(),
            'store_id' => $store->id,
            'type_id' => $type->id,
            'subtype_id' => $subtype->id,
            'is_temp' => true,
        ];
        $transaction = $this->service->create($data);

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'amount' => 100.00,
            'description' => 'Test transaction',
            'is_temp' => true,
        ]);
    }

    /** @test */
    public function it_updates_a_transaction_with_new_data(): void
    {
        $transaction = Transaction::factory()->create(['description' => 'Old description']);
        $newStore = Store::factory()->create();

        $data = [
            'description' => 'Updated description',
            'amount' => 250.50,
            'store_id' => $newStore->id,
        ];

        $result = $this->service->update($transaction, $data);

        $this->assertTrue($result);
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'description' => 'Updated description',
            'amount' => 250.50,
            'store_id' => $newStore->id,
        ]);
    }

    /** @test */
    public function it_soft_deletes_a_transaction(): void
    {
        $transaction = Transaction::factory()->create();

        $result = $this->service->delete($transaction);

        $this->assertTrue($result);
        $this->assertSoftDeleted($transaction);
    }


    /** @test */
    public function it_approves_a_transaction_and_sets_is_temp_false(): void
    {
        $transaction = Transaction::factory()->create(['is_temp' => true]);

        $data = ['description' => 'Approved transaction'];

        $result = $this->service->approve($transaction, $data);

        $this->assertTrue($result);
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'description' => 'Approved transaction',
            'is_temp' => false,
        ]);
    }

    /** @test */
    public function it_rejects_a_transaction_by_soft_deleting_it(): void
    {
        $transaction = Transaction::factory()->create(['is_temp' => true]);

        $result = $this->service->reject($transaction);

        $this->assertTrue($result);
        $this->assertSoftDeleted($transaction);
    }

    /** @test */
    public function it_bulk_approves_multiple_transactions(): void
    {
        $transactions = Transaction::factory()->count(3)->create(['is_temp' => true]);

        $data = $transactions->map(function ($tx) {
            return [
                'id' => $tx->id,
                'description' => 'Bulk approved',
            ];
        })->toArray();

        $updatedCount = $this->service->bulkApprove($data);

        $this->assertEquals(3, $updatedCount);
        foreach ($transactions as $tx) {
            $this->assertDatabaseHas('transactions', [
                'id' => $tx->id,
                'description' => 'Bulk approved',
                'is_temp' => false,
            ]);
        }
    }

    /** @test */
    public function it_bulk_rejects_transactions_by_soft_deleting_them(): void
    {
        $transactions = Transaction::factory()->count(3)->create(['is_temp' => true]);

        $ids = $transactions->pluck('id')->all();

        $this->service->bulkReject($ids);

        foreach ($transactions as $tx) {
            $this->assertSoftDeleted('transactions', ['id' => $tx->id]);
        }
    }

    /** @test */
    public function it_skips_invalid_ids_when_bulk_approving(): void
    {
        $validTransaction = Transaction::factory()->create(['is_temp' => true]);

        $data = [
            ['id' => $validTransaction->id, 'description' => 'Updated'],
            ['id' => 9999, 'description' => 'Should be skipped'], // non-existent ID
        ];

        $updatedCount = $this->service->bulkApprove($data);

        $this->assertEquals(1, $updatedCount);
        $this->assertDatabaseHas('transactions', [
            'id' => $validTransaction->id,
            'description' => 'Updated',
            'is_temp' => false,
        ]);
    }

    /** @test */
    public function it_skips_invalid_ids_when_bulk_rejecting(): void
    {
        $transaction = Transaction::factory()->create(['is_temp' => true]);

        $validId = $transaction->id;
        $invalidId = 9999;

        $this->service->bulkReject([$validId, $invalidId]);

        $this->assertSoftDeleted('transactions', ['id' => $validId]);
    }
}
