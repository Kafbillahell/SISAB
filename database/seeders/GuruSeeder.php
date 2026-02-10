<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class GuruSeeder extends Seeder
{
    public function run(): void
    {
        // Daftar Guru berdasarkan jadwal XII PPLG-RPL 1 
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
            // 1. Buat User Account terlebih dahulu
            $user = User::create([
                'name' => $g['nama'],
                'email' => strtolower(str_replace([' ', ',', '.'], '', $g['nama'])) . '@smkn1cianjur.sch.id',
                'password' => Hash::make('password123'),
                'role' => 'guru', // Pastikan kolom role ada di migration users Anda
            ]);

            // 2. Hubungkan ke tabel gurus
            Guru::create([
                'user_id' => $user->id,
                'nip' => null, // Bisa diisi manual nanti
                'nama_guru' => $g['nama'],
                'jenis_kelamin' => $g['jk'],
            ]);
        }
    }
}