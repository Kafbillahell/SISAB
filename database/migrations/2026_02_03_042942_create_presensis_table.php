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
    Schema::create('presensis', function (Blueprint $table) {
        $table->id();
        $table->foreignId('jadwal_id')->constrained('jadwals');
        $table->foreignId('siswa_id')->constrained('siswas');
        $table->dateTime('waktu_scan');
        $table->enum('status', ['Hadir', 'Izin', 'Sakit', 'Alpa', 'Terlambat']);
        $table->string('keterangan')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensis');
    }
};
