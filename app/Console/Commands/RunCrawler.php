<?php

namespace App\Console\Commands;

use App\Models\CrawlerSource;
use App\Services\CrawlerService;
use Illuminate\Console\Command;

class RunCrawler extends Command
{
    protected $signature   = 'crawler:run {--source= : Run a specific source by ID}';
    protected $description = 'Запустил веб-краулер для сбора заказов с внешних сайтов.
';

    public function __construct(private CrawlerService $crawlerService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $sourceId = $this->option('source');

        if ($sourceId) {
            $source = CrawlerSource::find($sourceId);
            if (!$source) {
                $this->error("Источник #{$sourceId} не найдено.");
                return 1;
            }
            $this->runSource($source);
        } else {
            $sources = CrawlerSource::where('status', 'active')->get();
            if ($sources->isEmpty()) {
                $this->warn('Активные источники не найдены!');
                return 0;
            }
            foreach ($sources as $source) {
                $this->runSource($source);
            }
        }

        return 0;
    }

    private function runSource(CrawlerSource $source): void
    {
        $this->info("Запуск краулера для: {$source->name}");
        try {
            $log = $this->crawlerService->run($source, null, 'manual');
            $this->info("Готово — найдено: {$log->found}, Создано: {$log->created}, Обновлено: {$log->updated}, Errors: {$log->errors}");
        } catch (\Exception $e) {
            $this->error("Не удалось: " . $e->getMessage());
        }
    }
}