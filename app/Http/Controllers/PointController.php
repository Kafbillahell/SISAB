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
        
        // Hitung breakdown poin (positif vs negatif) - gunakan collection helper
        $allPresensis = $siswa->presensis()->get();
        $positivePoints = (int)$allPresensis->where('points', '>', 0)->sum('points');
        $negativePoints = (int)$allPresensis->where('points', '<', 0)->sum(function($p) { return abs($p->points); });
        
        $presensis = $siswa->presensis()
            ->with('jadwal')
            ->orderBy('waktu_scan', 'desc')
            ->paginate(15);

        return view('pages.points.my-points', [
            'siswa' => $siswa,
            'totalPoints' => $totalPoints,
            'positivePoints' => $positivePoints,
            'negativePoints' => $negativePoints,
            'presensis' => $presensis,
        ]);
    }

    /**
     * Hitung dan tambahkan poin ke presensi berdasarkan jam
     * Tepat waktu: +10 poin (scan dalam 2 menit setelah jam mulai)
     * Terlambat: -5 poin (scan lebih dari 2 menit setelah jam mulai)
     * 
     * Contoh: Jam mulai 07:00
     * - Tepat waktu: scan antara 07:00 - 07:02 = +10 poin
     * - Terlambat: scan setelah 07:02 = -5 poin
     * 
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

        if ($presensi->waktu_scan && $jadwal->jam_mulai) {
            $waktuScan = \Carbon\Carbon::parse($presensi->waktu_scan);
            $jamMulai = \Carbon\Carbon::parse($jadwal->jam_mulai);
            
            // Batas akhir grace period (2 menit setelah jam mulai)
            $batasAkhirGracePeriod = $jamMulai->copy()->addMinutes(2);

            // Jika scan >= jam mulai DAN scan <= 2 menit setelah jam mulai = tepat waktu
            if ($waktuScan >= $jamMulai && $waktuScan <= $batasAkhirGracePeriod) {
                $presensi->points = $rule->points_on_time;
            } else {
                // Selain itu = terlambat
                $presensi->points = $rule->points_late;
            }
        }

        $presensi->save();
    }
}
