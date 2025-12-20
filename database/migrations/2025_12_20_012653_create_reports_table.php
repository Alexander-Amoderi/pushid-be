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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            // ID User yang mengirim laporan
            $table->foreignId('reporter_id')->constrained('users')->onDelete('cascade');
            // ID Lobby yang dilaporkan
            $table->foreignId('lobby_id')->constrained('lobbies')->onDelete('cascade');
            // Alasan: spam, harassment, inappropriate, scam, other
            $table->string('reason'); 
            // Detail tambahan mengenai pelanggaran
            $table->text('description'); 
            // Status: pending, investigating, resolved, dismissed
            $table->string('status')->default('pending'); 
            // Prioritas: low, medium, high
            $table->string('priority')->default('low');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
