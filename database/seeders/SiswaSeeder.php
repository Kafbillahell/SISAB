<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        // Contoh data siswa untuk kelas XII PPLG-RPL 1
        $dataSiswa = [
            ['nama' => 'Ahmad Fikri', 'jk' => 'L', 'nisn' => '0012345678'],
            ['nama' => 'Budi Santoso', 'jk' => 'L', 'nisn' => '0012345679'],
            ['nama' => 'Citra Lestari', 'jk' => 'P', 'nisn' => '0012345680'],
            ['nama' => 'Dewi Safitri', 'jk' => 'P', 'nisn' => '0012345681'],
            ['nama' => 'Eka Pratama', 'jk' => 'L', 'nisn' => '0012345682'],
        ];

        foreach ($dataSiswa as $s) {
            // 1. Buat User Account untuk Siswa
            $user = User::create([
                'name' => $s['nama'],
                'email' => strtolower(str_replace(' ', '', $s['nama'])) . '@student.sch.id',
                'password' => Hash::make('siswa123'),
                'role' => 'siswa',
            ]);

            // 2. Hubungkan ke tabel siswas
            Siswa::create([
                'user_id' => $user->id,
                'nisn' => $s['nisn'],
                'nama_siswa' => $s['nama'],
                'jenis_kelamin' => $s['jk'],
                'face_embedding' => null, // Akan diisi saat registrasi wajah di scanner
                'foto' => null,
            ]);
        }
    }
}