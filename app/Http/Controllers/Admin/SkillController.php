<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SkillController extends Controller
{
    public function index()
    {
        $skills     = Skill::with('category')->orderBy('name')->paginate(30);
        $categories = Category::where('is_active', true)->get();
        return view('admin.skills.index', compact('skills', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255|unique:skills,name',
            'category_id' => 'nullable|exists:categories,id',
        ]);
        Skill::create(array_merge($data, ['slug' => Str::slug($data['name'])]));
        return back()->with('success', 'Навык добавлен.');
    }

    public function destroy(Skill $skill)
    {
        $skill->delete();
        return back()->with('success', 'Навык удалён.');
    }
}