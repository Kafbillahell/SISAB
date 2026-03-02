<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Guru; // Pastikan import Model Guru
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // 2. Daftar Guru (XII PPLG-RPL 1)
        $dataGuru = [
            ['nama' => 'Yayat Ruhiyat, S.ST', 'jk' => 'L'],
            ['nama' => 'Ati Melani, M.Pd.', 'jk' => 'P'],
            ['nama' => 'Yaqub Hadi Permana, S.T.', 'jk' => 'L'],
            ['nama' => 'Sarah Siti Sumaerah, S.T.', 'jk' => 'P'],
            ['nama' => 'A. Luddie Tri S., S.T.', 'jk' => 'L'],
            ['nama' => 'Fajar M. Sukmawijaya, M.Kom.', 'jk' => 'L'],
            ['nama' => 'Tini Murtiningsih, S.Pd.', 'jk' => 'P'],
            ['nama' => 'Ernis Hendrawati, M.Kom.', 'jk' => 'P'],
            ['nama' => 'Pradita Surya Arianti', 'jk' => 'P'],
            ['nama' => 'Rubaetul Adawiyah, S.Pd.', 'jk' => 'P'],
            ['nama' => 'Hinda Gumiarti, M.Pd.', 'jk' => 'P'],
            ['nama' => 'Dikdik Juanda, S.Pd.I., M.M.Pd.', 'jk' => 'L'],
        ];

        foreach ($dataGuru as $g) {
            // Ambil nama depan saja untuk email agar simple
            $nameOnly = explode(',', $g['nama'])[0]; 
            $email = Str::slug($nameOnly) . '@gmail.com'; // Pakai @gmail.com sesuai permintaan awal

            // Buat User
            $user = User::create([
                'name' => $g['nama'],
                'email' => $email,
                'password' => Hash::make('password123'),
                'role' => 'guru',
            ]);

            // Buat Data Guru & Hubungkan
            $guru = Guru::create([
                'user_id' => $user->id,
                'nama_guru' => $g['nama'],
                'jenis_kelamin' => $g['jk'],
            ]);

            // Update user agar punya guru_id (Penting untuk logika scanner!)
            $user->update(['guru_id' => $guru->id]);
        }

        // 3. Akun Siswa
        User::create([
            'name' => 'Andi Siswa',
            'email' => 'siswa@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'siswa',
        ]);
    }
}