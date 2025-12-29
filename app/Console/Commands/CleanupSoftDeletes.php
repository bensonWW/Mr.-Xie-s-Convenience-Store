<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanupSoftDeletes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:soft-deletes 
                            {--days= : Days to keep soft-deleted records (default: from config)}
                            {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permanently delete soft-deleted records older than retention period';

    /**
     * Tables with soft deletes to clean up.
     * Add model classes that use SoftDeletes trait.
     */
    protected array $softDeleteModels = [
        // Add your models with SoftDeletes here
        // \App\Models\User::class,
        // \App\Models\Product::class,
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (!config('retention.soft_deletes.enabled', true)) {
            $this->info('Soft delete cleanup is disabled in config.');
            return 0;
        }

        $days = $this->option('days') ?? config('retention.soft_deletes.days', 90);
        $dryRun = $this->option('dry-run');
        $cutoffDate = now()->subDays($days);

        $this->info("Cleaning up soft-deleted records older than {$days} days (before {$cutoffDate})");

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No records will be deleted');
        }

        $totalDeleted = 0;

        foreach ($this->softDeleteModels as $modelClass) {
            if (!class_exists($modelClass)) {
                $this->warn("Model class not found: {$modelClass}");
                continue;
            }

            $query = $modelClass::onlyTrashed()
                ->where('deleted_at', '<', $cutoffDate);

            $count = $query->count();

            if ($count === 0) {
                continue;
            }

            $modelName = class_basename($modelClass);
            $this->line("  {$modelName}: {$count} records to delete");

            if (!$dryRun) {
                $query->forceDelete();
                Log::info("Cleanup: Force deleted {$count} {$modelName} records");
            }

            $totalDeleted += $count;
        }

        // Clean up expired sessions
        $this->cleanupSessions($cutoffDate, $dryRun, $totalDeleted);

        // Clean up old sequences
        $this->cleanupSequences($dryRun, $totalDeleted);

        $this->newLine();
        $this->info("Total records " . ($dryRun ? 'to be ' : '') . "deleted: {$totalDeleted}");

        return 0;
    }

    /**
     * Clean up expired sessions from database.
     */
    protected function cleanupSessions($cutoffDate, bool $dryRun, int &$totalDeleted): void
    {
        if (!config('retention.sessions.cleanup_enabled', true)) {
            return;
        }

        $sessionDays = config('retention.sessions.expire_after_days', 30);
        $sessionCutoff = now()->subDays($sessionDays)->timestamp;

        // Only if using database session driver
        if (config('session.driver') !== 'database') {
            return;
        }

        $count = DB::table('sessions')
            ->where('last_activity', '<', $sessionCutoff)
            ->count();

        if ($count > 0) {
            $this->line("  Sessions: {$count} expired sessions");
            if (!$dryRun) {
                DB::table('sessions')
                    ->where('last_activity', '<', $sessionCutoff)
                    ->delete();
                Log::info("Cleanup: Deleted {$count} expired sessions");
            }
            $totalDeleted += $count;
        }
    }

    /**
     * Clean up old sequence records.
     */
    protected function cleanupSequences(bool $dryRun, int &$totalDeleted): void
    {
        if (!config('retention.sequences.cleanup_enabled', true)) {
            return;
        }

        $keepDays = config('retention.sequences.keep_days', 90);
        $cutoff = now()->subDays($keepDays);

        $count = DB::table('sequences')
            ->where('updated_at', '<', $cutoff)
            ->count();

        if ($count > 0) {
            $this->line("  Sequences: {$count} old sequence records");
            if (!$dryRun) {
                DB::table('sequences')
                    ->where('updated_at', '<', $cutoff)
                    ->delete();
                Log::info("Cleanup: Deleted {$count} old sequence records");
            }
            $totalDeleted += $count;
        }
    }
}
