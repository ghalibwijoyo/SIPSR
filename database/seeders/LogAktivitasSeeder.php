<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Document;
use Carbon\Carbon;

class ActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $documents = Document::pluck('id')->toArray();

        // Realistic activity templates
        $activities = [
            ['jenis' => 'LOGIN_BERHASIL', 'detail' => 'User berhasil login ke sistem'],
            ['jenis' => 'LOGOUT', 'detail' => 'User logout dari sistem'],
            ['jenis' => 'UPLOAD_DOKUMEN', 'detail' => 'Mengupload dokumen baru ke arsip'],
            ['jenis' => 'DOWNLOAD_DOKUMEN', 'detail' => 'Mengunduh dokumen dari arsip'],
            ['jenis' => 'HAPUS_DOKUMEN', 'detail' => 'Menghapus dokumen ke Recycle Bin'],
            ['jenis' => 'RESTORE_DOKUMEN', 'detail' => 'Memulihkan dokumen dari Recycle Bin'],
            ['jenis' => 'LIHAT_DOKUMEN', 'detail' => 'Melihat detail dokumen'],
            ['jenis' => 'EDIT_PROFIL', 'detail' => 'Memperbarui data profil'],
            ['jenis' => 'GANTI_PASSWORD', 'detail' => 'Mengubah password akun'],
            ['jenis' => 'EXPORT_LAPORAN', 'detail' => 'Mengekspor laporan dokumen ke PDF'],
            ['jenis' => 'BUAT_SHARE_LINK', 'detail' => 'Membuat link berbagi dokumen'],
        ];

        $ipAddresses = [
            '192.168.1.10', '192.168.1.15', '192.168.1.20',
            '10.0.0.5', '10.0.0.12', '172.16.0.8',
            '127.0.0.1',
        ];

        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/149.0.0.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 Chrome/148.0.0.0',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 18_5 like Mac OS X) AppleWebKit/605.1.15',
            'Mozilla/5.0 (Linux; Android 15) AppleWebKit/537.36 Chrome/149.0.0.0 Mobile',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0',
        ];

        // Generate 150 random activity logs spanning the last 6 months
        $startDate = Carbon::now()->subMonths(6);
        $endDate = Carbon::now();

        for ($i = 0; $i < 150; $i++) {
            $randomUser = $users->random();
            $activity = $activities[array_rand($activities)];
            $randomDate = Carbon::createFromTimestamp(
                mt_rand($startDate->timestamp, $endDate->timestamp)
            );

            // Assign document_id only for document-related activities
            $documentId = null;
            if (in_array($activity['jenis'], [
                'UPLOAD_DOKUMEN', 'DOWNLOAD_DOKUMEN', 'HAPUS_DOKUMEN',
                'RESTORE_DOKUMEN', 'LIHAT_DOKUMEN', 'BUAT_SHARE_LINK'
            ]) && !empty($documents)) {
                $documentId = $documents[array_rand($documents)];
            }

            ActivityLog::create([
                'user_id'          => $randomUser->id,
                'role_saat_itu'    => $randomUser->role,
                'jenis_aktivitas'  => $activity['jenis'],
                'detail'           => $activity['detail'],
                'document_id'      => $documentId,
                'ip_address'       => $ipAddresses[array_rand($ipAddresses)],
                'user_agent'       => $userAgents[array_rand($userAgents)],
                'created_at'       => $randomDate,
            ]);
        }
    }
}
