<?php

namespace App\Http\Controllers;

use App\Models\SavedSearch;
use App\Models\Category;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SavedSearchController extends Controller
{
    public function index()
    {
        $searches = SavedSearch::where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();
        return view('saved_searches.index', compact('searches'));
    }

    public function create(Request $request)
    {
        $categories = Category::where('is_active', true)->get();
        $skills     = Skill::orderBy('name')->get();

        // Pre-fill from query string if coming from search page
        $prefill = $request->only(['keywords', 'category', 'budget_min', 'budget_max']);

        return view('saved_searches.create', compact('categories', 'skills', 'prefill'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'keywords'   => 'nullable|string|max:255',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'skills'     => 'nullable|array',
            'skills.*'   => 'exists:skills,id',
            'budget_min' => 'nullable|numeric|min:0',
            'budget_max' => 'nullable|numeric|min:0',
            'source'     => 'nullable|in:internal,external',
        ]);

        SavedSearch::create(array_merge($data, ['user_id' => Auth::id()]));

        return redirect()->route('saved-searches.index')
            ->with('success', 'Поиск сохранён. Вы получите уведомление о новых заказах.');
    }

    public function destroy(SavedSearch $savedSearch)
    {
        abort_if($savedSearch->user_id !== Auth::id(), 403);
        $savedSearch->delete();
        return back()->with('success', 'Поиск удалён.');
    }
}