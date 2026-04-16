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
        Schema::table('vouchers', function (Blueprint $table) {
            // Tipe penggunaan voucher: 'anytime' atau 'after_lesson'
            $table->enum('usage_type', ['anytime', 'after_lesson'])->default('anytime')->after('description')->comment('Kapan voucher bisa digunakan');
            
            // Untuk 'after_lesson' - berapa menit setelah pelajaran selesai
            $table->integer('valid_minutes')->nullable()->after('usage_type')->comment('Berapa menit setelah jam pelajaran voucher valid (untuk usage_type=after_lesson)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn(['usage_type', 'valid_minutes']);
        });
    }
};
