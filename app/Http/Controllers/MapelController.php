<?php

namespace App\Http\Controllers;

use App\Models\Mapel;
use Illuminate\Http\Request;

class MapelController extends Controller
{
    // Menampilkan halaman daftar seluruh Mata Pelajaran (Mapel)
    public function index()
    {
        $mapels = Mapel::orderBy('nama_mapel', 'asc')->get();
        return view('mapel.index', compact('mapels'));
    }

    // Menyimpan mata pelajaran baru ke database dengan validasi unik kode_mapel
    public function store(Request $request)
    {
        $request->validate([
            'nama_mapel' => 'required|string|max:255',
            'kode_mapel' => 'required|string|unique:mapels,kode_mapel|max:20',
        ]);

        Mapel::create($request->all());
        return redirect()->back()->with('success', 'Mata Pelajaran berhasil ditambahkan.');
    }

    // Memperbarui atribut dari sebuah mata pelajaran eksisting di database
    public function update(Request $request, Mapel $mapel)
    {
        $request->validate([
            'nama_mapel' => 'required|string|max:255',
            'kode_mapel' => 'required|string|max:20|unique:mapels,kode_mapel,' . $mapel->id,
        ]);

        $mapel->update($request->all());
        return redirect()->back()->with('success', 'Mata Pelajaran berhasil diperbarui.');
    }

    // Menghapus mata pelajaran dari database
    public function destroy(Mapel $mapel)
    {
        $mapel->delete();
        return redirect()->back()->with('success', 'Mata Pelajaran berhasil dihapus.');
    }
}