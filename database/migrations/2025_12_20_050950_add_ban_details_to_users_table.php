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
        Schema::table('users', function (Blueprint $table) {
            // Menyimpan tanggal berakhirnya ban untuk logika otomatis
            $table->timestamp('ban_until')->nullable()->after('status');
            
            // Menyimpan alasan atau pesan admin yang muncul di modal
            $table->text('admin_note')->nullable()->after('ban_until');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['ban_until', 'admin_note']);
        });
    }
};
