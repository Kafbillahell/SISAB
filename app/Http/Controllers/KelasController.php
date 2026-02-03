<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::all();
        return view('admin.kelas.index', compact('kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'tingkat'    => 'required|in:10,11,12',
        ], [
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            'tingkat.required'    => 'Tingkat wajib dipilih.',
        ]);

        Kelas::create($request->all());

        return back()->with('success', 'Kelas berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'tingkat'    => 'required|in:10,11,12',
        ]);

        $kelas = Kelas::findOrFail($id);
        $kelas->update($request->all());

        return back()->with('success', 'Data kelas berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);
        $kelas->delete();

        return back()->with('success', 'Kelas berhasil dihapus!');
    }
}