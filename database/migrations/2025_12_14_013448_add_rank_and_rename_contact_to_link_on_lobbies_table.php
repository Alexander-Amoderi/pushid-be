<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lobbies', function (Blueprint $table) {
            // 1. TAMBAH KOLOM RANK
            $table->string('rank')->nullable()->after('game_name'); 

            // 2. RENAME CONTACT_INFO menjadi LINK
            $table->renameColumn('contact_info', 'link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lobbies', function (Blueprint $table) {
            // 1. Rollback RENAME (Kembalikan ke contact_info)
            $table->renameColumn('link', 'contact_info'); 

            // 2. Hapus Kolom RANK
            $table->dropColumn('rank');
        });
    }
};
