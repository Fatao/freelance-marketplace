<?php

namespace App\Http\Controllers;

use App\Models\ExternalOrder;
use App\Models\CrawlerSource;
use Illuminate\Http\Request;

class ExternalOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = ExternalOrder::with(['source'])
            ->whereIn('status', ['new', 'active'])
            ->orderByDesc('discovered_at');

        if ($request->filled('keywords')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->keywords . '%')
                  ->orWhere('description', 'like', '%' . $request->keywords . '%');
            });
        }

        if ($request->filled('source_id')) {
            $query->where('crawler_source_id', $request->source_id);
        }

        if ($request->filled('budget_min')) {
            $query->where('budget', '>=', $request->budget_min);
        }

        if ($request->filled('budget_max')) {
            $query->where('budget', '<=', $request->budget_max);
        }

        $orders  = $query->paginate(20)->withQueryString();
        $sources = CrawlerSource::where('status', 'active')->select('id', 'name')->get();

        return view('external_orders.index', compact('orders', 'sources'));
    }

    public function show(ExternalOrder $externalOrder)
    {
        $externalOrder->load(['source']);
        return view('external_orders.show', compact('externalOrder'));
    }
}