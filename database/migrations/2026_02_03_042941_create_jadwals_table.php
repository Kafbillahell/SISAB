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
        $table->foreignId('rombel_id')->constrained('rombels')->onDelete('cascade');
        $table->foreignId('mapel_id')->constrained('mapels')->onDelete('cascade');
        
        // Tambahkan ->onDelete('cascade') agar saat Guru dihapus, Jadwalnya ikut terhapus
        $table->foreignId('guru_id')->constrained('gurus')->onDelete('cascade');
        
        // Tambahkan ->onDelete('cascade') untuk Sesi juga
        $table->foreignId('sesi_id')->constrained('sesis')->onDelete('cascade'); 

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
