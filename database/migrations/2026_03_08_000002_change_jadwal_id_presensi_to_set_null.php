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
            // Drop old cascade foreign key
            $table->dropForeign(['jadwal_id']);
            
            // Make jadwal_id nullable first
            $table->unsignedBigInteger('jadwal_id')->nullable()->change();
            
            // Add new foreign key with SET NULL (data presensi tetap ada, jadwal_id jadi null)
            $table->foreign('jadwal_id')
                ->references('id')
                ->on('jadwals')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presensis', function (Blueprint $table) {
            $table->dropForeign(['jadwal_id']);
            
            // Restore original state
            $table->unsignedBigInteger('jadwal_id')->nullable(false)->change();
            
            $table->foreign('jadwal_id')
                ->references('id')
                ->on('jadwals');
        });
    }
};
