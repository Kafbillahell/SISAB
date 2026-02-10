<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jurusan; // Pastikan model Jurusan di-import

class JurusanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'nama_jurusan' => 'Pengembangan Perangkat Lunak dan Gim',
                'kode_jurusan' => 'PPLG-RPL'
            ],
            // Anda bisa menambahkan jurusan lain dari SMKN 1 Cianjur di sini
        ];

        foreach ($data as $item) {
            Jurusan::create($item);
        }
    }
}