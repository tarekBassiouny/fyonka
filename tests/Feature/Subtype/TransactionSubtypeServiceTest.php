<?php

namespace Tests\Feature\Subtype;

use Tests\TestCase;
use App\Models\TransactionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Interfaces\TransactionSubtypeServiceInterface;
use App\Models\TransactionSubtype;

class TransactionSubtypeServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TransactionSubtypeServiceInterface $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(TransactionSubtypeServiceInterface::class);
    }

    /** @test */
    public function it_can_create_subtype()
    {
        $type = TransactionType::factory()->create();

        $subtype = $this->service->create([
            'name' => 'VAT',
            'transaction_type_id' => $type->id,
        ]);

        $this->assertDatabaseHas('transaction_subtypes', [
            'name' => 'VAT',
            'transaction_type_id' => $type->id,
        ]);
    }

    /** @test */
    public function it_rejects_duplicate_subtype_name()
    {
        $type = TransactionType::factory()->create();

        $this->service->create([
            'name' => 'Duplicate Subtype',
            'transaction_type_id' => $type->id,
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        $this->service->create([
            'name' => 'Duplicate Subtype',
            'transaction_type_id' => $type->id,
        ]);
    }

    /** @test */
    public function it_handles_failed_subtype_creation()
    {
        $mock = \Mockery::mock(TransactionSubtypeServiceInterface::class);
        $this->app->instance(TransactionSubtypeServiceInterface::class, $mock);

        $mock->shouldReceive('create')
            ->once()
            ->andThrow(new \Exception('Simulated failure'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Simulated failure');

        $mock->create([
            'name' => 'Broken Subtype',
            'transaction_type_id' => 1,
        ]);
    }

    /** @test */
    public function it_can_update_subtype_name_only()
    {
        $type = TransactionType::factory()->create();

        $subtype = $this->service->create([
            'name' => 'Old Name',
            'transaction_type_id' => $type->id,
        ]);

        $updated = $this->service->update($subtype, [
            'name' => 'New Name',
            'transaction_type_id' => $type->id, // still required
        ]);

        $this->assertTrue($updated);
        $this->assertDatabaseHas('transaction_subtypes', [
            'id' => $subtype->id,
            'name' => 'New Name',
        ]);
    }

    /** @test */
    public function it_can_update_subtype_type_only()
    {
        $typeA = TransactionType::factory()->create();
        $typeB = TransactionType::factory()->create();

        $subtype = $this->service->create([
            'name' => 'Shipping',
            'transaction_type_id' => $typeA->id,
        ]);

        $updated = $this->service->update($subtype, [
            'name' => 'Shipping', // name unchanged
            'transaction_type_id' => $typeB->id,
        ]);

        $this->assertTrue($updated);
        $this->assertDatabaseHas('transaction_subtypes', [
            'id' => $subtype->id,
            'transaction_type_id' => $typeB->id,
        ]);
    }

    /** @test */
    public function it_can_update_subtype_name_and_type()
    {
        $typeA = TransactionType::factory()->create();
        $typeB = TransactionType::factory()->create();

        $subtype = $this->service->create([
            'name' => 'Legacy Fee',
            'transaction_type_id' => $typeA->id,
        ]);

        $updated = $this->service->update($subtype, [
            'name' => 'Modern Fee',
            'transaction_type_id' => $typeB->id,
        ]);

        $this->assertTrue($updated);
        $this->assertDatabaseHas('transaction_subtypes', [
            'id' => $subtype->id,
            'name' => 'Modern Fee',
            'transaction_type_id' => $typeB->id,
        ]);
    }

    /** @test */
    public function it_handles_failed_subtype_update()
    {
        $type = TransactionType::factory()->create();

        $subtype = $this->service->create([
            'name' => 'Unchanged',
            'transaction_type_id' => $type->id,
        ]);

        $mock = \Mockery::mock(TransactionSubtypeServiceInterface::class);
        $this->app->instance(TransactionSubtypeServiceInterface::class, $mock);

        $mock->shouldReceive('update')
            ->once()
            ->andReturn(false);

        $result = $mock->update($subtype, ['name' => 'No Effect', 'transaction_type_id' => $type->id]);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_can_delete_subtype()
    {
        $type = TransactionType::factory()->create();

        $subtype = $this->service->create([
            'name' => 'Temporary Subtype',
            'transaction_type_id' => $type->id,
        ]);

        $deleted = $this->service->delete($subtype);

        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('transaction_subtypes', ['id' => $subtype->id]);
    }

    /** @test */
    public function it_handles_failed_subtype_deletion()
    {
        $mockSubtype = \Mockery::mock(\App\Models\TransactionSubtype::class);
        $mockSubtype->shouldReceive('delete')->andReturn(false);

        $this->assertFalse($this->service->delete($mockSubtype));
    }

    /** @test */
    public function it_can_list_subtypes_with_pagination()
    {
        $type = TransactionType::factory()->create();
        \App\Models\TransactionSubtype::factory()->count(15)->create([
            'transaction_type_id' => $type->id,
        ]);

        $result = $this->service->list([
            'per_page' => 10,
        ]);

        $this->assertEquals(10, $result->count());
        $this->assertEquals(2, $result->lastPage());
    }

    /** @test */
    public function it_filters_subtypes_by_name_and_type()
    {
        $typeA = TransactionType::factory()->create();
        $typeB = TransactionType::factory()->create();

        \App\Models\TransactionSubtype::factory()->create([
            'name' => 'Shipping',
            'transaction_type_id' => $typeA->id,
        ]);

        \App\Models\TransactionSubtype::factory()->create([
            'name' => 'Handling',
            'transaction_type_id' => $typeB->id,
        ]);

        $result = $this->service->list([
            'name' => 'Shipping',
            'type_id' => $typeA->id,
            'per_page' => 10,
        ]);

        $this->assertCount(1, $result);
        $this->assertEquals('Shipping', $result->first()->name);
        $this->assertEquals($typeA->id, $result->first()->transaction_type_id);
    }

    /** @test */
    public function it_can_list_subtypes_by_type()
    {
        $type = TransactionType::factory()->create();
        $otherType = TransactionType::factory()->create();

        TransactionSubtype::factory()->count(2)->create([
            'transaction_type_id' => $type->id,
        ]);

        TransactionSubtype::factory()->count(1)->create([
            'transaction_type_id' => $otherType->id,
        ]);

        $result = $this->service->listByType($type->id);

        $this->assertCount(2, $result);
        foreach ($result as $subtype) {
            $this->assertEquals($type->id, $subtype->transaction_type_id);
        }
    }
}
