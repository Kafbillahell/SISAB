<?php

namespace App\Http\Controllers;

use App\Models\Periode;
use Illuminate\Http\Request;

class PeriodeController extends Controller
{
    // Menampilkan daftar periode akademik/kegiatan yang terdaftar di sistem
    public function index()
    {
        $periodes = Periode::latest()->get();
        return view('periode.index', compact('periodes'));
    }

    // Membuat dan menyisipkan periode baru ke dalam database
    public function store(Request $request)
    {
        $request->validate([
            'nama_periode' => 'required|string|max:255',
            'is_active' => 'boolean'
        ]);

        Periode::create([
            'nama_periode' => $request->nama_periode,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('periode.index')->with('success', 'Periode berhasil ditambahkan.');
    }

    // Menyimpan perubahan pada data periode (seperti status keaktifan atau nama)
    public function update(Request $request, Periode $periode)
    {
        $request->validate([
            'nama_periode' => 'required|string|max:255',
            'is_active' => 'boolean'
        ]);

        $periode->update([
            'nama_periode' => $request->nama_periode,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('periode.index')->with('success', 'Periode berhasil diperbarui.');
    }

    // Menghapus data periode dari database
    public function destroy(Periode $periode)
    {
        $periode->delete();
        return redirect()->route('periode.index')->with('success', 'Periode berhasil dihapus.');
    }
}
