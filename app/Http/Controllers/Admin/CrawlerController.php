<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CrawlerSource;
use App\Models\CrawlerLog;
use App\Services\CrawlerService;
use Illuminate\Http\Request;

class CrawlerController extends Controller
{
    public function __construct(private CrawlerService $crawler) {}

    public function run(CrawlerSource $source)
    {
        try {
            $crawlRules = is_array($source->crawl_rules)
                ? $source->crawl_rules
                : (json_decode($source->crawl_rules, true) ?? []);

            if (($crawlRules['mode'] ?? '') === 'api') {
                $this->crawler->runApi($source, auth()->id(), 'manual');
            } else {
                $this->crawler->run($source, auth()->id(), 'manual');
            }

            return redirect()->route('admin.crawler.logs')
                ->with('success', "Краулер для «{$source->name}» запущен.");
        } catch (\Exception $e) {
            return redirect()->route('admin.crawler.logs')
                ->with('error', 'Ошибка: ' . $e->getMessage());
        }
    }

    public function runAll()
    {
        $sources = CrawlerSource::where('status', 'active')->get();
        foreach ($sources as $source) {
            try {
                $crawlRules = is_array($source->crawl_rules)
                    ? $source->crawl_rules
                    : (json_decode($source->crawl_rules, true) ?? []);

                if (($crawlRules['mode'] ?? '') === 'api') {
                    $this->crawler->runApi($source, auth()->id(), 'manual');
                } else {
                    $this->crawler->run($source, auth()->id(), 'manual');
                }
            } catch (\Exception $e) {
                \Log::error("Crawler failed [{$source->name}]: " . $e->getMessage());
            }
        }

        return redirect()->route('admin.crawler.logs')
            ->with('success', 'Все активные источники запущены.');
    }

    public function logs(Request $request)
    {
        $logs = CrawlerLog::with(['source', 'startedBy'])
            ->orderByDesc('started_at')
            ->paginate(30);
        $sources = CrawlerSource::all();
        return view('admin.crawler.logs', compact('logs', 'sources'));
    }
}