<?php

namespace Tests\Feature;

use App\Services\OrderSequenceGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OrderSequenceGeneratorTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_generates_logistics_numbers_sequentially()
    {
        $generator = app(OrderSequenceGenerator::class);

        // First call
        $logistics1 = $generator->generateLogisticsNumber();
        // Format: LOGI-YYYYMMDD-00001
        $date = now()->format('Ymd');
        $this->assertEquals("LOGI-{$date}-00001", $logistics1);

        // Second call
        $logistics2 = $generator->generateLogisticsNumber();
        $this->assertEquals("LOGI-{$date}-00002", $logistics2);

        // Verify DB
        $this->assertDatabaseHas('sequences', [
            'name' => "order_logistics_{$date}",
            'current_value' => 2
        ]);
    }

    public function test_it_handles_multiple_sequences_for_different_days()
    {
        // Mock time if possible, or just manually insert a different day sequence
        // Laravel Time Travel
        $this->travelTo(now()->subDay());
        $yesterday = now()->format('Ymd');

        $generator = app(OrderSequenceGenerator::class);
        $logistics = $generator->generateLogisticsNumber();
        $this->assertEquals("LOGI-{$yesterday}-00001", $logistics);

        $this->travelBack();

        $today = now()->format('Ymd');
        $logisticsToday = $generator->generateLogisticsNumber();
        $this->assertEquals("LOGI-{$today}-00001", $logisticsToday);
    }
}
