<?php

namespace App\Http\Controllers;

use App\Models\AnggotaRombel;
use App\Models\Rombel;
use App\Models\Siswa;
use Illuminate\Http\Request;

class AnggotaRombelController extends Controller
{
    // Menampilkan halaman daftar anggota rombongan belajar (rombel)
    // Fungsi ini juga menangani filter pencarian anggota berdasarkan rombel yang dipilih
    public function index(Request $request)
{
    $rombels = Rombel::with(['kelas', 'tahunAjaran'])->get();
    $selectedRombel = null;
    $anggotas = [];
    $siswas = [];

    // Ambil semua ID siswa yang sudah terdaftar di rombel MANAPUN
    $siswaTerdaftarIds = AnggotaRombel::pluck('siswa_id');

    if ($request->rombel_id) {
        $selectedRombel = Rombel::findOrFail($request->rombel_id);
        $anggotas = AnggotaRombel::with('siswa')
            ->where('rombel_id', $request->rombel_id)
            ->get();
        
        // Ambil siswa yang ID-nya tidak ada di daftar siswa yang sudah punya rombel
        $siswas = Siswa::whereNotIn('id', $siswaTerdaftarIds)
            ->orderBy('nama_siswa', 'asc')
            ->get();
    }

    return view('anggota_rombel.index', compact('rombels', 'selectedRombel', 'anggotas', 'siswas'));
}

    // Menyimpan data siswa baru ke dalam anggota rombongan belajar (rombel)
    // Memproses form tambah siswa secara massal untuk sebuah rombel
    public function store(Request $request)
{
    $request->validate([
        'rombel_id' => 'required|exists:rombels,id',
        'siswa_id' => 'required|array',
        // Validasi: siswa_id harus unik di tabel anggota_rombels
        'siswa_id.*' => 'required|exists:siswas,id|unique:anggota_rombels,siswa_id',
    ], [
        'siswa_id.*.unique' => 'Salah satu siswa sudah terdaftar di rombel lain.'
    ]);

    foreach ($request->siswa_id as $id) {
        AnggotaRombel::create([
            'rombel_id' => $request->rombel_id,
            'siswa_id' => $id
        ]);
    }

    return redirect()->back()->with('success', 'Siswa berhasil ditambahkan ke Rombel.');
}

    // Menghapus atau mengeluarkan seorang siswa dari rombongan belajar (rombel)
    public function destroy($id)
    {
        $anggota = AnggotaRombel::findOrFail($id);
        $anggota->delete();
        return redirect()->back()->with('success', 'Siswa dikeluarkan dari Rombel.');
    }
}