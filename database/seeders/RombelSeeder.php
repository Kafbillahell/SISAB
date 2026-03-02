<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rombel;
use App\Models\Kelas;
use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\TahunAjaran;

class RombelSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil Tahun Ajaran Aktif
        $ta = TahunAjaran::where('is_active', true)->first();

        if (!$ta) {
            return;
        }

        // Ambil semua kelas
        $kelas = Kelas::with('jurusan')->get();

        // Ambil semua guru untuk dibagi sebagai wali kelas
        $gurus = Guru::all();
        $guruIndex = 0;

        foreach ($kelas as $k) {
            // Cycle melalui guru-guru untuk menjadi wali kelas
            $guru = $gurus->count() > 0 ? $gurus[$guruIndex % $gurus->count()] : null;
            $guruIndex++;

            Rombel::create([
                'nama_rombel'     => $k->nama_kelas,
                'jurusan_id'      => $k->jurusan_id,
                'kelas_id'        => $k->id,
                'guru_id'         => $guru ? $guru->id : null,
                'tahun_ajaran_id' => $ta->id,
            ]);
        }
    }
}