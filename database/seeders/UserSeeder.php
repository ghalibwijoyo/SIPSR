<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'nik' => 'admin',
                'password' => 'Admin1234',
                'nama_lengkap' => 'Administrator',
                'role' => 'ADMIN',
                'is_active' => true,
            ],
            [
                'nik' => 'budi_psr',
                'password' => 'Staff1234',
                'nama_lengkap' => 'Budi Santoso',
                'role' => 'STAFF',
                'is_active' => true,
            ],
            [
                'nik' => 'sari_psr',
                'password' => 'Staff1234',
                'nama_lengkap' => 'Sari Dewi',
                'role' => 'STAFF',
                'is_active' => true,
            ],
            [
                'nik' => 'eko_psr',
                'password' => 'Staff1234',
                'nama_lengkap' => 'Eko Prasetyo',
                'role' => 'STAFF',
                'is_active' => true,
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
