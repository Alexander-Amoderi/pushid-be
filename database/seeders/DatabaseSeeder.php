<?php

namespace Database\Seeders;

// database/seeders/DatabaseSeeder.php

use Illuminate\Database\Seeder;
use App\Models\User; // JANGAN LUPA IMPORT MODEL USER

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. BUAT SATU USER DULU (ID = 1)
        User::factory()->create([
            'name' => 'Seeder User',
            'email' => 'seeder@pushid.com',
            // password bawaan factory biasanya 'password'
        ]);

        // 2. Kemudian panggil LobbySeeder
        $this->call([
            LobbySeeder::class,
        ]);
    }
}
