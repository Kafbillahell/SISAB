<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas;
use App\Models\Jurusan;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil ID dari jurusan PPLG-RPL yang sudah kita buat sebelumnya
        $jurusan = Jurusan::where('kode_jurusan', 'PPLG-RPL')->first();

        if ($jurusan) {
            Kelas::create([
                'tingkat'    => '12',             // Sesuai dokumen "XII" 
                'nama_kelas' => 'PPLG-RPL 1',     // Sesuai dokumen 
                'jurusan_id' => $jurusan->id,     // Relasi ke tabel jurusans
            ]);
        }
    }
}