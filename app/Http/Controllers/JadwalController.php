<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Rombel;
use App\Models\Mapel;
use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\Sesi;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index() 
{
    $rombels = Rombel::with('jurusan', 'kelas')->get();
    $jurusans = Jurusan::all();
    $mapels = Mapel::all();
    $gurus = Guru::all();
    
    // Ambil jadwal dengan relasi
    $jadwals = Jadwal::with(['mapel', 'guru', 'rombel', 'sesi'])->get();

    // PERBAIKAN: Jangan pakai orderBy('hari') karena kolom hari sudah dihapus dari tabel sesis
    $sesis = Sesi::orderBy('urutan', 'asc')->get(); 

    return view('jadwal.index', compact('rombels', 'jurusans', 'mapels', 'gurus', 'jadwals', 'sesis'));
}

    public function store(Request $request)
    {
        // PERBAIKAN: Gunakan sesi_id, bukan jam manual
        $request->validate([
            'rombel_id' => 'required|exists:rombels,id',
            'mapel_id'  => 'required|exists:mapels,id',
            'guru_id'   => 'required|exists:gurus,id',
            'sesi_id'   => 'required|exists:sesis,id',
            'hari'      => 'required',
        ]);

        // Ambil data sesi untuk validasi bentrok jam
        $sesiDipilih = Sesi::find($request->sesi_id);

        // 1. Cek apakah di Rombel tersebut pada Sesi tersebut sudah ada mapel
        $cekSlot = Jadwal::where('rombel_id', $request->rombel_id)
            ->where('sesi_id', $request->sesi_id)
            ->where('hari', $request->hari)
            ->first();

        if ($cekSlot) {
            return back()->with('error', "Gagal! Rombel ini sudah memiliki jadwal di sesi tersebut.");
        }

        // 2. Cek apakah Guru sedang mengajar di tempat lain pada jam yang sama
        $bentrokGuru = Jadwal::where('hari', $request->hari)
            ->where('guru_id', $request->guru_id)
            ->where('sesi_id', $request->sesi_id)
            ->first();

        if ($bentrokGuru) {
            return back()->with('error', "Gagal! Guru ini sudah terjadwal di kelas lain pada jam yang sama.");
        }

        // Simpan data
        Jadwal::create([
            'rombel_id' => $request->rombel_id,
            'mapel_id'  => $request->mapel_id,
            'guru_id'   => $request->guru_id,
            'sesi_id'   => $request->sesi_id,
            'hari'      => $request->hari,
            // Jika database Anda masih butuh kolom jam_mulai/selesai, ambil dari $sesiDipilih
            'jam_mulai' => $sesiDipilih->jam_mulai,
            'jam_selesai' => $sesiDipilih->jam_selesai,
        ]);

        return redirect()->back()->with('success', 'Jadwal berhasil disimpan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'hari'      => 'required',
            'sesi_id'   => 'required|exists:sesis,id',
            'rombel_id' => 'required',
            'mapel_id'  => 'required',
            'guru_id'   => 'required',
        ]);

        $sesi = Sesi::find($request->sesi_id);
        $jadwal = Jadwal::findOrFail($id);

        $jadwal->update([
            'hari'        => $request->hari,
            'sesi_id'     => $request->sesi_id,
            'jam_mulai'   => $sesi->jam_mulai,
            'jam_selesai' => $sesi->jam_selesai,
            'rombel_id'   => $request->rombel_id,
            'mapel_id'    => $request->mapel_id,
            'guru_id'     => $request->guru_id,
        ]);

        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil diperbarui!');
    }

    public function destroy(Jadwal $jadwal)
    {
        try {
            $jadwal->delete();
            return redirect()->back()->with('success', 'Jadwal berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus! Data ini mungkin terhubung dengan presensi.');
        }
    }
}