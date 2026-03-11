<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PenilaianSikapController extends Controller
{
    public function index()
    {
        // Only Admin and Guru can access
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'guru') {
            abort(403, 'Unauthorized action.');
        }

        $siswas = \App\Models\Siswa::with('user')->get();
        // optionally load rombel? Let's just pass siswas
        
        return view('penilaian-sikap.index', compact('siswas'));
    }

    public function form($siswa_id)
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'guru') {
            abort(403, 'Unauthorized action.');
        }

        $siswa = \App\Models\Siswa::findOrFail($siswa_id);
        $penilaian = \App\Models\PenilaianSikap::where('siswa_id', $siswa_id)->first();

        return view('penilaian-sikap.form', compact('siswa', 'penilaian'));
    }

    public function store(Request $request, $siswa_id)
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'guru') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'tanggung_jawab' => 'required|integer|min:1|max:5',
            'kejujuran' => 'required|integer|min:1|max:5',
            'sopan_santun' => 'required|integer|min:1|max:5',
            'kemandirian' => 'required|integer|min:1|max:5',
            'kerja_sama' => 'required|integer|min:1|max:5',
            'catatan' => 'nullable|string'
        ]);

        \App\Models\PenilaianSikap::updateOrCreate(
            ['siswa_id' => $siswa_id],
            [
                'penilai_id' => auth()->id(),
                'tanggung_jawab' => $request->tanggung_jawab,
                'kejujuran' => $request->kejujuran,
                'sopan_santun' => $request->sopan_santun,
                'kemandirian' => $request->kemandirian,
                'kerja_sama' => $request->kerja_sama,
                'catatan' => $request->catatan,
            ]
        );

        return redirect()->route('penilaian-sikap.index')->with('success', 'Penilaian sikap berhasil disimpan.');
    }

    public function show($siswa_id)
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'guru') {
            abort(403, 'Unauthorized action.');
        }

        $siswa = \App\Models\Siswa::findOrFail($siswa_id);
        $penilaian = \App\Models\PenilaianSikap::with('penilai')->where('siswa_id', $siswa_id)->first();

        return view('penilaian-sikap.show', compact('siswa', 'penilaian'));
    }
}
