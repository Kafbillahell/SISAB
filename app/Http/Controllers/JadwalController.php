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

        Jadwal::create($request->all());
        return redirect()->back()->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function destroy(Jadwal $jadwal)
    {
        $jadwal->delete();
        return redirect()->back()->with('success', 'Jadwal berhasil dihapus.');
    }
}