<?php

namespace App\Http\Controllers;

use App\Models\Rombel;
use App\Models\Kelas;
use App\Models\Guru;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class RombelController extends Controller
{
    // Menampilkan halaman daftar rombongan belajar (rombel)
    // Menarik data terkait seperti kelas, guru wali, dan tahun ajaran
    public function index()
    {
        $rombels = Rombel::with(['kelas', 'guru', 'tahunAjaran'])->get();
        $kelas = Kelas::all();
        $gurus = Guru::all();
        $tahunAjarans = TahunAjaran::all();
        
        return view('rombel.index', compact('rombels', 'kelas', 'gurus', 'tahunAjarans'));
    }

    // Menyimpan pengaturan rombel baru ke dalam database
    public function store(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'nama_rombel' => 'required|string|max:255',
            'guru_id' => 'nullable|exists:gurus,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
        ]);

        Rombel::create($request->all());
        return redirect()->back()->with('success', 'Rombongan Belajar berhasil dibuat.');
    }

    // Memperbarui pengaturan rombel yang ada (misal ganti wali kelas atau nama rombel)
    public function update(Request $request, Rombel $rombel)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'nama_rombel' => 'required|string|max:255',
            'guru_id' => 'nullable|exists:gurus,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
        ]);

        $rombel->update($request->all());
        return redirect()->back()->with('success', 'Rombel berhasil diperbarui.');
    }

    // Menghapus data rombongan belajar dari database
    public function destroy(Rombel $rombel)
    {
        $rombel->delete();
        return redirect()->back()->with('success', 'Rombel berhasil dihapus.');
    }
}