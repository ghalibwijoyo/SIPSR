<?php

namespace App\Http\Controllers;

use App\Models\DocumentShareLink;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ShareViewController extends Controller
{
    /**
     * Handle public access ke Share Link
     */
    public function show($token)
    {
        $link = DocumentShareLink::where('token', $token)->first();

        // Cek link ada
        if (! $link) {
            return view('share.invalid', [
                'message' => 'Tautan tidak ditemukan.',
            ]);
        }

        // Cek revoked
        if ($link->revoked_at !== null) {
            return view('share.invalid', [
                'message' => 'Tautan ini telah dicabut oleh pemiliknya.',
            ]);
        }

        // Cek expired
        if ($link->expired_at !== null && $link->expired_at < now()) {
            return view('share.invalid', [
                'message' => 'Tautan ini telah kedaluwarsa.',
            ]);
        }

        // Cek apakah dokumennya masih ada atau sudah di soft-delete
        if (! $link->document || $link->document->trashed()) {
            return view('share.invalid', [
                'message' => 'Dokumen terkait tidak ditemukan atau telah dihapus.',
            ]);
        }

        // Jika valid, tampilkan halaman share publik (tanpa auth)
        $dokumen = $link->document->load('category');

        return view('share.show', compact('dokumen', 'link'));
    }

    /**
     * Download file via share link (public, tanpa auth)
     */
    public function download($token)
    {
        $link = $this->validateShareLink($token);
        if ($link instanceof View) {
            return $link;
        }

        $dokumen = $link->document;

        if (! Storage::disk('local')->exists($dokumen->file_path)) {
            return view('share.invalid', [
                'message' => 'File tidak ditemukan di server.',
            ]);
        }

        return Storage::disk('local')->download($dokumen->file_path, $dokumen->file_name);
    }

    /**
     * Preview / stream file via share link (public, tanpa auth)
     */
    public function preview($token)
    {
        $link = $this->validateShareLink($token);
        if ($link instanceof View) {
            abort(404, 'Tautan tidak valid.');
        }

        $dokumen = $link->document;

        if (! Storage::disk('local')->exists($dokumen->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        $mimeType = Storage::disk('local')->mimeType($dokumen->file_path);

        return response()->file(
            Storage::disk('local')->path($dokumen->file_path),
            ['Content-Type' => $mimeType]
        );
    }

    /**
     * Validasi share link, return link model atau invalid view.
     */
    private function validateShareLink($token)
    {
        $link = DocumentShareLink::where('token', $token)->first();

        if (! $link) {
            return view('share.invalid', ['message' => 'Tautan tidak ditemukan.']);
        }
        if ($link->revoked_at !== null) {
            return view('share.invalid', ['message' => 'Tautan ini telah dicabut oleh pemiliknya.']);
        }
        if ($link->expired_at !== null && $link->expired_at < now()) {
            return view('share.invalid', ['message' => 'Tautan ini telah kedaluwarsa.']);
        }
        if (! $link->document || $link->document->trashed()) {
            return view('share.invalid', ['message' => 'Dokumen terkait tidak ditemukan atau telah dihapus.']);
        }

        return $link;
    }
}
