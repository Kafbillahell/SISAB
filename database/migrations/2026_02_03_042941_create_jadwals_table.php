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
    Schema::create('jadwals', function (Blueprint $table) {
        $table->id();
        $table->foreignId('rombel_id')->constrained('rombels');
        $table->foreignId('mapel_id')->constrained('mapels');
        $table->foreignId('guru_id')->constrained('gurus');
        
        // TAMBAHKAN INI: Menghubungkan ke tabel sesis
        $table->foreignId('sesi_id')->constrained('sesis'); 

        $table->enum('hari', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']);
        $table->time('jam_mulai');
        $table->time('jam_selesai');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwals');
    }
};
