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
            // Drop the old foreign key
            $table->dropForeign(['jadwal_id']);
            
            // Add the new foreign key with cascade delete
            $table->foreign('jadwal_id')
                ->references('id')
                ->on('jadwals')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presensis', function (Blueprint $table) {
            // Drop the cascade foreign key
            $table->dropForeign(['jadwal_id']);
            
            // Restore the original foreign key without cascade
            $table->foreign('jadwal_id')
                ->references('id')
                ->on('jadwals');
        });
    }
};
