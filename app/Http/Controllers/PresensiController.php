<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\Jadwal;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PresensiController extends Controller
{
    // Menampilkan Rekap Absensi
    public function index()
    {
        $presensis = Presensi::with(['siswa', 'jadwal.mapel', 'jadwal.rombel'])
                    ->orderBy('waktu_scan', 'desc')
                    ->get();
        return view('presensi.index', compact('presensis'));
    }

    // Tampilan Kamera Scanner
    public function scanner()
    {
        // Mencari jadwal yang sedang berlangsung saat ini berdasarkan hari dan jam
        $hariIni = Carbon::now()->translatedFormat('l'); // Senin, Selasa, dst
        $jamSekarang = Carbon::now()->format('H:i:s');

        $jadwalAktif = Jadwal::where('hari', $hariIni)
            ->where('jam_mulai', '<=', $jamSekarang)
            ->where('jam_selesai', '>=', $jamSekarang)
            ->with(['mapel', 'rombel'])
            ->first();

        return view('presensi.scanner', compact('jadwalAktif'));
    }
}