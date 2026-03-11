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

        // Check if user is Guru and Wali Kelas
        $guru_id = null;
        $rombel_id = null;
        $is_wali_kelas = false;
        $nama_kelas_wali = '';
        
        if (auth()->user()->role === 'guru') {
            $guru = \App\Models\Guru::where('user_id', auth()->id())->first();
            if ($guru) {
                $guru_id = $guru->id;
                $rombel = \App\Models\Rombel::where('guru_id', $guru_id)->first();
                if ($rombel) {
                    $is_wali_kelas = true;
                    $rombel_id = $rombel->id;
                    $kelas = \App\Models\Kelas::find($rombel->kelas_id);
                    $nama_kelas_wali = $kelas ? $kelas->tingkat . ' ' . $kelas->nama_kelas : $rombel->nama_rombel;
                }
            }
        }

        $jurusans = \App\Models\Jurusan::all();
        $semuaKelas = \App\Models\Kelas::all();
        
        // Active Period (Current Period) logic handling
        $periodes = \App\Models\Periode::all();
        $active_periode = \App\Models\Periode::where('is_active', true)->first();
        $selected_periode = request('periode_id', $active_periode ? $active_periode->id : null);

        $query = \App\Models\Siswa::with(['user', 'anggotaRombels.rombel.kelas.jurusan'])
            ->orderBy('nama_siswa', 'asc');

        // Jika dia wali kelas, paksa query hanya untuk rombel miliknya
        if ($is_wali_kelas) {
            $query->whereHas('anggotaRombels', function ($q) use ($rombel_id) {
                $q->where('rombel_id', $rombel_id);
            });
            $siswas = $query->get();
        } else {
            // Untuk Admin/Bukan Wali Kelas: Wajib pilih Kelas dulu agar tidak memuat semua data siswa
            if (request()->filled('kelas_id')) {
                $query->whereHas('anggotaRombels.rombel', function ($q) {
                    $q->where('kelas_id', request('kelas_id'));
                });
                
                // Tambahan proteksi filter Jurusan
                if (request()->filled('jurusan_id')) {
                    $query->whereHas('anggotaRombels.rombel.kelas', function ($q) {
                        $q->where('jurusan_id', request('jurusan_id'));
                    });
                }
                
                $siswas = $query->get();
            } else {
                // Return empty collection jika belum pilih kelas
                $siswas = collect();
            }
        }

        // Hitung Progress Penilaian untuk Guru/Wali Kelas
        $total_siswa = $siswas->count();
        $siswa_dinilai = 0;
        $progress_percentage = 0;

        if ($selected_periode && $total_siswa > 0) {
            $siswa_ids = $siswas->pluck('id')->toArray();
            $dinilai_count = \App\Models\PenilaianSikap::whereIn('siswa_id', $siswa_ids)
                                ->where('periode_id', $selected_periode)
                                ->count();
            $siswa_dinilai = $dinilai_count;
            $progress_percentage = ($siswa_dinilai / $total_siswa) * 100;
        }

        // Pass selected filters for the view to retain selected state
        $selected_jurusan = request('jurusan_id');
        $selected_kelas = request('kelas_id');
        
        return view('penilaian-sikap.index', compact(
            'siswas', 'jurusans', 'semuaKelas', 'periodes', 'selected_jurusan', 'selected_kelas', 'selected_periode',
            'is_wali_kelas', 'nama_kelas_wali', 'total_siswa', 'siswa_dinilai', 'progress_percentage', 'active_periode'
        ));
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

        $penilaian = \App\Models\PenilaianSikap::updateOrCreate(
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

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Penilaian sikap berhasil disimpan.',
                'data' => $penilaian
            ]);
        }

        // Redirect kembali ke detail siswa, bukan ke list utama 
        // Mengingat list utama butuh param "?kelas_id=" untuk filter Admin
        return redirect()->route('penilaian-sikap.show', ['siswa_id' => $siswa_id, 'periode_id' => $request->periode_id])
            ->with('success', 'Penilaian sikap berhasil disimpan.');
    }

    public function storeMassal(Request $request)
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'guru') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'periode_id' => 'required|exists:periodes,id',
            'siswa_ids' => 'required|array|min:1',
            'siswa_ids.*' => 'exists:siswas,id',
            'tanggung_jawab' => 'required|integer|min:1|max:5',
            'kejujuran' => 'required|integer|min:1|max:5',
            'sopan_santun' => 'required|integer|min:1|max:5',
            'kemandirian' => 'required|integer|min:1|max:5',
            'kerja_sama' => 'required|integer|min:1|max:5',
            'catatan' => 'nullable|string'
        ]);

        $penilais = [];
        foreach ($request->siswa_ids as $siswa_id) {
            $penilai = \App\Models\PenilaianSikap::updateOrCreate(
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
            $penilais[] = $penilai;
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Penilaian sikap massal berhasil disimpan.',
                'data' => $penilais
            ]);
        }

        return redirect()->back()->with('success', 'Penilaian massal berhasil disimpan.');
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
