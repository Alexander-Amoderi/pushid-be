<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Lobby;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat Akun Admin Utama
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@pushid.com',
            'password' => Hash::make('password123'),
            'role' => 'admin', // Pastikan role sesuai middleware
            'status' => 'active',
        ]);

        // 2. Buat Beberapa User Dummy
        $user = User::create([
            'name' => 'Player One',
            'email' => 'player@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'status' => 'active',
        ]);

        // 3. Buat Lobby Dummy untuk testing Dashboard
        $title = 'Push Rank Mythic';
        Lobby::create([
            'game_name' => 'Mobile Legends',
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(5), // SOLUSI: Tambahkan slug di sini
            'rank' => 'Mythic',
            'description' => 'Butuh Tank peka map',
            'link' => 'https://wa.me/123',
            'user_id' => $user->id,
            'status' => 'active',
        ]);
    }
}
