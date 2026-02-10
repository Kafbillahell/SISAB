<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AnggotaRombel;
use App\Models\Rombel;
use App\Models\Siswa;

class AnggotaRombelSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil ID Rombel XII PPLG-RPL 1
        $rombel = Rombel::where('nama_rombel', 'XII PPLG-RPL 1')->first();

        // 2. Ambil semua siswa yang sudah dibuat di SiswaSeeder
        $semuaSiswa = Siswa::all();

        if ($rombel && $semuaSiswa->count() > 0) {
            foreach ($semuaSiswa as $siswa) {
                AnggotaRombel::create([
                    'rombel_id' => $rombel->id,
                    'siswa_id'  => $siswa->id,
                ]);
            }
        }
    }
}