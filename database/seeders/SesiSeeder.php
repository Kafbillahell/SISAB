<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sesi;

class SesiSeeder extends Seeder
{
    public function run(): void
{
    // Cukup satu daftar sesi untuk semua hari
    $data = [
        ['urutan' => 1,  'nama_sesi' => 'Kegiatan Pagi', 'jam_mulai' => '06:30', 'jam_selesai' => '07:10', 'is_istirahat' => false],
        ['urutan' => 2,  'nama_sesi' => 'Jam Ke-1',      'jam_mulai' => '07:10', 'jam_selesai' => '07:50', 'is_istirahat' => false],
        ['urutan' => 3,  'nama_sesi' => 'Jam Ke-2',      'jam_mulai' => '07:50', 'jam_selesai' => '08:30', 'is_istirahat' => false],
        ['urutan' => 4,  'nama_sesi' => 'Istirahat 1',   'jam_mulai' => '08:30', 'jam_selesai' => '09:10', 'is_istirahat' => true],
        ['urutan' => 5,  'nama_sesi' => 'Jam Ke-3',      'jam_mulai' => '09:10', 'jam_selesai' => '09:50', 'is_istirahat' => false],
        ['urutan' => 6,  'nama_sesi' => 'Jam Ke-4',      'jam_mulai' => '09:50', 'jam_selesai' => '10:30', 'is_istirahat' => false],
        ['urutan' => 7,  'nama_sesi' => 'Jam Ke-5',      'jam_mulai' => '10:30', 'jam_selesai' => '11:10', 'is_istirahat' => false],
        ['urutan' => 8,  'nama_sesi' => 'Jam Ke-6',      'jam_mulai' => '11:10', 'jam_selesai' => '11:50', 'is_istirahat' => false],
        ['urutan' => 9,  'nama_sesi' => 'Istirahat 2',   'jam_mulai' => '11:50', 'jam_selesai' => '12:30', 'is_istirahat' => true],
        ['urutan' => 10, 'nama_sesi' => 'Jam Ke-7',      'jam_mulai' => '12:30', 'jam_selesai' => '13:10', 'is_istirahat' => false],
        ['urutan' => 11, 'nama_sesi' => 'Jam Ke-8',      'jam_mulai' => '13:10', 'jam_selesai' => '13:50', 'is_istirahat' => false],
        ['urutan' => 12, 'nama_sesi' => 'Jam Ke-9',      'jam_mulai' => '13:50', 'jam_selesai' => '14:30', 'is_istirahat' => false],
        ['urutan' => 13, 'nama_sesi' => 'Jam Ke-10',     'jam_mulai' => '14:30', 'jam_selesai' => '15:10', 'is_istirahat' => false],
        ['urutan' => 14, 'nama_sesi' => 'Jam Ke-11',     'jam_mulai' => '15:10', 'jam_selesai' => '15:50', 'is_istirahat' => false],
    ];

    foreach ($data as $item) Sesi::create($item);
}
    private function seedHariNormal($hari)
    {
        // Tentukan label khusus jam 06:30 sesuai hari
        $labelAwal = match($hari) {
            'Senin'  => 'Upacara',
            'Selasa' => 'Selasa Segar',
            'Rabu'   => 'Cahaya Rabu',
            'Kamis'  => 'Kamis Alami',
            default  => 'Kegiatan Pagi'
        };

        $data = [
            ['hari' => $hari, 'urutan' => 1,  'nama_sesi' => $labelAwal,   'jam_mulai' => '06:30', 'jam_selesai' => '07:10', 'is_istirahat' => false],
            ['hari' => $hari, 'urutan' => 2,  'nama_sesi' => 'Jam Ke-1',   'jam_mulai' => '07:10', 'jam_selesai' => '07:50', 'is_istirahat' => false],
            ['hari' => $hari, 'urutan' => 3,  'nama_sesi' => 'Jam Ke-2',   'jam_mulai' => '07:50', 'jam_selesai' => '08:30', 'is_istirahat' => false],
            ['hari' => $hari, 'urutan' => 4,  'nama_sesi' => 'Istirahat 1','jam_mulai' => '08:30', 'jam_selesai' => '09:10', 'is_istirahat' => true],
            ['hari' => $hari, 'urutan' => 5,  'nama_sesi' => 'Jam Ke-3',   'jam_mulai' => '09:10', 'jam_selesai' => '09:50', 'is_istirahat' => false],
            ['hari' => $hari, 'urutan' => 6,  'nama_sesi' => 'Jam Ke-4',   'jam_mulai' => '09:50', 'jam_selesai' => '10:30', 'is_istirahat' => false],
            ['hari' => $hari, 'urutan' => 7,  'nama_sesi' => 'Jam Ke-5',   'jam_mulai' => '10:30', 'jam_selesai' => '11:10', 'is_istirahat' => false],
            ['hari' => $hari, 'urutan' => 8,  'nama_sesi' => 'Jam Ke-6',   'jam_mulai' => '11:10', 'jam_selesai' => '11:50', 'is_istirahat' => false],
            ['hari' => $hari, 'urutan' => 9,  'nama_sesi' => 'Istirahat 2','jam_mulai' => '11:50', 'jam_selesai' => '12:30', 'is_istirahat' => true],
            ['hari' => $hari, 'urutan' => 10, 'nama_sesi' => 'Jam Ke-7',   'jam_mulai' => '12:30', 'jam_selesai' => '13:10', 'is_istirahat' => false],
            ['hari' => $hari, 'urutan' => 11, 'nama_sesi' => 'Jam Ke-8',   'jam_mulai' => '13:10', 'jam_selesai' => '13:50', 'is_istirahat' => false],
            ['hari' => $hari, 'urutan' => 12, 'nama_sesi' => 'Jam Ke-9',   'jam_mulai' => '13:50', 'jam_selesai' => '14:30', 'is_istirahat' => false],
            ['hari' => $hari, 'urutan' => 13, 'nama_sesi' => 'Jam Ke-10',  'jam_mulai' => '14:30', 'jam_selesai' => '15:10', 'is_istirahat' => false],
            ['hari' => $hari, 'urutan' => 14, 'nama_sesi' => 'Jam Ke-11',  'jam_mulai' => '15:10', 'jam_selesai' => '15:50', 'is_istirahat' => false],
        ];

        foreach ($data as $item) Sesi::create($item);
    }

    private function seedHariJumat()
    {
        $data = [
            ['hari' => 'Jumat', 'urutan' => 1, 'nama_sesi' => 'Kerohanian', 'jam_mulai' => '06:30', 'jam_selesai' => '07:10', 'is_istirahat' => false],
            ['hari' => 'Jumat', 'urutan' => 2, 'nama_sesi' => 'Jam Ke-1',   'jam_mulai' => '07:10', 'jam_selesai' => '07:50', 'is_istirahat' => false],
            ['hari' => 'Jumat', 'urutan' => 3, 'nama_sesi' => 'Jam Ke-2',   'jam_mulai' => '07:50', 'jam_selesai' => '08:30', 'is_istirahat' => false],
            ['hari' => 'Jumat', 'urutan' => 4, 'nama_sesi' => 'Istirahat',  'jam_mulai' => '08:30', 'jam_selesai' => '09:10', 'is_istirahat' => true],
            ['hari' => 'Jumat', 'urutan' => 5, 'nama_sesi' => 'Jam Ke-3',   'jam_mulai' => '09:10', 'jam_selesai' => '09:50', 'is_istirahat' => false],
            ['hari' => 'Jumat', 'urutan' => 6, 'nama_sesi' => 'Jam Ke-4',   'jam_mulai' => '09:50', 'jam_selesai' => '10:30', 'is_istirahat' => false],
            ['hari' => 'Jumat', 'urutan' => 7, 'nama_sesi' => 'Jam Ke-5',   'jam_mulai' => '10:30', 'jam_selesai' => '11:25', 'is_istirahat' => false],
        ];

        foreach ($data as $item) Sesi::create($item);
    }
}