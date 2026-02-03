<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Akun Admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // 2. Akun Guru
        User::create([
            'name' => 'Budi Guru',
            'email' => 'guru@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'guru',
        ]);

        // 3. Akun Siswa
        User::create([
            'name' => 'Andi Siswa',
            'email' => 'siswa@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'siswa',
            'face_id' => 'dummy_face_data_001' // Contoh pengisian face_id
        ]);
    }
}