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

        $jurusans = \App\Models\Jurusan::all();
        $semuaKelas = \App\Models\Kelas::all();
        $periodes = \App\Models\Periode::where('is_active', true)->get();

        $query = \App\Models\Siswa::with(['user', 'anggotaRombels.rombel.kelas.jurusan']);

        // Filter Jurusan
        if (request()->filled('jurusan_id')) {
            $query->whereHas('anggotaRombels.rombel.kelas', function ($q) {
                $q->where('jurusan_id', request('jurusan_id'));
            });
        }

        // Filter Kelas
        if (request()->filled('kelas_id')) {
            $query->whereHas('anggotaRombels.rombel', function ($q) {
                $q->where('kelas_id', request('kelas_id'));
            });
        }

        $siswas = $query->get();

        // Pass selected filters for the view to retain selected state
        $selected_jurusan = request('jurusan_id');
        $selected_kelas = request('kelas_id');
        $selected_periode = request('periode_id');
        
        return view('penilaian-sikap.index', compact('siswas', 'jurusans', 'semuaKelas', 'periodes', 'selected_jurusan', 'selected_kelas', 'selected_periode'));
    }

    public function form($siswa_id)
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'guru') {
            abort(403, 'Unauthorized action.');
        }

        $siswa = \App\Models\Siswa::findOrFail($siswa_id);
        
        $periode_id = request('periode_id');
        if (!$periode_id) {
            $periodeAktif = \App\Models\Periode::where('is_active', true)->first();
            $periode_id = $periodeAktif ? $periodeAktif->id : null;
        }

        $penilaian = null;
        if ($periode_id) {
            $penilaian = \App\Models\PenilaianSikap::where('siswa_id', $siswa_id)
                ->where('periode_id', $periode_id)
                ->first();
        }

        $periodes = \App\Models\Periode::where('is_active', true)->get();

        return view('penilaian-sikap.form', compact('siswa', 'penilaian', 'periodes', 'periode_id'));
    }

    public function store(Request $request, $siswa_id)
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'guru') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'periode_id' => 'required|exists:periodes,id',
            'tanggung_jawab' => 'required|integer|min:1|max:5',
            'kejujuran' => 'required|integer|min:1|max:5',
            'sopan_santun' => 'required|integer|min:1|max:5',
            'kemandirian' => 'required|integer|min:1|max:5',
            'kerja_sama' => 'required|integer|min:1|max:5',
            'catatan' => 'nullable|string'
        ]);

        \App\Models\PenilaianSikap::updateOrCreate(
            ['siswa_id' => $siswa_id, 'periode_id' => $request->periode_id],
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
        
        $periode_id = request('periode_id');
        
        $query = \App\Models\PenilaianSikap::with(['penilai', 'periode'])->where('siswa_id', $siswa_id);
        
        if ($periode_id) {
            $query->where('periode_id', $periode_id);
        }
        
        $penilaians = $query->orderBy('created_at', 'desc')->get();
        $periodes = \App\Models\Periode::all();

        return view('penilaian-sikap.show', compact('siswa', 'penilaians', 'periodes', 'periode_id'));
    }
}
