<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LobbySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('lobbies')->insert([
            [
                'user_id' => 3,
                'game_name' => 'MLBB',
                'title' => 'Cari 2 orang buat push rank Legend ke Mythic!',
                'slug' => Str::slug('cari 2 orang buat push rank legend ke mythic'),
                'description' => 'Mabar santai, role bebas, yang penting ga toxic.',
                'contact_info' => '@andika_mlbb',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 4,
                'game_name' => 'Valorant',
                'title' => 'Recruit 3 agents for Competitive Ascent/Immortal',
                'slug' => Str::slug('recruit 3 agents for competitive ascent immortal'),
                'description' => 'Need good duelist and initiator. Mic on.',
                'contact_info' => 'Discord: Budi#1234',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 5,
                'game_name' => 'Elden Ring',
                'title' => 'Co-op Boss Fight: Malenia, Blade of Miquella',
                'slug' => Str::slug('co-op boss fight malenia blade of miquella'),
                'description' => 'Butuh summoner yang handal. Password: MALENIA1',
                'contact_info' => 'WA: 0812xxxxxx',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}