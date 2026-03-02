<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat User Admin Default (Opsional)
        User::factory()->create([
            'name' => 'Administrator Presensi',
            'email' => 'admin@smkn1cianjur.sch.id',
            'password' => bcrypt('password'), // Pastikan password aman
        ]);

        // 2. Jalankan Seeder sesuai urutan ketergantungan (Foreign Key)
        $this->call([
        TahunAjaranSeeder::class,
        JurusanSeeder::class,
        KelasSeeder::class,
        SesiSeeder::class,   // Harus lebih dulu
        MapelSeeder::class,
        GuruSeeder::class,
        RombelSeeder::class,
        SiswaSeeder::class,
        AnggotaRombelSeeder::class,
        JadwalSeeder::class, // Harus belakangan
    ]);
    }
}