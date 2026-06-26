<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function index()
    {
        $banks = Bank::withCount('documents')->orderBy('nama')->paginate(20);
        return view('admin.banks.index', compact('banks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255', 'unique:banks'],
        ], [
            'nama.unique' => 'Bank sudah ada.'
        ]);

        $bank = Bank::create($validated);

        ActivityLog::create([
            'user_id' => request()->user()->id,
            'role_saat_itu' => request()->user()->role,
            'jenis_aktivitas' => 'TAMBAH_BANK',
            'detail' => 'Menambahkan bank baru: ' . $bank->nama,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()->route('banks.index')->with('success', 'Bank berhasil ditambahkan.');
    }

    public function update(Request $request, Bank $bank)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255', 'unique:banks,nama,' . $bank->id],
        ], [
            'nama.unique' => 'Bank sudah ada.'
        ]);

        $oldName = $bank->nama;
        $bank->update($validated);

        ActivityLog::create([
            'user_id' => request()->user()->id,
            'role_saat_itu' => request()->user()->role,
            'jenis_aktivitas' => 'EDIT_BANK',
            'detail' => "Memperbarui bank: $oldName menjadi " . $bank->nama,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()->route('banks.index')->with('success', 'Bank berhasil diperbarui.');
    }

    public function destroy(Bank $bank)
    {
        if ($bank->documents()->withTrashed()->count() > 0) {
            return redirect()->route('banks.index')->with('error', 'Bank tidak dapat dihapus karena masih digunakan oleh dokumen (termasuk di Recycle Bin).');
        }

        $name = $bank->nama;
        $bank->delete();

        ActivityLog::create([
            'user_id' => request()->user()->id,
            'role_saat_itu' => request()->user()->role,
            'jenis_aktivitas' => 'HAPUS_BANK',
            'detail' => 'Menghapus bank: ' . $name,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()->route('banks.index')->with('success', 'Bank berhasil dihapus.');
    }
}
