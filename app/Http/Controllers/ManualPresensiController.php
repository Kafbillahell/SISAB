<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rombel;
use App\Models\Guru;
use App\Models\AnggotaRombel;
use App\Models\Siswa;
use App\Models\Presensi;
use App\Models\Jadwal;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ManualPresensiController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $rombels = Rombel::all();
        $selectedRombel = null;

        // Jika guru (wali kelas), ambil rombel yang dia pegang
        if ($user && $user->role === 'guru') {
            $guru = Guru::where('user_id', $user->id)->first();
            if ($guru) {
                $selectedRombel = Rombel::where('guru_id', $guru->id)->first();
            }
        }

        // Jika admin atau request memilih rombel
        if ($request->filled('rombel_id')) {
            $selectedRombel = Rombel::find($request->rombel_id);
        }

        // Jika user adalah guru (wali kelas) dan memiliki rombel, otomatis buka kelasnya
        if ($user && $user->role === 'guru' && $selectedRombel && ! $request->filled('rombel_id')) {
            return redirect()->route('presensi.manual', ['rombel_id' => $selectedRombel->id]);
        }

        $siswas = collect();
        $jadwals = collect();
        $defaultJadwalId = null;

        if ($selectedRombel) {
            $siswas = DB::table('anggota_rombels')
                ->join('siswas', 'anggota_rombels.siswa_id', '=', 'siswas.id')
                ->where('anggota_rombels.rombel_id', $selectedRombel->id)
                ->select('siswas.id', 'siswas.nama_siswa', 'siswas.foto')
                ->get();

            // Hanya tampilkan jadwal yang berjalan hari ini untuk rombel tersebut
            $hariIndo = $this->getHariIndo(Carbon::now()->format('l'));

            $jadwalQuery = Jadwal::where('rombel_id', $selectedRombel->id)
                ->where('hari', $hariIndo)
                ->with('mapel', 'guru');

            // Jika user adalah guru, batasi ke jadwal yang dia ajarkan
            if ($user && $user->role === 'guru') {
                $guru = Guru::where('user_id', $user->id)->first();
                if ($guru) {
                    $jadwalQuery->where('guru_id', $guru->id);
                }
            }

            $jadwals = $jadwalQuery->get();
            $defaultJadwalId = $jadwals->first()->id ?? null;
        }

        return view('presensi.manual', compact('rombels', 'selectedRombel', 'siswas', 'jadwals', 'defaultJadwalId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'rombel_id' => 'required|exists:rombels,id',
            'tanggal' => 'required|date',
            'jadwal_id' => 'required|exists:jadwals,id',
            'status' => 'array',
        ]);

        $tanggal = Carbon::parse($request->tanggal)->startOfDay();
        $jadwalId = $request->jadwal_id;

        foreach ($request->input('status', []) as $siswaId => $stat) {
            if (empty($stat)) continue;

            // Upsert presensi untuk siswa, jadwal, dan tanggal yang sama
            $waktu = $tanggal->copy()->addHours(8); // jam default 08:00

            $existing = Presensi::where('siswa_id', $siswaId)
                ->where('jadwal_id', $jadwalId)
                ->whereDate('waktu_scan', $tanggal->format('Y-m-d'))
                ->first();

            if ($existing) {
                $existing->update([
                    'waktu_scan' => $waktu,
                    'status' => $stat,
                    'keterangan' => $stat
                ]);
            } else {
                Presensi::create([
                    'siswa_id' => $siswaId,
                    'jadwal_id' => $jadwalId,
                    'waktu_scan' => $waktu,
                    'status' => $stat,
                    'keterangan' => $stat
                ]);
            }
        }

        return redirect()->back()->with('success', 'Data presensi manual berhasil disimpan.');
    }

    private function getHariIndo($day)
    {
        $map = [
            'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'
        ];
        return $map[$day] ?? $day;
    }
}
