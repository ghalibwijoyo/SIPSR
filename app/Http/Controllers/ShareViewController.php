<?php

namespace App\Http\Controllers;

use App\Models\DocumentShareLink;
use Illuminate\Http\Request;

class ShareViewController extends Controller
{
    /**
     * Handle public access ke Share Link
     */
    public function show($token)
    {
        $link = DocumentShareLink::where('token', $token)->first();

        // Cek link ada
        if (!$link) {
            return view('share.invalid', [
                'message' => 'Tautan tidak ditemukan.'
            ]);
        }

        // Cek revoked
        if ($link->revoked_at !== null) {
            return view('share.invalid', [
                'message' => 'Tautan ini telah dicabut oleh pemiliknya.'
            ]);
        }

        // Cek expired
        if ($link->expired_at < now()) {
            return view('share.invalid', [
                'message' => 'Tautan ini telah kedaluwarsa.'
            ]);
        }

        // Cek apakah dokumennya masih ada atau sudah di soft-delete
        if (!$link->document || $link->document->trashed()) {
            return view('share.invalid', [
                'message' => 'Dokumen terkait tidak ditemukan atau telah dihapus.'
            ]);
        }

        // Jika valid, redirect ke halaman detail dokumen
        return redirect()->route('dokumen.show', $link->document_id);
    }
}
