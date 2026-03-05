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
        // Track guru assignments per hari+sesi to avoid double-booking
        $assigned = [];

        foreach ($rombels as $rombel) {
            foreach ($hariDaftar as $hari) {
                foreach ($sesis as $sesi) {

                    // --- LOGIKA KHUSUS JUMAT ---
                    // Membatasi KBM hari Jumat hanya sampai jam 11:00
                    if ($hari === 'Jumat' && $sesi->jam_mulai >= '11:00:00') {
                        break; 
                    }

                    // Pastikan ada guru yang belum ditugaskan pada hari+sesi ini
                    $key = $hari . '_' . $sesi->id;
                    $usedGuruIds = $assigned[$key] ?? [];

                    $availableGurus = $gurus->filter(function($g) use ($usedGuruIds) {
                        return ! in_array($g->id, $usedGuruIds);
                    })->values();

                    if ($availableGurus->isEmpty()) {
                        // Tidak ada guru tersisa untuk slot ini — log dan lewati
                        $this->command->warn("Tidak ada guru tersedia untuk hari {$hari} sesi {$sesi->id} (rombel {$rombel->nama_rombel}), melewatkan slot.");
                        continue;
                    }

                    // Pilih guru yang tersedia secara acak dan tandai sebagai digunakan untuk hari+sesi ini
                    $guru = $availableGurus->random();
                    $assigned[$key][] = $guru->id;

                    // Pilih mapel secara acak (boleh sama antar rombel)
                    $mapel = $mapels->random();

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