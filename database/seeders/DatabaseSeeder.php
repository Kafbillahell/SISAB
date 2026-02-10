<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 1. Buat User Admin Default
        User::updateOrCreate(
            ['email' => 'admin@smkn1cianjur.sch.id'],
            [
                'name' => 'Administrator Presensi',
                'password' => bcrypt('password'),
                'role' => 'admin', // Pastikan kolom role tersedia
            ]
        );

        // 2. Jalankan Seeder dengan urutan yang logis
        $this->call([
            TahunAjaranSeeder::class, // Dasar waktu
            JurusanSeeder::class,     // Dasar jurusan
            KelasSeeder::class,       // Dasar tingkat (X, XI, XII)
            SesiSeeder::class,        // Waktu KBM (Jam 1, 2, dst)
            MapelSeeder::class,       // Daftar Mata Pelajaran
            GuruSeeder::class,        // Ambil dari API ZieLabs (Membuat User & Guru)
            RombelSeeder::class,      // Ambil dari API ZieLabs (Menghubungkan Kelas ke Jurusan)
            SiswaSeeder::class,       // Ambil dari API ZieLabs
            AnggotaRombelSeeder::class, // Menempatkan Siswa ke Rombel
            JadwalSeeder::class,      // Terakhir: Menghubungkan Guru, Mapel, Rombel, dan Sesi
        ]);
    }
}