<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Jurusan;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::with('jurusan')->orderBy('tingkat', 'asc')->get();
        $jurusans = Jurusan::orderBy('nama_jurusan', 'asc')->get();
        
        return view('kelas.index', compact('kelas', 'jurusans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tingkat' => 'required|string|max:10',
            'jurusan_id' => 'required|exists:jurusans,id',
            'nama_kelas' => 'required|string|max:50',
        ]);

        Kelas::create($request->all());

        return redirect()->back()->with('success', 'Kelas berhasil ditambahkan.');
    }

    // Ubah parameter dari Kelas $kelas menjadi $id
    public function update(Request $request, $id) 
    {
        $request->validate([
            'tingkat' => 'required|string|max:10',
            'jurusan_id' => 'required|exists:jurusans,id',
            'nama_kelas' => 'required|string|max:50',
        ]);

        $kelas = Kelas::findOrFail($id); // Cari data berdasarkan ID
        $kelas->update($request->all());

        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil diupdate.');
    }

    // Ubah juga parameter destroy agar konsisten
    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);
        $kelas->delete();
        
        return redirect()->back()->with('success', 'Kelas berhasil dihapus.');
    }
}