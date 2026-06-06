<?php

use Illuminate\Support\Facades\Schedule;
use App\Models\CrawlerSource;
use App\Services\CrawlerService;

Schedule::call(function () {
    $sources = CrawlerSource::where('status', 'active')->get();

    foreach ($sources as $source) {
        $lastRun  = $source->last_run_at;
        $interval = $source->frequency_minutes;

        if (!$lastRun || $lastRun->addMinutes($interval)->isPast()) {
            try {
                app(CrawlerService::class)->run($source, null, 'scheduled');
            } catch (\Exception $e) {
                \Log::error("Scheduled crawler failed [{$source->name}]: " . $e->getMessage());
            }
        }
    }
})->everyFiveMinutes()->name('crawler.scheduled')->withoutOverlapping();