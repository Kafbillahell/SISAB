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
        Schema::create('penilaian_sikaps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->foreignId('penilai_id')->constrained('users')->onDelete('cascade');
            $table->integer('tanggung_jawab')->default(0)->comment('1-5');
            $table->integer('kejujuran')->default(0)->comment('1-5');
            $table->integer('sopan_santun')->default(0)->comment('1-5');
            $table->integer('kemandirian')->default(0)->comment('1-5');
            $table->integer('kerja_sama')->default(0)->comment('1-5');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian_sikaps');
    }
};
