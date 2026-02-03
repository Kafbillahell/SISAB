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
    Schema::create('rombels', function (Blueprint $table) {
        $table->id();
        $table->foreignId('kelas_id')->constrained('kelas');
        $table->string('nama_rombel'); // Contoh: X RPL 1
        $table->foreignId('guru_id')->nullable()->constrained('gurus'); // Wali Kelas
        $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rombels');
    }
};
