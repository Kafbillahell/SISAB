<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use Illuminate\Http\Request;

class JurusanController extends Controller
{
    // Menampilkan halaman daftar semua jurusan yang tersedia
    public function index()
    {
        $jurusans = Jurusan::orderBy('nama_jurusan', 'asc')->get();
        return view('jurusan.index', compact('jurusans'));
    }

    // Menyimpan data jurusan baru ke dalam database
    public function store(Request $request)
    {
        $request->validate([
            'kode_jurusan' => 'required|string|unique:jurusans,kode_jurusan',
            'nama_jurusan' => 'required|string',
        ]);

        Jurusan::create($request->all());
        return redirect()->back()->with('success', 'Jurusan berhasil ditambahkan.');
    }

    // Memperbarui informasi jurusan yang dipilih di dalam database
    public function update(Request $request, Jurusan $jurusan)
    {
        $request->validate([
            'kode_jurusan' => 'required|string|unique:jurusans,kode_jurusan,' . $jurusan->id,
            'nama_jurusan' => 'required|string',
        ]);

        $jurusan->update($request->all());
        return redirect()->back()->with('success', 'Jurusan berhasil diupdate.');
    }

    // Menghapus data jurusan dari database berdasarkan objek jurusan yang ditentukan
    public function destroy(Jurusan $jurusan)
    {
        $jurusan->delete();
        return redirect()->back()->with('success', 'Jurusan berhasil dihapus.');
    }
}