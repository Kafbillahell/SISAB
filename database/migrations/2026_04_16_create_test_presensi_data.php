<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ambil siswa pertama untuk test
        $siswa = DB::table('siswas')->first();
        if (!$siswa) return;

        // Ambil jadwal
        $jadwal1 = DB::table('jadwals')->find(1); // Bimbingan Konseling
        $jadwal2 = DB::table('jadwals')->find(2); // Bahasa Inggris

        if (!$jadwal1 || !$jadwal2) return;

        // Hapus presensi lama dulu jika ada
        DB::table('presensis')->where('siswa_id', $siswa->id)->whereIn('tanggal', ['2026-04-17', '2026-04-18', '2026-04-19'])->delete();

        // Insert test presensi - Tepat waktu
        DB::table('presensis')->insert([
            'siswa_id' => $siswa->id,
            'jadwal_id' => $jadwal2->id,
            'waktu_scan' => '2026-04-17 07:11:30',
            'tanggal' => '2026-04-17',
            'keterangan' => 'Hadir',
            'latitude' => -7.0,
            'longitude' => 110.0,
            'points' => 10, // Tepat waktu
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert test presensi - Terlambat
        DB::table('presensis')->insert([
            'siswa_id' => $siswa->id,
            'jadwal_id' => $jadwal1->id,
            'waktu_scan' => '2026-04-18 07:45:00',
            'tanggal' => '2026-04-18',
            'keterangan' => 'Hadir',
            'latitude' => -7.0,
            'longitude' => 110.0,
            'points' => -5, // Terlambat
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert presensi tepat waktu ketiga
        DB::table('presensis')->insert([
            'siswa_id' => $siswa->id,
            'jadwal_id' => $jadwal2->id,
            'waktu_scan' => '2026-04-19 07:12:00',
            'tanggal' => '2026-04-19',
            'keterangan' => 'Hadir',
            'latitude' => -7.0,
            'longitude' => 110.0,
            'points' => 10, // Tepat waktu
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus test data
        $siswa = DB::table('siswas')->skip(1)->first();
        if ($siswa) {
            DB::table('presensis')
                ->where('siswa_id', $siswa->id)
                ->whereIn('tanggal', ['2026-04-17', '2026-04-18', '2026-04-19'])
                ->delete();
        }
    }
};
