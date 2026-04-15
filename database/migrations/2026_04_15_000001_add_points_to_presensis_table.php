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
        Schema::table('presensis', function (Blueprint $table) {
            // Hanya tambah kolom jika belum ada
            if (!Schema::hasColumn('presensis', 'points')) {
                $table->integer('points')->default(0)->after('keterangan')->comment('Poin untuk absensi ini');
            }
            
            if (!Schema::hasColumn('presensis', 'used_voucher')) {
                $table->boolean('used_voucher')->default(false)->after('points')->comment('Apakah menggunakan voucher');
            }
            
            if (!Schema::hasColumn('presensis', 'student_voucher_id')) {
                $table->unsignedBigInteger('student_voucher_id')->nullable()->after('used_voucher')->comment('ID Voucher yang digunakan');
            }
            
            // Foreign key hanya jika kolom baru saja dibuat
            if (!Schema::hasColumn('presensis', 'student_voucher_id') || 
                !Schema::hasColumn('presensis', 'student_voucher_id')) {
                // Skip foreign key, akan ditambah di migration terpisah jika perlu
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presensis', function (Blueprint $table) {
            if (Schema::hasColumn('presensis', 'student_voucher_id')) {
                $table->dropForeign(['student_voucher_id']);
                $table->dropColumn('student_voucher_id');
            }
            if (Schema::hasColumn('presensis', 'used_voucher')) {
                $table->dropColumn('used_voucher');
            }
            if (Schema::hasColumn('presensis', 'points')) {
                $table->dropColumn('points');
            }
        });
    }
};

