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
        Schema::create('student_vouchers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('siswa_id');
            $table->unsignedBigInteger('voucher_id');
            $table->timestamp('redeemed_at')->nullable()->comment('Kapan voucher ditukar');
            $table->timestamp('used_at')->nullable()->comment('Kapan voucher digunakan');
            $table->boolean('is_used')->default(false)->comment('Apakah voucher sudah digunakan');
            $table->timestamps();
            
            $table->foreign('siswa_id')->references('id')->on('siswas')->onDelete('cascade');
            $table->foreign('voucher_id')->references('id')->on('vouchers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_vouchers');
    }
};
