<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class OrderSequenceGenerator
{
    /**
     * Generate a unique logistics number.
     * Format: LOGI-YYYYMMDD-{SEQ}
     * SEQ is a zero-padded daily sequence.
     *
     * @return string
     */
    public function generateLogisticsNumber(): string
    {
        $date = now()->format('Ymd');
        $sequenceName = "order_logistics_{$date}";

        $dailySequence = $this->getNextSequenceValue($sequenceName);

        // Pad sequence to 5 digits, e.g. 00001
        $paddedSequence = str_pad($dailySequence, 5, '0', STR_PAD_LEFT);

        return "LOGI-{$date}-{$paddedSequence}";
    }

    /**
     * Atomically get the next value for a named sequence.
     *
     * @param string $name
     * @return int
     */
    protected function getNextSequenceValue(string $name): int
    {
        return DB::transaction(function () use ($name) {
            // Lock the row for update
            $sequence = DB::table('sequences')->where('name', $name)->lockForUpdate()->first();

            if ($sequence) {
                $next = $sequence->current_value + 1;
                DB::table('sequences')->where('name', $name)->update([
                    'current_value' => $next,
                    'updated_at' => now()
                ]);
                return $next;
            } else {
                // Initialize sequence
                // Try catch to handle race condition on creation
                try {
                    DB::table('sequences')->insert([
                        'name' => $name,
                        'current_value' => 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    return 1;
                } catch (\Exception $e) {
                    // One retry if race condition hit
                    $sequence = DB::table('sequences')->where('name', $name)->lockForUpdate()->first();
                    if ($sequence) {
                        $next = $sequence->current_value + 1;
                        DB::table('sequences')->where('name', $name)->update([
                            'current_value' => $next,
                            'updated_at' => now()
                        ]);
                        return $next;
                    }
                    throw $e; // Should not happen if race was just creation
                }
            }
        });
    }
}
