<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiswaController extends Controller
{
    // Menampilkan semua data siswa
    public function index()
    {
        $siswas = Siswa::latest()->paginate(10);
        return view('siswas.index', compact('siswas'));
    }

    // Form tambah siswa
    public function create()
    {
        return view('siswas.create');
    }

    // Simpan data siswa baru
    public function store(Request $request)
{
    $request->validate([
        'nisn'          => 'required|unique:siswas,nisn',
        'nama_siswa'    => 'required|string|max:255',
        'jenis_kelamin' => 'required|in:L,P',
        'foto'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    // Mengambil ID user yang sedang login
    $userId = auth()->id();

    // Cek jika user ID tidak ada (kasus darurat)
    if (!$userId) {
        return redirect()->back()->withErrors('Sesi Anda habis. Silakan login kembali.');
    }

    $data = $request->all();
    $data['user_id'] = $userId;

    if ($request->hasFile('foto')) {
        $data['foto'] = $request->file('foto')->store('foto_siswa', 'public');
    }

    Siswa::create($data);

    return redirect()->route('siswas.index')->with('success', 'Data siswa berhasil ditambahkan.');
}

    // Form edit siswa
    public function edit(Siswa $siswa)
    {
        return view('siswas.edit', compact('siswa'));
    }

    // Update data siswa
    public function update(Request $request, Siswa $siswa)
    {
        $request->validate([
            'nisn'          => 'required|unique:siswas,nisn,' . $siswa->id,
            'nama_siswa'    => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'foto'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($siswa->foto) {
                Storage::disk('public')->delete($siswa->foto);
            }
            $data['foto'] = $request->file('foto')->store('foto_siswa', 'public');
        }

        $siswa->update($data);

        return redirect()->route('siswas.index')->with('success', 'Data siswa berhasil diupdate.');
    }

    // Hapus data siswa
    public function destroy(Siswa $siswa)
    {
        if ($siswa->foto) {
            Storage::disk('public')->delete($siswa->foto);
        }
        
        $siswa->delete();
        return redirect()->route('siswas.index')->with('success', 'Data siswa berhasil dihapus.');
    }
}