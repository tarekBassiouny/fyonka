<?php

namespace Tests\Feature\PDF;

use Tests\TestCase;
use App\Models\Store;
use App\Models\Transaction;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Interfaces\DashboardServiceInterface;

class DashboardServicesRenderPDFTest extends TestCase
{
    use RefreshDatabase;

    protected DashboardServiceInterface $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(DashboardServiceInterface::class);
    }

    /** @test */
    public function it_can_render_pdf_with_valid_store(): void
    {
        $store = Store::factory()->create();
        Transaction::factory()->create(['store_id' => $store->id, 'is_temp' => false]);

        $filters = ['store_id' => $store->id];
        $pdf = $this->service->renderPDF($filters, $store);

        $this->assertInstanceOf(\Barryvdh\DomPDF\PDF::class, $pdf);
        $this->assertNotEmpty($pdf->output());
    }

    /** @test */
    public function it_can_fallback_to_default_logo_when_store_image_is_missing(): void
    {
        $store = Store::factory()->create(['image_path' => 'store_image/missing.png']);
        Transaction::factory()->create(['store_id' => $store->id, 'is_temp' => false]);

        $filters = ['store_id' => $store->id];
        $pdf = $this->service->renderPDF($filters, $store);

        $this->assertInstanceOf(\Barryvdh\DomPDF\PDF::class, $pdf);
    }

    /** @test */
    public function it_can_render_pdf_with_null_store(): void
    {
        Transaction::factory()->create(['is_temp' => false]);

        $filters = [];
        $pdf = $this->service->renderPDF($filters, null);

        $this->assertInstanceOf(\Barryvdh\DomPDF\PDF::class, $pdf);
    }

    /** @test */
    public function it_can_render_pdf_with_empty_transaction_list(): void
    {
        $store = Store::factory()->create();

        $filters = [
            'store_id' => $store->id,
            'date_from' => now()->subYear()->toDateString(),
            'date_to' => now()->subYear()->toDateString(),
        ];

        $pdf = $this->service->renderPDF($filters, $store);
        $this->assertInstanceOf(\Barryvdh\DomPDF\PDF::class, $pdf);
    }

    /** @test */
    public function it_can_render_pdf_with_missing_dates(): void
    {
        $store = Store::factory()->create();
        Transaction::factory()->create(['store_id' => $store->id, 'is_temp' => false]);

        $filters = ['store_id' => $store->id];
        $pdf = $this->service->renderPDF($filters, $store);

        $this->assertInstanceOf(\Barryvdh\DomPDF\PDF::class, $pdf);
    }

    /** @test */
    public function it_can_render_pdf_with_future_date_range(): void
    {
        $store = Store::factory()->create();

        $filters = [
            'store_id' => $store->id,
            'date_from' => now()->addYear()->toDateString(),
            'date_to' => now()->addYears(2)->toDateString(),
        ];

        $pdf = $this->service->renderPDF($filters, $store);
        $this->assertInstanceOf(\Barryvdh\DomPDF\PDF::class, $pdf);
    }
}
