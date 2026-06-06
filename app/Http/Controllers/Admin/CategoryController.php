<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('orders')->orderBy('name')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:255']);
        Category::create(['name' => $data['name'], 'slug' => Str::slug($data['name'])]);
        return back()->with('success', 'Категория добавлена.');
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate(['name' => 'required|string|max:255', 'is_active' => 'boolean']);
        $category->update(array_merge($data, ['slug' => Str::slug($data['name'])]));
        return back()->with('success', 'Категория обновлена.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return back()->with('success', 'Категория удалена.');
    }
}