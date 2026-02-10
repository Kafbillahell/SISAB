<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TahunAjaran; // Pastikan model ini sudah ada

class TahunAjaranSeeder extends Seeder
{
    public function run(): void
    {
        TahunAjaran::create([
            'tahun'     => '2025/2026', // Sesuai dokumen 
            'semester'  => 'Genap',     // Sesuai dokumen 
            'is_active' => true,
        ]);
    }
}