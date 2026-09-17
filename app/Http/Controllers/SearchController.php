<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ExternalOrder;
use App\Models\Category;
use App\Models\Skill;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        // Internal orders
        $internalQuery = Order::with(['client.clientProfile', 'category', 'skills'])
            ->where('status', 'published');

        // External orders
        $externalQuery = ExternalOrder::with(['source'])
            ->whereIn('status', ['new', 'active']);

        if ($request->filled('keywords')) {
            $kw = '%' . $request->keywords . '%';
            $internalQuery->where(fn($q) =>
                $q->where('title', 'like', $kw)->orWhere('description', 'like', $kw)
            );
            $externalQuery->where(fn($q) =>
                $q->where('title', 'like', $kw)->orWhere('description', 'like', $kw)
            );
        }

        if ($request->filled('category')) {
            $internalQuery->where('category_id', $request->category);
        }

        if ($request->filled('budget_min')) {
            $internalQuery->where('budget_max', '>=', $request->budget_min);
            $externalQuery->where('budget', '>=', $request->budget_min);
        }

        if ($request->filled('budget_max')) {
            $internalQuery->where('budget_min', '<=', $request->budget_max);
            $externalQuery->where('budget', '<=', $request->budget_max);
        }

        $source = $request->get('source', 'all');

        $internalOrders = ($source !== 'external')
            ? $internalQuery->orderByDesc('published_at')->limit(20)->get()
            : collect();

        $externalOrders = ($source !== 'internal')
            ? $externalQuery->orderByDesc('discovered_at')->limit(20)->get()
            : collect();

        $categories = Category::where('is_active', true)->get();
        $skills     = Skill::orderBy('name')->get();
        $total      = $internalOrders->count() + $externalOrders->count();

        return view('search.index', compact(
            'internalOrders', 'externalOrders',
            'categories', 'skills', 'total'
        ));
    }
}