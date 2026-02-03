<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\Jadwal;
use App\Models\Siswa;
use App\Models\Rombel;
use App\Models\Guru;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PresensiController extends Controller
{
    public function index(Request $request)
{
    $rombels = \App\Models\Rombel::all();
    
    // Inisialisasi koleksi kosong agar saat pertama buka tidak error/berat
    $presensis = collect();

    // Jalankan query HANYA jika rombel_id dipilih
    if ($request->filled('rombel_id')) {
        $query = Presensi::with(['siswa', 'jadwal.mapel', 'jadwal.rombel.guru']);

        // Filter Kelas Wajib
        $query->whereHas('jadwal', function($q) use ($request) {
            $q->where('rombel_id', $request->rombel_id);
        });

        // Filter Tanggal (Default hari ini jika kosong)
        if (!$request->filled('start_date') && !$request->filled('end_date')) {
            $query->whereDate('waktu_scan', Carbon::today());
        } else {
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('waktu_scan', [
                    $request->start_date . ' 00:00:00', 
                    $request->end_date . ' 23:59:59'
                ]);
            }
        }

        $presensis = $query->orderBy('waktu_scan', 'desc')->get();
    }

    return view('presensi.index', compact('presensis', 'rombels'));
}

    public function scanner(Request $request)
    {
        $now = now()->setTimezone('Asia/Jakarta');
        $hariIndo = $this->getHariIndo($now->format('l'));
        $jamSekarang = $now->format('H:i:s');

        $allGurus = Guru::all();
        $targetGuruId = $request->guru_id ?? (auth()->user()->guru_id ?? null);

        $queryJadwal = Jadwal::where('hari', $hariIndo)
            ->whereTime('jam_mulai', '<=', $jamSekarang)
            ->whereTime('jam_selesai', '>=', $jamSekarang)
            ->with(['mapel', 'rombel']);

        if ($targetGuruId) {
            $queryJadwal->where('guru_id', $targetGuruId);
        }

        $jadwalAktif = $queryJadwal->first();

        // Gunakan ini karena terbukti tidak error di database kamu
        $daftarSiswa = Siswa::whereNotNull('foto')->get(['id', 'nama_siswa', 'foto']);

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