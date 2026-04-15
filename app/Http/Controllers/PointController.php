<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\PointRule;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PointController extends Controller
{
    /**
     * Menampilkan halaman poin saya untuk siswa
     */
    public function myPoints()
    {
        $siswa = Siswa::where('user_id', Auth::id())->first();

        if (!$siswa) {
            return redirect()->route('dashboard')->with('error', 'Data siswa tidak ditemukan');
        }

        $totalPoints = $siswa->getTotalPoints();
        $presensis = $siswa->presensis()
            ->with('jadwal')
            ->orderBy('tanggal', 'desc')
            ->paginate(15);

        return view('pages.points.my-points', [
            'siswa' => $siswa,
            'totalPoints' => $totalPoints,
            'presensis' => $presensis,
        ]);
    }

    /**
     * Hitung dan tambahkan poin ke presensi berdasarkan jam
     * (Dipanggil ketika presensi dibuat atau diupdate)
     */
    public static function calculatePoints(Presensi $presensi)
    {
        $jadwal = $presensi->jadwal;
        
        if (!$jadwal) {
            return;
        }

        // Ambil rule poin
        $rule = PointRule::first() ?? new PointRule([
            'points_late' => -5,
            'points_on_time' => 10,
        ]);

        // Jika waktu scan <= jam mulai, maka tepat waktu
        if ($presensi->waktu_scan && $jadwal->jam_mulai) {
            $waktuScan = \Carbon\Carbon::parse($presensi->waktu_scan);
            $jamMulai = \Carbon\Carbon::parse($jadwal->jam_mulai);

            if ($waktuScan <= $jamMulai) {
                $presensi->points = $rule->points_on_time;
            } else {
                $presensi->points = $rule->points_late;
            }
        }

        $presensi->save();
    }
}
