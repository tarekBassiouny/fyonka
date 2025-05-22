<?php

namespace Tests\Feature\Charts;

use Tests\TestCase;
use App\Interfaces\DashboardServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardServiceChartsEmptyTest extends TestCase
{
    use RefreshDatabase;

    protected DashboardServiceInterface $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(DashboardServiceInterface::class);
    }

    /** @test */
    public function it_returns_valid_structure_with_empty_dataset()
    {
        $data = $this->service->getChartData([
            'date_from' => now()->startOfYear()->toDateString(),
            'date_to' => now()->endOfYear()->toDateString(),
        ]);

        $this->assertArrayHasKey('months', $data);
        $this->assertArrayHasKey('stores', $data);
        $this->assertArrayHasKey('net_income', $data);
        $this->assertArrayHasKey('income', $data);
        $this->assertArrayHasKey('outcome', $data);

        $this->assertCount(12, $data['months']);
        $this->assertEmpty($data['stores']);
        $this->assertEmpty($data['income']);
        $this->assertEmpty($data['outcome']);
        $this->assertEmpty($data['net_income']);
    }

    /** @test */
    public function it_handles_filters_that_return_no_data()
    {
        $data = $this->service->getChartData([
            'type_id' => 99999, // non-existent type
            'store_id' => 99999,
            'subtype_id' => 99999,
            'date_from' => now()->subYear()->toDateString(),
            'date_to' => now()->toDateString(),
        ]);

        $this->assertCount(12, $data['months']);
        $this->assertEmpty($data['income']);
        $this->assertEmpty($data['outcome']);
        $this->assertEmpty($data['net_income']);
    }
}

