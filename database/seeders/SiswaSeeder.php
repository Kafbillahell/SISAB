<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;
use App\Models\User;
use App\Models\Rombel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        $response = Http::get('https://zieapi.zielabs.id/api/getsiswa?tahun=2025');

        if ($response->successful()) {
            $dataSiswa = $response->json()['data'] ?? $response->json();

            foreach ($dataSiswa as $s) {
                // Buat User Siswa
                $email = strtolower(str_replace(' ', '', $s['nama'])) . $s['nisn'] . '@student.sch.id';

                $user = User::updateOrCreate(
                    ['email' => $email],
                    [
                        'name' => $s['nama'],
                        'password' => Hash::make('siswa123'),
                        'role' => 'siswa',
                    ]
                );

                // Cari Rombel_id berdasarkan nama kelas dari API
                $rombel = Rombel::where('nama_rombel', $s['nama_kelas'])->first();

                Siswa::updateOrCreate(
                    ['nisn' => $s['nisn']],
                    [
                        'user_id' => $user->id,
                        'nama_siswa' => $s['nama'],
                        'jenis_kelamin' => $s['jk'] ?? 'L',
                        'rombel_id' => $rombel ? $rombel->id : null,
                    ]
                );
            }
            $this->command->info("Siswa berhasil diimpor.");
        }
    }
}