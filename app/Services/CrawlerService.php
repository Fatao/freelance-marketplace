<?php

namespace App\Services;

use App\Models\CrawlerSource;
use App\Models\CrawlerLog;
use App\Models\ExternalOrder;
use App\Models\Category;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

class CrawlerService
{
    private int $found    = 0;
    private int $created  = 0;
    private int $updated  = 0;
    private int $archived = 0;
    private int $errors   = 0;
    private array $errorDetails = [];

    public function run(CrawlerSource $source, ?int $startedBy, string $trigger): CrawlerLog
    {
        $this->reset();
        $startedAt = now();

        $log = CrawlerLog::create([
            'crawler_source_id' => $source->id,
            'started_by'        => $startedBy,
            'trigger'           => $trigger,
            'started_at'        => $startedAt,
            'found'             => 0,
            'created'           => 0,
            'updated'           => 0,
            'archived'          => 0,
            'errors'            => 0,
        ]);

        try {
            $this->crawl($source);
        } catch (\Exception $e) {
            $this->errors++;
            $this->errorDetails[] = 'Критическая ошибка: ' . $e->getMessage();
            Log::error("CrawlerService: {$source->name} — " . $e->getMessage());
            $source->update(['status' => 'error']);
        }

        $log->update([
            'found'        => $this->found,
            'created'      => $this->created,
            'updated'      => $this->updated,
            'archived'     => $this->archived,
            'errors'       => $this->errors,
            'error_details'=> implode("\n", $this->errorDetails),
            'finished_at'  => now(),
        ]);

        $source->update(['last_run_at' => now()]);

        return $log;
    }

    private function crawl(CrawlerSource $source): void
    {
        $crawlRules   = $source->crawl_rules;
        $extractRules = $source->extract_rules;

        $pages = $this->getPages($source->base_url, $crawlRules);

        foreach ($pages as $pageUrl) {
            try {
                $html = $this->fetchPage($pageUrl);
                if (!$html) continue;

                $dom   = new Crawler($html);
                $items = $dom->filter($crawlRules['item_selector'] ?? 'article');

                $items->each(function (Crawler $item) use ($source, $extractRules, $pageUrl) {
                    try {
                        $data = $this->extractData($item, $extractRules, $source->base_url);
                        if (!$data || empty($data['title']) || empty($data['source_url'])) return;

                        $this->found++;
                        $this->saveOrder($source, $data);
                    } catch (\Exception $e) {
                        $this->errors++;
                        $this->errorDetails[] = "Ошибка извлечения: " . $e->getMessage();
                    }
                });

                // Rate limiting — be polite to external sites
                usleep(500000); // 0.5s between pages

            } catch (\Exception $e) {
                $this->errors++;
                $this->errorDetails[] = "Ошибка страницы {$pageUrl}: " . $e->getMessage();
            }
        }

        // Archive orders that are no longer found
        $this->archiveMissing($source);
    }

    private function getPages(string $baseUrl, array $rules): array
    {
        $pages   = [$baseUrl];
        $maxPages = $rules['max_pages'] ?? 1;

        if ($maxPages <= 1) return $pages;

        $pattern = $rules['pagination_pattern'] ?? null;
        if (!$pattern) return $pages;

        for ($i = 2; $i <= $maxPages; $i++) {
            $pages[] = str_replace('{page}', $i, $pattern);
        }

        return $pages;
    }

    private function fetchPage(string $url): ?string
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (compatible; FreelanceMarketBot/1.0)',
                'Accept'     => 'text/html,application/xhtml+xml',
            ])->timeout(15)->get($url);

            if ($response->successful()) {
                return $response->body();
            }
        } catch (\Exception $e) {
            Log::warning("Crawler fetch failed: {$url} — " . $e->getMessage());
        }
        return null;
    }

    private function extractData(Crawler $item, array $rules, string $baseUrl): ?array
    {
        $get = function (string $selector, string $attr = 'text') use ($item): ?string {
            try {
                $node = $item->filter($selector);
                if (!$node->count()) return null;
                return $attr === 'text'
                    ? trim($node->first()->text())
                    : trim($node->first()->attr($attr));
            } catch (\Exception $e) {
                return null;
            }
        };

        $sourceUrl = $get($rules['url_selector'] ?? 'a', 'href');
        if ($sourceUrl && !str_starts_with($sourceUrl, 'http')) {
            $sourceUrl = rtrim($baseUrl, '/') . '/' . ltrim($sourceUrl, '/');
        }

        $budgetRaw = $get($rules['budget_selector'] ?? '.budget');
        $budget    = $budgetRaw ? (float) preg_replace('/[^\d.]/', '', $budgetRaw) : null;

        return [
            'title'       => $get($rules['title_selector'] ?? 'h2'),
            'description' => $get($rules['description_selector'] ?? 'p'),
            'source_url'  => $sourceUrl,
            'budget'      => $budget,
            'skills'      => $this->extractSkills($get($rules['skills_selector'] ?? '.skills')),
        ];
    }

    private function extractSkills(?string $raw): array
    {
        if (!$raw) return [];
        return array_values(array_filter(
            array_map('trim', explode(',', $raw))
        ));
    }

    private function saveOrder(CrawlerSource $source, array $data): void
    {
        $existing = ExternalOrder::where('source_url', $data['source_url'])->first();

        if ($existing) {
            $existing->update([
                'title'           => $data['title'],
                'description'     => $data['description'],
                'budget'          => $data['budget'],
                'skills'          => $data['skills'],
                'status'          => 'active',
                'last_updated_at' => now(),
            ]);
            $this->updated++;
        } else {
            ExternalOrder::create([
                'crawler_source_id' => $source->id,
                'title'             => $data['title'],
                'description'       => $data['description'],
                'budget'            => $data['budget'],
                'skills'            => $data['skills'],
                'source_url'        => $data['source_url'],
                'status'            => 'new',
                'discovered_at'     => now(),
                'last_updated_at'   => now(),
            ]);
            $this->created++;

            // Check saved searches
            app(NotificationService::class)
                ->notifyMatchingSavedSearchesExternal(
                    $data['title'],
                    $data['skills'] ?? []
                );
        }
    }

    private function archiveMissing(CrawlerSource $source): void
    {
        $threshold = now()->subHours(48);

        $archived = ExternalOrder::where('crawler_source_id', $source->id)
            ->where('status', 'active')
            ->where('last_updated_at', '<', $threshold)
            ->update(['status' => 'archived']);

        $this->archived += $archived;
    }

    private function reset(): void
    {
        $this->found = $this->created = $this->updated
                     = $this->archived = $this->errors = 0;
        $this->errorDetails = [];
    }
}