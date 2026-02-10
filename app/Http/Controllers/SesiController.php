<?php

namespace App\Http\Controllers;

use App\Models\Sesi;
use Illuminate\Http\Request;

class SesiController extends Controller
{
    public function index()
    {
        // PERBAIKAN: Hapus 'hari' karena kolomnya sudah tidak ada di database
        $sesis = Sesi::orderBy('urutan', 'asc')->get();
        return view('sesi.index', compact('sesis'));
    }

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

    public function destroy(Sesi $sesi)
    {
        $sesi->delete();
        return back()->with('success', 'Modul waktu berhasil dihapus!');
    }
}