<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;
use App\Models\User;
use App\Models\Rombel;
use App\Models\AnggotaRombel;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class SiswaSeeder extends Seeder
{
    public function run(): void
{
    $faker = Faker::create('id_ID');
    
    $namaDepan = ['Ade', 'Ahmad', 'Amir', 'Bagus', 'Budi', 'Citra', 'Dani', 'Deni', 'Dewi', 'Dimas']; // ... dst
    $namaBelakang = ['Abdullah', 'Ababila', 'Abidin', 'Abimanyu', 'Abiyoso']; // ... dst
    $jkList = ['L', 'P'];

    $rombels = Rombel::all();
    $nisn = 10000000;

    // OPTIMASI 1: Hash password di luar loop, cukup sekali saja!
    $password = Hash::make('password123');

    foreach ($rombels as $rombel) {
        $jumlahSiswa = rand(30, 35);
        
        // Siapkan array untuk menampung data (Bulk Insert)
        $usersData = [];
        
        for ($i = 0; $i < $jumlahSiswa; $i++) {
            $nama = $namaDepan[array_rand($namaDepan)] . ' ' . $namaBelakang[array_rand($namaBelakang)];
            $jk = $jkList[array_rand($jkList)];
            $nisn++;
            $email = strtolower(str_replace(' ', '', $nama)) . $nisn . '@student.sch.id';

            // OPTIMASI 2: Buat User dulu (per rombel saja biar tidak terlalu berat di memori)
            $user = User::create([
                'name' => $nama,
                'email' => $email,
                'password' => $password, // Pakai variabel yang sudah di-hash tadi
                'role' => 'siswa',
            ]);

            // OPTIMASI 3: Buat Siswa
            $siswa = Siswa::create([
                'user_id' => $user->id,
                'nisn' => (string)$nisn,
                'nama_siswa' => $nama,
                'jenis_kelamin' => $jk,
            ]);

            // OPTIMASI 4: Hubungkan ke AnggotaRombel
            AnggotaRombel::create([
                'siswa_id' => $siswa->id,
                'rombel_id' => $rombel->id
            ]);
        }
    }

    $this->command->info("Seeding siswa berhasil dengan lebih cepat!");
}
}