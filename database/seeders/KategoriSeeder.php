<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Surat Masuk',
            'Surat Keluar',
            'Nota Dinas',
            'Laporan Bulanan',
            'Laporan Tahunan',
            'Berita Acara',
            'SPK (Surat Perintah Kerja)',
            'SK / Keputusan',
            'Dokumen Teknis',
            'Dokumen Perencanaan',
            'Perizinan',
            'Dokumen Keuangan',
        ];

        foreach ($categories as $nama) {
            Category::create(['nama' => $nama]);
        }
    }
}
