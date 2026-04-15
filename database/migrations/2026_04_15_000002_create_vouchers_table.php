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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nama voucher');
            $table->text('description')->nullable()->comment('Deskripsi voucher');
            $table->integer('point_cost')->comment('Biaya poin untuk menukar voucher');
            $table->integer('quantity')->comment('Jumlah voucher tersedia');
            $table->integer('used')->default(0)->comment('Jumlah voucher yang sudah digunakan');
            $table->timestamp('valid_until')->nullable()->comment('Tanggal kedaluwarsa voucher');
            $table->boolean('is_active')->default(true)->comment('Voucher aktif atau tidak');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
