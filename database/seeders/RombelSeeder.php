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
        // 1. Ambil data Jurusan PPLG-RPL [cite: 1]
        $jurusan = Jurusan::where('kode_jurusan', 'PPLG-RPL')->first();

        // 2. Ambil data Kelas XII [cite: 1]
        $kelas = Kelas::where('tingkat', '12')->first();

        // 3. Ambil data Wali Kelas (Contoh: Yayat Ruhiyat) [cite: 1]
        $guru = Guru::where('nama_guru', 'LIKE', '%Yayat Ruhiyat%')->first();

        // 4. Ambil Tahun Ajaran Aktif (2025/2026 Genap) [cite: 2]
        $ta = TahunAjaran::where('is_active', true)->first();

        if ($jurusan && $kelas && $ta) {
            Rombel::updateOrCreate(
                ['nama_rombel' => 'XII PPLG-RPL 1'], // [cite: 3]
                [
                    'jurusan_id'      => $jurusan->id, // Kolom baru Anda
                    'kelas_id'        => $kelas->id,
                    'guru_id'         => $guru ? $guru->id : null,
                    'tahun_ajaran_id' => $ta->id,
                ]
            );
        }
    }
}