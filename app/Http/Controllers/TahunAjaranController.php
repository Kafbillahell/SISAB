<?php

namespace App\Http\Controllers;

use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class TahunAjaranController extends Controller
{
    public function index()
    {
        $tahunAjarans = TahunAjaran::orderBy('tahun', 'desc')->get();
        return view('tahun_ajaran.index', compact('tahunAjarans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun' => 'required|string',
            'semester' => 'required|in:Ganjil,Genap',
        ]);

        TahunAjaran::create($request->all());
        return redirect()->back()->with('success', 'Tahun Ajaran berhasil ditambah.');
    }

    public function activate($id)
    {
        // Nonaktifkan semua tahun ajaran
        TahunAjaran::where('is_active', true)->update(['is_active' => false]);

        // Aktifkan yang dipilih
        $ta = TahunAjaran::findOrFail($id);
        $ta->update(['is_active' => true]);

        return redirect()->back()->with('success', 'Tahun Ajaran ' . $ta->tahun . ' (' . $ta->semester . ') sekarang aktif.');
    }

    public function destroy(TahunAjaran $tahunAjaran)
    {
        if ($tahunAjaran->is_active) {
            return redirect()->back()->with('error', 'Tahun ajaran aktif tidak boleh dihapus!');
        }
        $tahunAjaran->delete();
        return redirect()->back()->with('success', 'Data dihapus.');
    }
}