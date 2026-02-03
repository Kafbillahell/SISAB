<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Rombel;
use App\Models\Mapel;
use App\Models\Guru;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index()
    {
        // Mengurutkan berdasarkan hari dan jam mulai agar rapi di tabel
        $jadwals = Jadwal::with(['rombel', 'mapel', 'guru'])->orderBy('hari')->orderBy('jam_mulai')->get();
        $rombels = Rombel::all();
        $mapels = Mapel::all();
        $gurus = Guru::all();
        
        return view('jadwal.index', compact('jadwals', 'rombels', 'mapels', 'gurus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'rombel_id' => 'required',
            'mapel_id' => 'required',
            'guru_id' => 'required',
            'hari' => 'required',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
        ]);

        // 1. CEK BENTROK GURU (Apakah guru ini sudah ada jadwal lain di hari & jam yang sama?)
        $bentrokGuru = Jadwal::where('hari', $request->hari)
            ->where('guru_id', $request->guru_id)
            ->where(function($query) use ($request) {
                $query->whereBetween('jam_mulai', [$request->jam_mulai, $request->jam_selesai])
                      ->orWhereBetween('jam_selesai', [$request->jam_mulai, $request->jam_selesai])
                      // Cek jika jam input berada di dalam rentang jam yang sudah ada
                      ->orWhere(function($q) use ($request) {
                          $q->where('jam_mulai', '<=', $request->jam_mulai)
                            ->where('jam_selesai', '>=', $request->jam_selesai);
                      });
            })->first();

        if ($bentrokGuru) {
            return redirect()->back()->with('error', "Gagal! Guru ini sudah memiliki jadwal di hari {$request->hari} pada jam tersebut.");
        }

        // 2. CEK BENTROK ROMBEL (Apakah kelas ini sudah ada mapel lain di jam tersebut?)
        $bentrokRombel = Jadwal::where('hari', $request->hari)
            ->where('rombel_id', $request->rombel_id)
            ->where(function($query) use ($request) {
                $query->whereBetween('jam_mulai', [$request->jam_mulai, $request->jam_selesai])
                      ->orWhereBetween('jam_selesai', [$request->jam_mulai, $request->jam_selesai])
                      ->orWhere(function($q) use ($request) {
                          $q->where('jam_mulai', '<=', $request->jam_mulai)
                            ->where('jam_selesai', '>=', $request->jam_selesai);
                      });
            })->first();

        if ($bentrokRombel) {
            return redirect()->back()->with('error', "Gagal! Kelas tersebut sudah memiliki jadwal lain di jam tersebut.");
        }

        // Jika lolos semua pengecekan, baru simpan
        Jadwal::create($request->all());
        return redirect()->back()->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function destroy(Jadwal $jadwal)
    {
        try {
            $jadwal->delete();
            return redirect()->back()->with('success', 'Jadwal berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            // Menangani error foreign key (ada data presensi yang nyangkut)
            if ($e->getCode() == "23000") {
                return redirect()->back()->with('error', 'Gagal menghapus! Jadwal ini sudah memiliki data absensi siswa.');
            }

            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat menghapus data.');
        }
    }
}