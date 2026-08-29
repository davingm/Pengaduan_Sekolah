<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        if (auth()->user()->isSiswa()) {
            abort(403, 'Akses terbatas untuk staf dan administrator.');
        }

        $categories = Category::withCount('complaints')->latest()->get();
        return view('pages.dashboard.kategori.index', compact('categories'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->isSiswa()) {
            abort(403, 'Akses ditolak.');
        }
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'icon' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:500'],
            'default_role_target' => ['nullable', 'string', 'max:50'],
        ]);

        Category::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'icon' => $validated['icon'] ?? 'folder',
            'color' => $validated['color'] ?? 'indigo',
            'description' => $validated['description'] ?? null,
            'default_role_target' => $validated['default_role_target'] ?? 'petugas',
            'is_active' => true,
        ]);

        return back()->with('success', 'Kategori pengaduan berhasil ditambahkan.');
    }

    public function update(Request $request, Category $category)
    {
        if (auth()->user()->isSiswa()) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name,' . $category->id],
            'icon' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:500'],
            'default_role_target' => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $category->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'icon' => $validated['icon'] ?? $category->icon,
            'color' => $validated['color'] ?? $category->color,
            'description' => $validated['description'] ?? null,
            'default_role_target' => $validated['default_role_target'] ?? 'petugas',
            'is_active' => $request->has('is_active'),
        ]);

        return back()->with('success', 'Kategori pengaduan berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        if (auth()->user()->isSiswa()) {
            abort(403, 'Akses ditolak.');
        }

        if ($category->complaints()->count() > 0) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena sudah memiliki data pengaduan terkait.');
        }

        $category->delete();
        return back()->with('success', 'Kategori pengaduan berhasil dihapus.');
    }
}
