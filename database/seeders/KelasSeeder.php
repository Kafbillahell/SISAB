<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas;
use App\Models\Jurusan;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua jurusan yang sudah dibuat
        $jurusans = Jurusan::all();

        // Data untuk kelas per tingkat
        $tingkats = ['10', '11', '12'];
        $jumlahKelas = 2; // Jumlah kelas per tingkat per jurusan

        foreach ($jurusans as $jurusan) {
            foreach ($tingkats as $tingkat) {
                for ($i = 1; $i <= $jumlahKelas; $i++) {
                    Kelas::create([
                        'tingkat'    => $tingkat,
                        'nama_kelas' => $jurusan->kode_jurusan . ' ' . $tingkat . '-' . $i,
                        'jurusan_id' => $jurusan->id,
                    ]);
                }
            }
        }
    }
}