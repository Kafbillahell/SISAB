<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jadwal;
use App\Models\Rombel;
use App\Models\Mapel;
use App\Models\Guru;
use App\Models\Sesi;

class JadwalSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil Semua Data Master
        $rombels = Rombel::all();
        $mapels = Mapel::all();
        $gurus = Guru::all();
        $sesis = Sesi::where('is_istirahat', false)->orderBy('urutan')->get();

        // Validasi ketersediaan data master
        if ($rombels->isEmpty() || $mapels->isEmpty() || $gurus->isEmpty() || $sesis->isEmpty()) {
            $this->command->error("Gagal Seeding: Pastikan data Rombel, Mapel, Guru, dan Sesi sudah terisi!");
            return;
        }

        $this->command->info("Memulai pengisian jadwal untuk " . $rombels->count() . " rombel...");

        $hariDaftar = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        // 2. Loop Setiap Rombel (10.1 - 10.5 dan 11.1 - 11.7)
        foreach ($rombels as $rombel) {
            foreach ($hariDaftar as $hari) {
                foreach ($sesis as $sesi) {
                    
                    // --- LOGIKA KHUSUS JUMAT ---
                    // Membatasi KBM hari Jumat hanya sampai jam 11:00
                    if ($hari === 'Jumat' && $sesi->jam_mulai >= '11:00:00') {
                        break; 
                    }

                    // Ambil mapel & guru secara acak untuk simulasi
                    $mapel = $mapels->random();
                    $guru = $gurus->random();

                    Jadwal::create([
                        'rombel_id'   => $rombel->id,
                        'mapel_id'    => $mapel->id,
                        'guru_id'     => $guru->id,
                        'sesi_id'     => $sesi->id,
                        'hari'        => $hari,
                        'jam_mulai'   => $sesi->jam_mulai,
                        'jam_selesai' => $sesi->jam_selesai,
                    ]);
                }
            }
            $this->command->info("Jadwal untuk {$rombel->nama_rombel} selesai dibuat.");
        }

        $this->command->info("Berhasil! Seluruh rombel telah memiliki jadwal.");
    }
}