<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\Jadwal;
use App\Models\Siswa;
use App\Models\Rombel;
use App\Models\Guru;
use App\Models\Mapel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PresensiController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $guru = null;

        if ($user->role == 'guru') {
            $guru = Guru::where('user_id', $user->id)->first();
            $rombelIds = Jadwal::where('guru_id', $guru->id)->pluck('rombel_id')->unique();
            $rombels = Rombel::whereIn('id', $rombelIds)->get();
        } else {
            $rombels = Rombel::all();
        }

        $mapels = collect();
        $presensis = collect();
        $siswa_stats = collect();
        $total_sesi = 0;
        $statistik_kelas = ['persentase_hadir' => 0];

        if ($request->filled('rombel_id') && $rombels->contains('id', $request->rombel_id)) {
            $rombelId = $request->rombel_id;

            // 1. Ambil daftar Mapel yang ada di jadwal rombel tersebut
            $mapels = Mapel::whereHas('jadwals', function($q) use ($rombelId, $guru) {
                $q->where('rombel_id', $rombelId);
                if ($guru) {
                    $q->where('guru_id', $guru->id);
                }
            })->get();

            // 2. Filter Log Presensi Utama
            $query = Presensi::with(['siswa', 'jadwal.mapel']);
            $query->whereHas('jadwal', function($q) use ($request, $rombelId, $guru) {
                $q->where('rombel_id', $rombelId);
                if ($request->filled('mapel_id')) {
                    $q->where('mapel_id', $request->mapel_id);
                }
                if ($guru) {
                    $q->where('guru_id', $guru->id);
                }
            });

            // Filter Tanggal
            $startDate = $request->start_date ?? Carbon::now()->format('Y-m-d');
            $endDate = $request->end_date ?? Carbon::now()->format('Y-m-d');
            $query->whereBetween('waktu_scan', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            
            $presensis = $query->orderBy('waktu_scan', 'desc')->get();

            // 3. LOGIKA STATISTIK MURID (Menggunakan DB Table agar tidak error relasi)
            $siswaIds = DB::table('anggota_rombels')
                ->where('rombel_id', $rombelId)
                ->pluck('siswa_id');

            $siswas = Siswa::whereIn('id', $siswaIds)->get();

            // Hitung total sesi yang unik (berapa kali pertemuan terjadi)
            $total_sesi = Presensi::whereHas('jadwal', function($q) use ($request, $rombelId, $guru) {
                    $q->where('rombel_id', $rombelId);
                    if ($request->filled('mapel_id')) {
                        $q->where('mapel_id', $request->mapel_id);
                    }
                    if ($guru) {
                        $q->where('guru_id', $guru->id);
                    }
                })
                ->whereBetween('waktu_scan', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->selectRaw('DATE(waktu_scan) as tanggal, jadwal_id')
                ->groupByRaw('DATE(waktu_scan), jadwal_id')
                ->get()
                ->count();

            $total_sesi_fixed = $total_sesi > 0 ? $total_sesi : 1;

            $siswa_stats = $siswas->map(function($s) use ($request, $startDate, $endDate, $total_sesi_fixed, $rombelId, $guru) {
                $p_siswa = Presensi::where('siswa_id', $s->id)
                    ->whereBetween('waktu_scan', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                    ->whereHas('jadwal', function($q) use ($request, $rombelId, $guru) {
                        $q->where('rombel_id', $rombelId);
                        if ($request->filled('mapel_id')) {
                            $q->where('mapel_id', $request->mapel_id);
                        }
                        if ($guru) {
                            $q->where('guru_id', $guru->id);
                        }
                    })->get();

                $hadir = $p_siswa->where('keterangan', 'Hadir')->count();
                $persen = ($hadir / $total_sesi_fixed) * 100;

                return (object)[
                    'id' => $s->id,
                    'nama_siswa' => $s->nama_siswa,
                    'nisn' => $s->nisn,
                    'foto' => $s->foto,
                    'total_hadir' => $hadir,
                    'total_izin' => $p_siswa->where('keterangan', 'Izin')->count(),
                    'total_sakit' => $p_siswa->where('keterangan', 'Sakit')->count(),
                    'total_alpa' => $p_siswa->where('keterangan', 'Alpa')->count(),
                    'persen' => round($persen, 1),
                    'detail' => $p_siswa->sortByDesc('waktu_scan')->values()
                ];
            });

            // 4. Hitung Rata-rata Kehadiran Kelas
            if ($siswa_stats->count() > 0) {
                $statistik_kelas['persentase_hadir'] = round($siswa_stats->avg('persen'), 1);
            }
        }

        return view('presensi.index', compact(
            'presensis', 'rombels', 'mapels', 'siswa_stats', 'total_sesi', 'statistik_kelas'
        ));
    }

    public function scanner(Request $request)
{
    // 1. Ambil Waktu & Hari Real-time
    $now = \Carbon\Carbon::now()->setTimezone('Asia/Jakarta');
    $hariIndo = $this->getHariIndo($now->format('l'));
    $jamSekarang = $now->format('H:i:s');

    $allGurus = \App\Models\Guru::all();
    $user = auth()->user();
    $targetGuruId = null;
    $jadwalAktif = null;

    // 2. LOGIKA PENENTUAN JADWAL
    if ($user->role == 'admin') { 
        $targetGuruId = $request->guru_id;
        if ($targetGuruId) {
            $jadwalAktif = \App\Models\Jadwal::where('guru_id', $targetGuruId)
                ->where('hari', $hariIndo)
                ->whereTime('jam_mulai', '<=', $jamSekarang)
                ->whereTime('jam_selesai', '>=', $jamSekarang)
                ->first();
        }
    } 
    elseif ($user->role == 'siswa') {
        // JIKA SISWA LOGIN: Cari rombel siswa ini
        $siswa = \App\Models\Siswa::where('user_id', $user->id)->first();
        
        if ($siswa) {
            // Cari rombel tempat siswa ini bernaung
            $anggotaRombel = \DB::table('anggota_rombels')
                ->where('siswa_id', $siswa->id)
                ->first();

            if ($anggotaRombel) {
                // Strategi pencarian jadwal untuk siswa:
                // 1. Cari jadwal yang SEDANG BERJALAN (jam_mulai <= sekarang <= jam_selesai) di hari ini
                $jadwalAktif = \App\Models\Jadwal::where('rombel_id', $anggotaRombel->rombel_id)
                    ->where('hari', $hariIndo)
                    ->whereTime('jam_mulai', '<=', $jamSekarang)
                    ->whereTime('jam_selesai', '>=', $jamSekarang)
                    ->first();
                
                // 2. Jika tidak ada jadwal aktif hari ini, tampilkan jadwal PERTAMA hari ini (untuk siswa bisa scan lebih awal)
                if (!$jadwalAktif) {
                    $jadwalAktif = \App\Models\Jadwal::where('rombel_id', $anggotaRombel->rombel_id)
                        ->where('hari', $hariIndo)
                        ->orderBy('jam_mulai', 'asc')
                        ->first();
                }
                
                if ($jadwalAktif) {
                    $targetGuruId = $jadwalAktif->guru_id;
                }
            }
        }
    } 
    else {
        // JIKA GURU LOGIN
        $guru = \App\Models\Guru::where('user_id', $user->id)->first();
        if ($guru) {
            $targetGuruId = $guru->id;
            $jadwalAktif = \App\Models\Jadwal::where('guru_id', $targetGuruId)
                ->where('hari', $hariIndo)
                ->whereTime('jam_mulai', '<=', $jamSekarang)
                ->whereTime('jam_selesai', '>=', $jamSekarang)
                ->first();
        }
    }

    // 3. Ambil Daftar Siswa jika jadwal ketemu
    $daftarSiswa = collect();
    if ($jadwalAktif) {
        // Load relasi agar di view tidak error saat panggil $jadwalAktif->guru->nama_guru
        $jadwalAktif->load(['mapel', 'rombel', 'guru']);

        $daftarSiswa = \DB::table('anggota_rombels')
            ->join('siswas', 'anggota_rombels.siswa_id', '=', 'siswas.id')
            ->where('anggota_rombels.rombel_id', $jadwalAktif->rombel_id)
            ->whereNotNull('siswas.foto')
            ->get(['siswas.id', 'siswas.nama_siswa', 'siswas.foto']);
    }

    return view('presensi.scanner', [
        'jadwalAktif' => $jadwalAktif,
        'daftarSiswa' => $daftarSiswa,
        'allGurus' => $allGurus,
        'targetGuruId' => $targetGuruId
    ]);
}

    /**
     * AJAX: Return siswa list (id, nama_siswa, foto URL) for a rombel (only students in rombel)
     */
    public function daftarSiswa(Request $request, $rombelId)
    {
        $jadwalId = $request->query('jadwal_id');

        $siswas = \DB::table('anggota_rombels')
            ->join('siswas', 'anggota_rombels.siswa_id', '=', 'siswas.id')
            ->where('anggota_rombels.rombel_id', $rombelId)
            ->whereNotNull('siswas.foto')
            ->get(['siswas.id', 'siswas.nama_siswa', 'siswas.foto']);

        $today = Carbon::today()->setTimezone('Asia/Jakarta');

        $result = $siswas->map(function($s) use ($jadwalId, $today) {
            $presensi = \App\Models\Presensi::where('siswa_id', $s->id)
                ->whereDate('waktu_scan', $today)
                ->when($jadwalId, function($q) use ($jadwalId) {
                    return $q->where('jadwal_id', $jadwalId);
                })->latest('waktu_scan')->first();

            $presensiData = null;
            if ($presensi) {
                $k = trim((string)$presensi->keterangan);
                $isManual = in_array($k, ['Izin', 'Sakit', 'Alpa']);
                $presensiData = [
                    'keterangan' => $k,
                    'is_manual' => $isManual
                ];
            }

            return [
                'id' => $s->id,
                'nama' => $s->nama_siswa,
                'foto' => asset('storage/' . $s->foto),
                'presensi' => $presensiData
            ];
        })->values();

        return response()->json(['data' => $result]);
    }

    public function store(Request $request)
    {
        $siswaId = $request->siswa_id;
        $jadwalId = $request->jadwal_id;

        if (!$jadwalId) {
            return response()->json(['status' => 'error', 'message' => 'Tidak ada jadwal aktif!'], 422);
        }

        // Validasi tambahan: Pastikan jam masih masuk dalam rentang jadwal
        $now = now()->setTimezone('Asia/Jakarta');
        $jamSekarang = $now->format('H:i:s');
        $hariIndo = $this->getHariIndo($now->format('l'));

        $cekJadwal = Jadwal::where('id', $jadwalId)
            ->where('hari', $hariIndo)
            ->whereTime('jam_mulai', '<=', $jamSekarang)
            ->whereTime('jam_selesai', '>=', $jamSekarang)
            ->exists();

        if (!$cekJadwal) {
            return response()->json(['status' => 'error', 'message' => 'Sesi absen sudah berakhir atau tidak sesuai hari!'], 403);
        }

        $todayForCheck = Carbon::today()->setTimezone('Asia/Jakarta')->format('Y-m-d');

        // Cek 1: Apakah ada presensi HADIR untuk jadwal spesifik hari ini?
        $existingHadir = Presensi::where('siswa_id', $siswaId)
            ->where('jadwal_id', $jadwalId)
            ->whereDate('waktu_scan', $todayForCheck)
            ->whereIn('keterangan', ['Hadir', 'hadir'])
            ->first();

        if ($existingHadir) {
            return response()->json(['status' => 'exists', 'message' => 'Siswa sudah absen.']);
        }

        // Cek 2: Apakah ada presensi MANUAL (Izin/Sakit/Alpa) untuk siswa INI hari ini (SEMUA jadwal)?
        $existingManual = Presensi::where('siswa_id', $siswaId)
            ->whereDate('waktu_scan', $todayForCheck)
            ->whereIn('keterangan', ['Izin', 'Sakit', 'Alpa', 'izin', 'sakit', 'alpa'])
            ->first();

        if ($existingManual) {
            return response()->json(['status' => 'blocked', 'message' => 'Sudah ada data masuk'], 409);
        }

        // Gunakan kunci bernama MySQL agar operasi insert serial (menghindari race condition)
        $lockName = "presensi_{$siswaId}_{$jadwalId}_{$todayForCheck}";
        $gotLock = DB::select('SELECT GET_LOCK(?, 5) as got', [$lockName]);
        $acquired = isset($gotLock[0]) && (int)($gotLock[0]->got ?? $gotLock[0]->GET_LOCK) === 1;

        try {
            if (! $acquired) {
                // Jika tidak dapat kunci, fallback cek eksistensi dan beri tahu
                $nowExists = Presensi::where('siswa_id', $siswaId)
                    ->where('jadwal_id', $jadwalId)
                    ->whereDate('waktu_scan', $todayForCheck)
                    ->exists();

                if ($nowExists) {
                    return response()->json(['status' => 'exists', 'message' => 'Siswa sudah absen.']);
                }

                return response()->json(['status' => 'error', 'message' => 'Gagal memperoleh kunci. Coba lagi.'], 423);
            }

            // Setelah memperoleh kunci, cek ulang apakah sudah ada presensi (mungkin dibuat paralel)
            $nowExists = Presensi::where('siswa_id', $siswaId)
                ->where('jadwal_id', $jadwalId)
                ->whereDate('waktu_scan', $todayForCheck)
                ->exists();

            if ($nowExists) {
                return response()->json(['status' => 'exists', 'message' => 'Siswa sudah absen.']);
            }

            // Insert record presensi (tangani kemungkinan unique constraint pada DB)
            try {
                Presensi::create([
                    'siswa_id' => $siswaId,
                    'jadwal_id' => $jadwalId,
                    'waktu_scan' => now()->setTimezone('Asia/Jakarta'),
                    'keterangan' => 'Hadir'
                ]);

                return response()->json(['status' => 'success', 'message' => 'Absensi berhasil dicatat!']);
            } catch (\Illuminate\Database\QueryException $ex) {
                // SQLSTATE 23000 (integrity constraint violation) likely duplicate unique index
                $sqlState = $ex->getCode();
                if (in_array($sqlState, ['23000', '23505'])) {
                    return response()->json(['status' => 'exists', 'message' => 'Siswa sudah absen.']);
                }

                // Jika error lain, rethrow so it's visible in logs (but return JSON to client)
                \Log::error('Presensi create failed: '.$ex->getMessage());
                return response()->json(['status' => 'error', 'message' => 'Gagal mencatat absensi.'], 500);
            }
        } finally {
            // Lepaskan kunci jika sempat diperoleh
            if ($acquired) {
                DB::select('SELECT RELEASE_LOCK(?)', [$lockName]);
            }
        }
    }

    private function getHariIndo($day)
    {
        $map = [
            'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'
        ];
        return $map[$day];
    }
}