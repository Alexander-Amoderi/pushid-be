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
            // Menambahkan kolom status untuk fitur toggle Aktif/Tidak Aktif
            $table->string('status')->default('active')->after('link'); 
            
            // Opsional: Menambahkan softDeletes jika Anda ingin admin bisa menghapus 
            // tapi data tetap ada di database (log record)
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lobbies', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropSoftDeletes();
        });
    }
};
