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
        $selectedRombel = null;

        // Tentukan daftar kelas yang boleh dipilih: jika guru -> hanya kelas yang dia wali atau mengajar
        if ($user && $user->role === 'guru') {
            $guru = Guru::where('user_id', $user->id)->first();
            if ($guru) {
                $waliIds = Rombel::where('guru_id', $guru->id)->pluck('id')->toArray();
                $teachingIds = Jadwal::where('guru_id', $guru->id)->pluck('rombel_id')->toArray();
                $ids = array_unique(array_merge($waliIds, $teachingIds));
                $rombels = Rombel::whereIn('id', $ids)->get();
            } else {
                $rombels = collect();
            }
        } else {
            $rombels = Rombel::all();
        }

        // Jika user adalah guru, cari informasi guru dan otomatis pilih rombel
        if ($user && $user->role === 'guru') {
            $guru = Guru::where('user_id', $user->id)->first();
            if ($guru) {
                // Prioritas: jika guru sedang mengajar sekarang, buka rombel tempat dia mengajar
                $now = Carbon::now()->setTimezone('Asia/Jakarta');
                $hariNow = $this->getHariIndo($now->format('l'));
                $timeNow = $now->format('H:i:s');

                $activeJadwal = Jadwal::where('guru_id', $guru->id)
                    ->where('hari', $hariNow)
                    ->where('jam_mulai', '<=', $timeNow)
                    ->where('jam_selesai', '>=', $timeNow)
                    ->first();

                if ($activeJadwal) {
                    $selectedRombel = Rombel::find($activeJadwal->rombel_id);
                } else {
                    // Jika tidak sedang mengajar, fallback ke rombel yang dia wali (jika ada)
                    $selectedRombel = Rombel::where('guru_id', $guru->id)->first();
                }
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

        // Pilihan tanggal (default hari ini) dan jadwal terpilih (dari request atau default)
        $selectedTanggal = $request->tanggal ?? date('Y-m-d');

        if ($selectedRombel) {
            $siswas = DB::table('anggota_rombels')
                ->join('siswas', 'anggota_rombels.siswa_id', '=', 'siswas.id')
                ->where('anggota_rombels.rombel_id', $selectedRombel->id)
                ->select('siswas.id', 'siswas.nama_siswa', 'siswas.foto')
                ->get();

            // Hanya tampilkan jadwal yang berjalan pada tanggal yang dipilih untuk rombel tersebut
            $hariIndo = $this->getHariIndo(Carbon::parse($selectedTanggal)->format('l'));

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

            // Tentukan apakah jadwal harus dikunci untuk guru yang sedang mengajar
            $jadwal_locked = false;
            $lockedJadwalId = null;
            if ($user && $user->role === 'guru') {
                // Jika tanggal yang dipilih adalah hari ini, cek jadwal yang sedang berjalan berdasarkan waktu
                if ($selectedTanggal == date('Y-m-d')) {
                    $nowTime = Carbon::now()->setTimezone('Asia/Jakarta')->format('H:i:s');
                    $active = $jadwals->first(function($j) use ($nowTime) {
                        return ($j->jam_mulai <= $nowTime) && ($j->jam_selesai >= $nowTime);
                    });
                    if ($active) {
                        $jadwal_locked = true;
                        $lockedJadwalId = $active->id;
                    }
                }

                // Jika tidak ada jadwal aktif tetapi hanya ada satu jadwal di daftar, kunci ke jadwal itu
                if (! $jadwal_locked && $jadwals->count() == 1) {
                    $jadwal_locked = true;
                    $lockedJadwalId = $jadwals->first()->id;
                }
            } else {
                $jadwal_locked = false;
            }
        }

        // Pilihan tanggal (default hari ini) dan jadwal terpilih (dari request atau default)
        $selectedTanggal = $request->tanggal ?? date('Y-m-d');
        $selectedJadwal = $request->jadwal_id ?? $defaultJadwalId;
        // Jika jadwal dikunci, gunakan jadwal yang dikunci
        if (isset($jadwal_locked) && $jadwal_locked && isset($lockedJadwalId)) {
            $selectedJadwal = $lockedJadwalId;
        }

        // Ambil status presensi yang sudah tersimpan untuk tanggal dan jadwal tersebut
        $presensiStatuses = [];
        if ($selectedJadwal) {
            $presensiStatuses = Presensi::where('jadwal_id', $selectedJadwal)
                ->whereDate('waktu_scan', $selectedTanggal)
                ->pluck('status', 'siswa_id')
                ->toArray();
        }

        return view('presensi.manual', compact(
            'rombels', 'selectedRombel', 'siswas', 'jadwals', 'defaultJadwalId', 'selectedTanggal', 'selectedJadwal', 'presensiStatuses', 'jadwal_locked', 'lockedJadwalId'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'rombel_id' => 'required|exists:rombels,id',
            'tanggal' => 'required|date',
            'jadwal_id' => 'nullable|exists:jadwals,id',
            'status' => 'array',
        ]);

        $tanggal = Carbon::parse($request->tanggal)->startOfDay();
        $jadwalId = $request->jadwal_id;

        // Jika jadwal_id tidak diberikan, coba ambil jadwal hari ini untuk rombel dan guru (jika wali)
        if (empty($jadwalId)) {
            $hariIndo = $this->getHariIndo($tanggal->format('l'));
            $jadwalQuery = Jadwal::where('rombel_id', $request->rombel_id)
                ->where('hari', $hariIndo);

            $user = auth()->user();
            if ($user && $user->role === 'guru') {
                $guru = Guru::where('user_id', $user->id)->first();
                if ($guru) $jadwalQuery->where('guru_id', $guru->id);
            }

            $jadwal = $jadwalQuery->first();
            if ($jadwal) $jadwalId = $jadwal->id;
        }

        if (empty($jadwalId)) {
            return redirect()->back()->withInput()->withErrors(['jadwal_id' => 'Tidak ada jadwal yang cocok untuk tanggal/kelas ini. Silakan pilih jadwal terlebih dahulu.']);
        }

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
