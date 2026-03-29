<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoriesController extends Controller
{
    public function categories()
    {
        $categories = Category::all();
        return view('categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'required|in:0,1'
        ]);

        $baseSlug = Str::slug($request->name);
        $slug = $baseSlug;
        $count = 1;

        while (Category::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $count;
            $count++;
        }

        Category::create([
            'name' => $request->name,
            'slug' => $slug,
            'is_active' => $request->is_active,
        ]);

        return redirect()->back()->with('success', 'Category added successfully!');
    }

    // Silme Metodu - Senin rotandaki isimlendirmeyle uyumlu
    public function deleteCategory($id) 
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->route('categories')->with('success', 'Category successfully deleted.');
    }
}