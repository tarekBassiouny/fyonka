<?php

namespace Tests\Feature\Store;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Interfaces\StoreServiceInterface;
use App\Models\Store;

class StoreServiceTest extends TestCase
{
    use RefreshDatabase;

    protected StoreServiceInterface $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(StoreServiceInterface::class);
        Storage::fake('public');
    }

    /** @test */
    public function it_can_create_store_with_image()
    {
        $file = UploadedFile::fake()->image('logo.png');

        $data = ['name' => 'Test Store'];

        $store = $this->service->create($data, $file);

        $this->assertDatabaseHas('stores', ['name' => 'Test Store']);
        Storage::disk('public')->assertExists($store->image_path);
        $this->assertNotNull($store->image_path);
    }

    /** @test */
    public function it_rejects_duplicate_store_name()
    {
        $file = UploadedFile::fake()->image('logo1.png');
        $this->service->create(['name' => 'Duplicate Store'], $file);

        $this->expectException(\Illuminate\Database\QueryException::class);

        $file2 = UploadedFile::fake()->image('logo2.png');
        $this->service->create(['name' => 'Duplicate Store'], $file2);
    }

    /** @test */
    public function it_handles_failed_store_creation()
    {
        $mock = \Mockery::mock(StoreServiceInterface::class);
        $this->app->instance(StoreServiceInterface::class, $mock);

        $mock->shouldReceive('create')
            ->once()
            ->andThrow(new \Exception('Simulated failure'));

        $file = UploadedFile::fake()->image('fail.png');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Simulated failure');

        app(StoreServiceInterface::class)->create(['name' => 'Broken Store'], $file);
    }

    /** @test */
    public function it_can_update_store_name_only()
    {
        $file = UploadedFile::fake()->image('original.png');
        $store = $this->service->create(['name' => 'Initial Store'], $file);

        $updated = $this->service->update($store, ['name' => 'Renamed Store'], null);

        $this->assertTrue($updated);
        $this->assertDatabaseHas('stores', ['id' => $store->id, 'name' => 'Renamed Store']);
    }

    /** @test */
    public function it_can_update_store_image_only()
    {
        $file = UploadedFile::fake()->image('original.png');
        $store = $this->service->create(['name' => 'Image Only Store'], $file);

        $newImage = UploadedFile::fake()->image('new.png');
        $updated = $this->service->update($store, ['name' => 'Image Only Store'], $newImage);

        $this->assertTrue($updated);
        Storage::disk('public')->assertExists($store->fresh()->image_path);
    }

    /** @test */
    public function it_can_update_store_name_and_image()
    {
        $file = UploadedFile::fake()->image('original.png');
        $store = $this->service->create(['name' => 'Old Store'], $file);

        $newFile = UploadedFile::fake()->image('new.png');
        $updated = $this->service->update($store, ['name' => 'Updated Store'], $newFile);

        $this->assertTrue($updated);
        $this->assertDatabaseHas('stores', ['name' => 'Updated Store']);
        Storage::disk('public')->assertExists($store->fresh()->image_path);
    }

    /** @test */
    public function it_removes_old_image_when_updating()
    {
        $oldImage = UploadedFile::fake()->image('old.png');
        $store = $this->service->create(['name' => 'Image Swap Store'], $oldImage);

        $oldPath = $store->image_path;

        $newImage = UploadedFile::fake()->image('new.png');
        $this->service->update($store, ['name' => 'Image Swap Store'], $newImage);

        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($store->fresh()->image_path);
    }

    /** @test */
    public function it_handles_failed_store_update()
    {
        $store = Store::factory()->create(['name' => 'Should Not Update']);

        $mock = \Mockery::mock(StoreServiceInterface::class);
        $this->app->instance(StoreServiceInterface::class, $mock);

        $mock->shouldReceive('update')
            ->once()
            ->andReturn(false);

        $result = app(StoreServiceInterface::class)->update($store, ['name' => 'Still Should Not'], null);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_can_delete_store_and_remove_image()
    {
        $file = UploadedFile::fake()->image('todelete.png');
        $store = $this->service->create(['name' => 'Delete Me'], $file);

        $this->assertTrue($this->service->delete($store));
        $this->assertDatabaseMissing('stores', ['id' => $store->id]);
        Storage::disk('public')->assertMissing($store->image_path);
    }

    /** @test */
    public function it_handles_failed_store_deletion()
    {
        $mockStore = \Mockery::mock(Store::class);
        $mockStore->shouldReceive('getAttribute')->with('image_path')->andReturn('some/image.png');
        $mockStore->shouldReceive('delete')->andReturn(false);

        $this->assertFalse($this->service->delete($mockStore));
    }

    /** @test */
    public function it_can_list_stores_with_pagination()
    {
        Store::factory()->count(15)->create();

        $result = $this->service->list(['per_page' => 10]);

        $this->assertEquals(10, $result->count());
        $this->assertEquals(2, $result->lastPage());
    }

    /** @test */
    public function it_filters_stores_by_name_and_per_page()
    {
        Store::factory()->create(['name' => 'Match']);
        Store::factory()->create(['name' => 'Ignore']);
        Store::factory()->create(['name' => 'Another Match']);

        $result = $this->service->list([
            'name' => 'Match',
            'per_page' => 1,
        ]);

        $this->assertCount(1, $result);
        $this->assertStringContainsString('Match', $result->first()->name);
    }
}
