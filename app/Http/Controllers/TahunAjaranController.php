<?php

namespace App\Http\Controllers;

use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class TahunAjaranController extends Controller
{
    // Menampilkan halaman daftar seluruh tahun ajaran dan semester yang ada dalam sistem
    public function index()
    {
        $tahunAjarans = TahunAjaran::orderBy('tahun', 'desc')->get();
        return view('tahun_ajaran.index', compact('tahunAjarans'));
    }

    // Menyimpan pengaturan tahun ajaran dan semester baru ke dalam database
    public function store(Request $request)
    {
        $request->validate([
            'tahun' => 'required|string',
            'semester' => 'required|in:Ganjil,Genap',
        ]);

        TahunAjaran::create($request->all());
        return redirect()->back()->with('success', 'Tahun Ajaran berhasil ditambah.');
    }

    // Mengaktifkan suatu tahun ajaran spesifik
    // Menonaktifkan seluruh tahun ajaran lain terlebih dahulu agar hanya satu yang aktif
    public function activate($id)
    {
        // Nonaktifkan semua tahun ajaran
        TahunAjaran::where('is_active', true)->update(['is_active' => false]);

        // Aktifkan yang dipilih
        $ta = TahunAjaran::findOrFail($id);
        $ta->update(['is_active' => true]);

        return redirect()->back()->with('success', 'Tahun Ajaran ' . $ta->tahun . ' (' . $ta->semester . ') sekarang aktif.');
    }

    // Menghapus data tahun ajaran dari sistem
    // Mencegah panghapusan jika tahun ajaran tersebut masih berstatus aktif
    public function destroy(TahunAjaran $tahunAjaran)
    {
        if ($tahunAjaran->is_active) {
            return redirect()->back()->with('error', 'Tahun ajaran aktif tidak boleh dihapus!');
        }
        $tahunAjaran->delete();
        return redirect()->back()->with('success', 'Data dihapus.');
    }
}