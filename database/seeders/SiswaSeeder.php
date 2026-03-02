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
        
        // Daftar nama siswa Indonesia
        $namaDepan = [
            'Ade', 'Ahmad', 'Amir', 'Bagus', 'Budi', 'Citra', 'Dani', 'Deni', 'Dewi', 'Dimas',
            'Eka', 'Elsa', 'Endra', 'Endy', 'Erni', 'Fadil', 'Fajar', 'Farida', 'Faris', 'Fauzi',
            'Feni', 'Fikri', 'Filsa', 'Firman', 'Fitra', 'Gisela', 'Gita', 'Giyanto', 'Gumilang', 'Guntur',
            'Hadi', 'Hafid', 'Hakim', 'Halim', 'Hamid', 'Hana', 'Hani', 'Hanif', 'Hanurah', 'Hapiz',
            'Hasan', 'Hasna', 'Hasyim', 'Hatim', 'Hayati', 'Heddy', 'Hendra', 'Hendri', 'Hendriana', 'Hendro'
        ];

        $namaBelakang = [
            'Abdullah', 'Ababila', 'Abidin', 'Abimanyu', 'Abiyoso', 'Abiyudha', 'Abiyuwono', 'Abiyuwono', 'Abiyuwono', 'Abubakar',
            'Adiputra', 'Adisaputra', 'Adisurya', 'Adiyanto', 'Aditya', 'Adityawan', 'Adiyatno', 'Adiyuwin', 'Ahmad', 'Ahmadi',
            'Ahmadini', 'Ahmad Sodikin', 'Ahmadsyah', 'Ahnaf', 'Ahrend', 'Aidin', 'Aiditya', 'Aifan', 'Aifandi', 'Aihas',
            'Aihan', 'Aihari', 'Aiharno', 'Aihasani', 'Aihasan', 'Aihasna', 'Aihasni', 'Aihasno', 'Aihasnu', 'Aihasny',
            'Aihasro', 'Aihasru', 'Aihasso', 'Aihassu', 'Aihasya', 'Aihaszn', 'Aihatan', 'Aihaven', 'Aihawan', 'Aihayah'
        ];

        $jkList = ['L', 'P'];

        // Ambil semua rombel yang sudah dibuat
        $rombels = Rombel::all();
        $nisn = 10000000; // Starting NISN

        foreach ($rombels as $rombel) {
            // Buat 30-35 siswa per rombel
            $jumlahSiswa = rand(30, 35);

            for ($i = 0; $i < $jumlahSiswa; $i++) {
                $nama = $namaDepan[array_rand($namaDepan)] . ' ' . $namaBelakang[array_rand($namaBelakang)];
                $jk = $jkList[array_rand($jkList)];
                $nisn++;

                // Buat Akun User untuk Login Siswa
                $email = strtolower(str_replace(' ', '', $nama)) . $nisn . '@student.sch.id';
                
                $user = User::updateOrCreate(
                    ['email' => $email],
                    [
                        'name' => $nama,
                        'password' => Hash::make('password123'),
                        'role' => 'siswa',
                    ]
                );

                // Simpan ke Tabel Siswas
                $siswa = Siswa::updateOrCreate(
                    ['nisn' => (string)$nisn],
                    [
                        'user_id' => $user->id,
                        'nama_siswa' => $nama,
                        'jenis_kelamin' => $jk,
                    ]
                );

                // Hubungkan ke Rombel melalui tabel AnggotaRombels
                AnggotaRombel::updateOrCreate(
                    [
                        'siswa_id' => $siswa->id,
                        'rombel_id' => $rombel->id
                    ]
                );
            }
        }

        $this->command->info("Seeding siswa berhasil!");
    }
}