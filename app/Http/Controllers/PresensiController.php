<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\Jadwal;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PresensiController extends Controller
{
    public function index()
    {
        $presensis = Presensi::with(['siswa', 'jadwal.mapel', 'jadwal.rombel'])
                    ->orderBy('waktu_scan', 'desc')
                    ->get();
        return view('presensi.index', compact('presensis'));
    }

    public function scanner()
    {
        $now = now()->setTimezone('Asia/Jakarta');
        $hariIndo = $this->getHariIndo($now->format('l'));
        $jamSekarang = $now->format('H:i:s');

        $jadwalAktif = Jadwal::where('hari', $hariIndo)
            ->whereTime('jam_mulai', '<=', $jamSekarang)
            ->whereTime('jam_selesai', '>=', $jamSekarang)
            ->with(['mapel', 'rombel'])
            ->first();

        // PENTING: Ambil data siswa yang fotonya tidak kosong untuk AI
        $daftarSiswa = Siswa::whereNotNull('foto')->get(['id', 'nama_siswa', 'foto']);

        return view('presensi.scanner', compact('jadwalAktif', 'daftarSiswa'));
    }

    // FUNGSI BARU: Untuk menyimpan absen via AJAX (Face Recognition)
    public function store(Request $request)
    {
        $siswaId = $request->siswa_id;
        $jadwalId = $request->jadwal_id;

        if (!$jadwalId) {
            return response()->json(['status' => 'error', 'message' => 'Tidak ada jadwal aktif!'], 422);
        }

        // Cek apakah hari ini sudah absen di jadwal yang sama?
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