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
    Schema::table('rombels', function (Blueprint $table) {
        // Tambahkan kolom jurusan_id setelah kolom id
        $table->unsignedBigInteger('jurusan_id')->nullable()->after('id');
        
        // Opsional: Tambahkan foreign key agar data konsisten
        $table->foreign('jurusan_id')->references('id')->on('jurusans')->onDelete('cascade');
    });
}

public function down(): void
{
    Schema::table('rombels', function (Blueprint $table) {
        $table->dropForeign(['jurusan_id']);
        $table->dropColumn('jurusan_id');
    });
}
};
