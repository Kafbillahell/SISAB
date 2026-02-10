<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mapel;

class MapelSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nama_mapel' => 'Konsentrasi Keahlian RPL', 'kode_mapel' => 'KRPL'], // 
            ['nama_mapel' => 'Pendidikan Pancasila', 'kode_mapel' => 'PP'], // 
            ['nama_mapel' => 'Bimbingan Konseling', 'kode_mapel' => 'BK'], // 
            ['nama_mapel' => 'Matematika', 'kode_mapel' => 'MTK'], // 
            ['nama_mapel' => 'Bahasa Inggris', 'kode_mapel' => 'BING'], // 
            ['nama_mapel' => 'Bahasa Indonesia', 'kode_mapel' => 'BIND'], // 
            ['nama_mapel' => 'Pendidikan Agama & Budi Pekerti', 'kode_mapel' => 'PAB'], // 
            ['nama_mapel' => 'Mata Pelajaran Pilihan PPLG', 'kode_mapel' => 'P-PPLG'], // 
            ['nama_mapel' => 'Mulok Bahasa Jepang', 'kode_mapel' => 'BJPN'], // 
            ['nama_mapel' => 'Kreativitas dan Kewirausahaan', 'kode_mapel' => 'KIK'], // 
        ];

        foreach ($data as $item) {
            Mapel::updateOrCreate(['kode_mapel' => $item['kode_mapel']], $item);
        }
    }
}