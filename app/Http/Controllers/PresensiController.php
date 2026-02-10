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
        $rombels = Rombel::all();
        $mapels = collect();
        $presensis = collect();
        $siswa_stats = collect();
        $total_sesi = 0;
        $statistik_kelas = ['persentase_hadir' => 0];

        if ($request->filled('rombel_id')) {
            $rombelId = $request->rombel_id;

            // 1. Ambil daftar Mapel yang ada di jadwal rombel tersebut
            $mapels = Mapel::whereHas('jadwals', function($q) use ($rombelId) {
                $q->where('rombel_id', $rombelId);
            })->get();

            // 2. Filter Log Presensi Utama
            $query = Presensi::with(['siswa', 'jadwal.mapel']);
            $query->whereHas('jadwal', function($q) use ($request, $rombelId) {
                $q->where('rombel_id', $rombelId);
                if ($request->filled('mapel_id')) {
                    $q->where('mapel_id', $request->mapel_id);
                }
            });

            // Filter Tanggal
            $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->format('Y-m-d');
            $endDate = $request->end_date ?? Carbon::now()->format('Y-m-d');
            $query->whereBetween('waktu_scan', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            
            $presensis = $query->orderBy('waktu_scan', 'desc')->get();

            // 3. LOGIKA STATISTIK MURID (Menggunakan DB Table agar tidak error relasi)
            $siswaIds = DB::table('anggota_rombels')
                ->where('rombel_id', $rombelId)
                ->pluck('siswa_id');

            $siswas = Siswa::whereIn('id', $siswaIds)->get();

            // Hitung total sesi yang unik (berapa kali pertemuan terjadi)
            $total_sesi = Presensi::whereHas('jadwal', function($q) use ($request, $rombelId) {
                    $q->where('rombel_id', $rombelId);
                    if ($request->filled('mapel_id')) {
                        $q->where('mapel_id', $request->mapel_id);
                    }
                })
                ->whereBetween('waktu_scan', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->selectRaw('DATE(waktu_scan) as tanggal, jadwal_id')
                ->groupBy('tanggal', 'jadwal_id')
                ->get()
                ->count();

            $total_sesi_fixed = $total_sesi > 0 ? $total_sesi : 1;

            $siswa_stats = $siswas->map(function($s) use ($request, $startDate, $endDate, $total_sesi_fixed, $rombelId) {
                $p_siswa = Presensi::where('siswa_id', $s->id)
                    ->whereBetween('waktu_scan', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                    ->whereHas('jadwal', function($q) use ($request, $rombelId) {
                        $q->where('rombel_id', $rombelId);
                        if ($request->filled('mapel_id')) {
                            $q->where('mapel_id', $request->mapel_id);
                        }
                    })->get();

                $hadir = $p_siswa->where('keterangan', 'Hadir')->count();
                $persen = ($hadir / $total_sesi_fixed) * 100;

                return (object)[
                    'nama_siswa' => $s->nama_siswa,
                    'nisn' => $s->nisn,
                    'foto' => $s->foto,
                    'total_hadir' => $hadir,
                    'total_izin' => $p_siswa->whereIn('keterangan', ['Izin', 'Sakit'])->count(),
                    'total_alpa' => $p_siswa->where('keterangan', 'Alpa')->count(),
                    'persen' => round($persen, 1)
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
    $now = now()->setTimezone('Asia/Jakarta');
    $hariIndo = $this->getHariIndo($now->format('l'));
    $jamSekarang = $now->format('H:i:s');

    $allGurus = Guru::all();
    $targetGuruId = $request->guru_id ?? (auth()->user()->guru_id ?? null);

    $jadwalAktif = Jadwal::where('hari', $hariIndo)
        ->whereTime('jam_mulai', '<=', $jamSekarang)
        ->whereTime('jam_selesai', '>=', $jamSekarang)
        ->with(['mapel', 'rombel'])
        ->when($targetGuruId, function($q) use ($targetGuruId) {
            return $q->where('guru_id', $targetGuruId);
        })
        ->first();

    $daftarSiswa = collect();

    // LOGIKA FILTER: Gunakan tabel anggota_rombels
    if ($jadwalAktif) {
        $daftarSiswa = \DB::table('anggota_rombels')
            ->join('siswas', 'anggota_rombels.siswa_id', '=', 'siswas.id')
            ->where('anggota_rombels.rombel_id', $jadwalAktif->rombel_id)
            ->whereNotNull('siswas.foto')
            ->get(['siswas.id', 'siswas.nama_siswa', 'siswas.foto']);
    }

    return view('presensi.scanner', compact('jadwalAktif', 'daftarSiswa', 'allGurus', 'targetGuruId'));
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

        $sudahAbsen = Presensi::where('siswa_id', $siswaId)
            ->where('jadwal_id', $jadwalId)
            ->whereDate('waktu_scan', Carbon::today())
            ->first();

        if ($sudahAbsen) {
            return response()->json(['status' => 'exists', 'message' => 'Sudah absen hari ini!']);
        }

        Presensi::create([
            'siswa_id' => $siswaId,
            'jadwal_id' => $jadwalId,
            'waktu_scan' => now()->setTimezone('Asia/Jakarta'),
            'keterangan' => 'Hadir'
        ]);

        return response()->json(['status' => 'success', 'message' => 'Absensi berhasil dicatat!']);
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