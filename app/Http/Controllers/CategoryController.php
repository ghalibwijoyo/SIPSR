<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('documents')->orderBy('nama')->paginate(20);
        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255', 'unique:categories'],
        ], [
            'nama.unique' => 'Kategori sudah ada.'
        ]);

        $category = Category::create($validated);

        ActivityLog::create([
            'user_id' => request()->user()->id,
            'jenis_aktivitas' => 'TAMBAH_KATEGORI',
            'deskripsi' => 'Menambahkan kategori baru: ' . $category->nama,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255', 'unique:categories,nama,' . $category->id],
        ], [
            'nama.unique' => 'Kategori sudah ada.'
        ]);

        $oldName = $category->nama;
        $category->update($validated);

        ActivityLog::create([
            'user_id' => request()->user()->id,
            'jenis_aktivitas' => 'EDIT_KATEGORI',
            'deskripsi' => "Memperbarui kategori: $oldName menjadi " . $category->nama,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        if ($category->documents()->count() > 0) {
            return redirect()->route('categories.index')->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh dokumen aktif.');
        }

        $name = $category->nama;
        $category->delete();

        ActivityLog::create([
            'user_id' => request()->user()->id,
            'jenis_aktivitas' => 'HAPUS_KATEGORI',
            'deskripsi' => 'Menghapus kategori: ' . $name,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
