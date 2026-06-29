<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Document;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $categories = Category::all();

        $documents = [
            ['nomor' => 'SM/001/PSR/2026', 'nama' => 'Surat Masuk dari Dinas Pertanian', 'kategori' => 'Surat Masuk', 'tanggal' => '2026-01-10', 'deleted' => false],
            ['nomor' => 'SM/002/PSR/2026', 'nama' => 'Surat Masuk Permintaan Data Produksi', 'kategori' => 'Surat Masuk', 'tanggal' => '2026-01-15', 'deleted' => false],
            ['nomor' => 'SK/001/PSR/2026', 'nama' => 'Surat Keluar Balasan Dinas Pertanian', 'kategori' => 'Surat Keluar', 'tanggal' => '2026-01-20', 'deleted' => false],
            ['nomor' => 'ND/001/PSR/2026', 'nama' => 'Nota Dinas Pengadaan Pupuk', 'kategori' => 'Nota Dinas', 'tanggal' => '2026-02-05', 'deleted' => false],
            ['nomor' => 'LB/001/PSR/2026', 'nama' => 'Laporan Bulanan Januari 2026', 'kategori' => 'Laporan Bulanan', 'tanggal' => '2026-02-01', 'deleted' => false],
            ['nomor' => 'LB/002/PSR/2026', 'nama' => 'Laporan Bulanan Februari 2026', 'kategori' => 'Laporan Bulanan', 'tanggal' => '2026-03-01', 'deleted' => true],
            ['nomor' => 'LT/001/PSR/2025', 'nama' => 'Laporan Tahunan 2025', 'kategori' => 'Laporan Tahunan', 'tanggal' => '2026-01-30', 'deleted' => false],
            ['nomor' => 'BA/001/PSR/2026', 'nama' => 'Berita Acara Serah Terima Lahan', 'kategori' => 'Berita Acara', 'tanggal' => '2026-02-10', 'deleted' => false],
            ['nomor' => 'BA/002/PSR/2026', 'nama' => 'Berita Acara Rapat Koordinasi', 'kategori' => 'Berita Acara', 'tanggal' => '2026-03-05', 'deleted' => true],
            ['nomor' => 'SPK/001/PSR/2026', 'nama' => 'SPK Pemeliharaan Kebun Blok A', 'kategori' => 'SPK (Surat Perintah Kerja)', 'tanggal' => '2026-02-15', 'deleted' => false],
            ['nomor' => 'SPK/002/PSR/2026', 'nama' => 'SPK Pengangkutan Hasil Panen', 'kategori' => 'SPK (Surat Perintah Kerja)', 'tanggal' => '2026-03-10', 'deleted' => false],
            ['nomor' => 'SK-K/001/PSR/2026', 'nama' => 'SK Penunjukan Tim Inventarisasi', 'kategori' => 'SK / Keputusan', 'tanggal' => '2026-01-05', 'deleted' => false],
            ['nomor' => 'DT/001/PSR/2026', 'nama' => 'Dokumen Teknis Penanaman Sawit', 'kategori' => 'Dokumen Teknis', 'tanggal' => '2026-03-15', 'deleted' => false],
            ['nomor' => 'DT/002/PSR/2026', 'nama' => 'SOP Pemupukan Tanaman', 'kategori' => 'Dokumen Teknis', 'tanggal' => '2026-04-01', 'deleted' => true],
            ['nomor' => 'DP/001/PSR/2026', 'nama' => 'Rencana Kerja Tahunan 2026', 'kategori' => 'Dokumen Perencanaan', 'tanggal' => '2026-01-02', 'deleted' => false],
            ['nomor' => 'DP/002/PSR/2026', 'nama' => 'RKAP Bidang PSR 2026', 'kategori' => 'Dokumen Perencanaan', 'tanggal' => '2026-01-03', 'deleted' => false],
            ['nomor' => 'PZ/001/PSR/2026', 'nama' => 'Izin Lingkungan Pembukaan Lahan', 'kategori' => 'Perizinan', 'tanggal' => '2026-02-20', 'deleted' => true],
            ['nomor' => 'PZ/002/PSR/2026', 'nama' => 'Perizinan Penggunaan Air', 'kategori' => 'Perizinan', 'tanggal' => '2026-03-20', 'deleted' => false],
            ['nomor' => 'DK/001/PSR/2026', 'nama' => 'Anggaran Operasional Q1 2026', 'kategori' => 'Dokumen Keuangan', 'tanggal' => '2026-01-08', 'deleted' => false],
            ['nomor' => 'DK/002/PSR/2026', 'nama' => 'Realisasi Anggaran Februari 2026', 'kategori' => 'Dokumen Keuangan', 'tanggal' => '2026-03-01', 'deleted' => true],
        ];

        foreach ($documents as $i => $doc) {
            $tanggal = Carbon::parse($doc['tanggal']);
            $uploader = $users->random();

            $document = new Document;
            $document->nomor_dokumen = $doc['nomor'];
            $document->nama_dokumen = $doc['nama'];
            $document->category_id = $categories->firstWhere('nama', $doc['kategori'])->id;
            $document->tanggal_dokumen = $tanggal;
            $document->deskripsi = 'Deskripsi untuk dokumen '.$doc['nama'];
            $document->file_path = 'uploads/'.$tanggal->format('Y').'/'.$tanggal->format('m').'/dummy_'.($i + 1).'.pdf';
            $document->file_name = 'dummy_'.($i + 1).'.pdf';
            $document->uploader_id = $uploader->id;
            $document->created_at = $tanggal;
            $document->updated_at = $tanggal;

            if ($doc['deleted']) {
                $document->deleted_at = $tanggal->copy()->addDays(rand(5, 30));
                $document->deleted_by_id = $users->firstWhere('role', 'ADMIN')->id;
            }

            $document->save();
        }
    }
}
