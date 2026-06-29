<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('documents')->orderBy('nama')->paginate(20);

        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255', 'unique:categories'],
        ], [
            'nama.unique' => 'Kategori sudah ada.',
        ]);

        $category = Category::create($validated);

        ActivityLog::create([
            'user_id' => request()->user()->id,
            'role_saat_itu' => request()->user()->role,
            'jenis_aktivitas' => 'TAMBAH_KATEGORI',
            'detail' => 'Menambahkan kategori baru: '.$category->nama,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255', 'unique:categories,nama,'.$category->id],
        ], [
            'nama.unique' => 'Kategori sudah ada.',
        ]);

        $oldName = $category->nama;
        $category->update($validated);

        ActivityLog::create([
            'user_id' => request()->user()->id,
            'role_saat_itu' => request()->user()->role,
            'jenis_aktivitas' => 'EDIT_KATEGORI',
            'detail' => "Memperbarui kategori: $oldName menjadi ".$category->nama,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        if ($category->documents()->withTrashed()->count() > 0) {
            return redirect()->route('categories.index')->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh dokumen (termasuk di Recycle Bin).');
        }

        $name = $category->nama;
        $category->delete();

        ActivityLog::create([
            'user_id' => request()->user()->id,
            'role_saat_itu' => request()->user()->role,
            'jenis_aktivitas' => 'HAPUS_KATEGORI',
            'detail' => 'Menghapus kategori: '.$name,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
