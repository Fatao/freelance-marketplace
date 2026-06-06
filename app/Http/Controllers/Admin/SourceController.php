<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CrawlerSource;
use Illuminate\Http\Request;

class SourceController extends Controller
{
    public function index()
    {
        $sources = CrawlerSource::withCount(['externalOrders', 'logs'])
            ->orderByDesc('created_at')->get();
        return view('admin.sources.index', compact('sources'));
    }

    public function create()
    {
        return view('admin.sources.form', ['source' => new CrawlerSource()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        CrawlerSource::create($data);
        return redirect()->route('admin.sources.index')->with('success', 'Источник добавлен.');
    }

    public function edit(CrawlerSource $source)
    {
        return view('admin.sources.form', compact('source'));
    }

    public function update(Request $request, CrawlerSource $source)
    {
        $source->update($this->validated($request));
        return redirect()->route('admin.sources.index')->with('success', 'Источник обновлён.');
    }

    public function destroy(CrawlerSource $source)
    {
        $source->delete();
        return redirect()->route('admin.sources.index')->with('success', 'Источник удалён.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name'               => 'required|string|max:255',
            'base_url'           => 'required|url',
            'crawl_rules'        => 'required|json',
            'extract_rules'      => 'required|json',
            'frequency_minutes'  => 'required|integer|min:5',
            'status'             => 'required|in:active,disabled,error',
        ]);
    }
}