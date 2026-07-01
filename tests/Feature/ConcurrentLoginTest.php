<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ConcurrentLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_logging_in_invalidates_other_sessions_by_updating_password_hash()
    {
        // 1. Setup User
        $user = User::factory()->create([
            'username' => 'testuser',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $originalHash = $user->password;

        // 2. User login (ini akan menjalankan Auth::logoutOtherDevices yang mengubah hash password)
        $response = $this->post('/login', [
            'username' => 'testuser',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');

        // 3. Ambil data user terbaru dari database
        $user->refresh();

        // 4. Pastikan hash password telah berubah 
        // (Ini adalah mekanisme Laravel untuk membuat session lama menjadi tidak valid)
        $this->assertNotEquals($originalHash, $user->password, 'Password hash harus berubah untuk membatalkan session lama.');
        
        // 5. Pastikan password tetap valid meskipun hash berubah
        $this->assertTrue(Hash::check('password123', $user->password));
    }
}
