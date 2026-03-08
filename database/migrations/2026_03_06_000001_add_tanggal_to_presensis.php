<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('presensis', function (Blueprint $table) {
            $table->date('tanggal')->nullable()->after('waktu_scan');
        });

        // Populate tanggal from waktu_scan
        DB::statement("UPDATE presensis SET tanggal = DATE(waktu_scan) WHERE tanggal IS NULL");

        // Remove duplicates keeping the earliest id for each siswa/jadwal/tanggal
        DB::statement(<<<'SQL'
            DELETE p1 FROM presensis p1
            INNER JOIN presensis p2
            ON p1.siswa_id = p2.siswa_id
            AND p1.jadwal_id = p2.jadwal_id
            AND p1.tanggal = p2.tanggal
            AND p1.id > p2.id;
        SQL
        );

        // Make tanggal NOT NULL and add unique index
        Schema::table('presensis', function (Blueprint $table) {
            $table->date('tanggal')->nullable(false)->change();
            $table->unique(['siswa_id', 'jadwal_id', 'tanggal'], 'presensi_unique_s_j_t');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('presensis', function (Blueprint $table) {
            $table->dropUnique('presensi_unique_s_j_t');
            $table->dropColumn('tanggal');
        });
    }
};
