<?php

namespace App\Http\Controllers;

use App\Models\Sesi;
use Illuminate\Http\Request;

class SesiController extends Controller
{
    // Menampilkan halaman daftar sesi atau slot waktu pelajaran referensi universal
    public function index()
    {
        // PERBAIKAN: Hapus 'hari' karena kolomnya sudah tidak ada di database
        $sesis = Sesi::orderBy('urutan', 'asc')->get();
        return view('sesi.index', compact('sesis'));
    }

    // Membuat dan menyisipkan data sesi waktu pembelajaran ke dalam database
    public function store(Request $request)
    {
        // PERBAIKAN: Jangan validasi 'hari' di sini
        $request->validate([
            'urutan' => 'required|numeric',
            'nama_sesi' => 'required',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
        ]);

        // Simpan hanya kolom yang ada di database
        Sesi::create([
            'urutan' => $request->urutan,
            'nama_sesi' => $request->nama_sesi,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'is_istirahat' => $request->has('is_istirahat'),
        ]);
        
        return back()->with('success', 'Sesi waktu berhasil ditambahkan secara universal!');
    }

    // Menghapus definisi jadwal sesi dari database
    public function destroy(Sesi $sesi)
    {
        $sesi->delete();
        return back()->with('success', 'Modul waktu berhasil dihapus!');
    }
}