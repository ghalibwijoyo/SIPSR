<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Document;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        $users     = User::all();
        $documents = Document::withTrashed()->get();

        $activities = [
            ['jenis' => 'LOGIN',              'detail' => 'User berhasil login ke sistem'],
            ['jenis' => 'LOGOUT',             'detail' => 'User logout dari sistem'],
            ['jenis' => 'UPLOAD_DOKUMEN',     'detail' => 'Mengupload dokumen baru'],
            ['jenis' => 'EDIT_DOKUMEN',       'detail' => 'Mengedit metadata dokumen'],
            ['jenis' => 'HAPUS_DOKUMEN',      'detail' => 'Menghapus dokumen (soft delete)'],
            ['jenis' => 'RESTORE_DOKUMEN',    'detail' => 'Memulihkan dokumen dari recycle bin'],
            ['jenis' => 'DOWNLOAD_DOKUMEN',   'detail' => 'Mengunduh file dokumen'],
            ['jenis' => 'SHARE_LINK',         'detail' => 'Membuat share link dokumen'],
            ['jenis' => 'TAMBAH_USER',        'detail' => 'Menambahkan user baru'],
            ['jenis' => 'EDIT_USER',          'detail' => 'Mengedit data user'],
            ['jenis' => 'NONAKTIFKAN_USER',   'detail' => 'Menonaktifkan akun user'],
            ['jenis' => 'TAMBAH_KATEGORI',    'detail' => 'Menambahkan kategori baru'],
            ['jenis' => 'EDIT_KATEGORI',      'detail' => 'Mengedit nama kategori'],
            ['jenis' => 'HAPUS_KATEGORI',     'detail' => 'Menghapus kategori'],
            ['jenis' => 'LIHAT_DOKUMEN',      'detail' => 'Melihat detail dokumen'],
            ['jenis' => 'GANTI_PASSWORD',     'detail' => 'Mengganti password sendiri'],
            ['jenis' => 'UPLOAD_DOKUMEN',     'detail' => 'Mengupload dokumen laporan'],
            ['jenis' => 'LOGIN',              'detail' => 'User berhasil login ke sistem'],
            ['jenis' => 'EDIT_DOKUMEN',       'detail' => 'Mengedit deskripsi dokumen'],
            ['jenis' => 'DOWNLOAD_DOKUMEN',   'detail' => 'Mengunduh dokumen perizinan'],
        ];

        $ipAddresses = ['192.168.1.10', '192.168.1.15', '10.0.0.5', '172.16.0.100', '192.168.1.20'];
        $userAgents  = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:120.0) Gecko/20100101 Firefox/120.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15',
        ];

        foreach ($activities as $i => $activity) {
            $user          = $users->random();
            $needsDocument = in_array($activity['jenis'], [
                'UPLOAD_DOKUMEN', 'EDIT_DOKUMEN', 'HAPUS_DOKUMEN',
                'RESTORE_DOKUMEN', 'DOWNLOAD_DOKUMEN', 'SHARE_LINK', 'LIHAT_DOKUMEN',
            ]);

            ActivityLog::create([
                'user_id'          => $user->id,
                'role_saat_itu'    => $user->role,
                'jenis_aktivitas'  => $activity['jenis'],
                'detail'           => $activity['detail'],
                'document_id'      => $needsDocument && $documents->count() > 0
                                        ? $documents->random()->id
                                        : null,
                'ip_address'       => $ipAddresses[array_rand($ipAddresses)],
                'user_agent'       => $userAgents[array_rand($userAgents)],
                'created_at'       => Carbon::now()->subDays(rand(0, 60))->subHours(rand(0, 23)),
            ]);
        }
    }
}
