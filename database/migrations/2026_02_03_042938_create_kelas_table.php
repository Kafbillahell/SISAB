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
    Schema::create('kelas', function (Blueprint $table) {
        $table->id();
        $table->string('tingkat'); // Contoh: 10, 11, 12
        $table->string('nama_kelas'); // Contoh: RPL 1, TKJ 2
        // Tambahkan baris di bawah ini:
        $table->unsignedBigInteger('jurusan_id'); 
        $table->timestamps();

        // Tambahkan relasi foreign key
        $table->foreign('jurusan_id')->references('id')->on('jurusans')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
