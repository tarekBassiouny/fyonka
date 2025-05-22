<?php

namespace Tests\Feature\File;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Interfaces\UploadedFileServiceInterface;
use App\Models\UploadedFile as UploadedFileModel;

class UploadedFileServiceTest extends TestCase
{
    use RefreshDatabase;

    protected UploadedFileServiceInterface $service;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake(); // use default disk for now
        $this->service = app(UploadedFileServiceInterface::class);
    }

    /** @test */
    public function it_can_store_uploaded_file_and_record_to_db()
    {
        $file = UploadedFile::fake()->create('transactions.csv', 100, 'text/csv');

        $uploaded = $this->service->storeUploadedFile($file);

        $this->assertDatabaseHas('uploaded_files', [
            'filename' => 'transactions.csv',
            'path' => $uploaded->path,
        ]);

        Storage::assertExists($uploaded->path);
        $this->assertInstanceOf(UploadedFileModel::class, $uploaded);
    }

    /** @test */
    public function it_uses_date_based_folder_path()
    {
        $file = UploadedFile::fake()->create('dated.csv');
        $today = now()->format('Y-m-d');

        $uploaded = $this->service->storeUploadedFile($file);

        $this->assertStringContainsString("uploads/{$today}/", $uploaded->path);
        Storage::assertExists($uploaded->path);
    }

    /** @test */
    public function it_fails_if_file_storage_fails()
    {
        $mock = \Mockery::mock(UploadedFile::class)->makePartial();
        $mock->shouldReceive('getClientOriginalName')->andReturn('fail.csv');
        $mock->shouldReceive('store')->andReturn(false);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('File storage failed');

        $this->service->storeUploadedFile($mock);
    }

    /** @test */
    public function it_handles_duplicate_filename_gracefully()
    {
        $file1 = UploadedFile::fake()->create('dupe.csv');
        $file2 = UploadedFile::fake()->create('dupe.csv');

        $upload1 = $this->service->storeUploadedFile($file1);
        $upload2 = $this->service->storeUploadedFile($file2);

        $this->assertNotEquals($upload1->path, $upload2->path);
        $this->assertDatabaseCount('uploaded_files', 2);
    }
}
