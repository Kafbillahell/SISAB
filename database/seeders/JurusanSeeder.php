<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jurusan;

class JurusanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'nama_jurusan' => 'Pengembangan Perangkat Lunak dan Gim',
                'kode_jurusan' => 'PPLG'
            ],
            [
                'nama_jurusan' => 'Teknik Komputer dan Jaringan',
                'kode_jurusan' => 'TKJ'
            ],
            [
                'nama_jurusan' => 'Teknik Kendaraan Ringan',
                'kode_jurusan' => 'TKR'
            ],
            [
                'nama_jurusan' => 'Bisnis dan Manajemen Perkantoran',
                'kode_jurusan' => 'BPMP'
            ],
            [
                'nama_jurusan' => 'Desain Grafis Multimedia',
                'kode_jurusan' => 'DGM'
            ],
        ];

        foreach ($data as $item) {
            Jurusan::create($item);
        }
    }
}