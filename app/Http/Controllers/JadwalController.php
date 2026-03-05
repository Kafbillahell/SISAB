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
        
        // Mengambil data terbaru dari database
        $jadwals = Jadwal::with(['mapel', 'guru', 'rombel', 'sesi'])->get();
        $sesis = Sesi::orderBy('urutan', 'asc')->get(); 

        return view('jadwal.index', compact('rombels', 'jurusans', 'mapels', 'gurus', 'jadwals', 'sesis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'rombel_id' => 'required|exists:rombels,id',
            'mapel_id'  => 'required|exists:mapels,id',
            'guru_id'   => 'required|exists:gurus,id',
            'sesi_id'   => 'required|exists:sesis,id',
            'hari'      => 'required',
        ]);

        $sesiDipilih = Sesi::find($request->sesi_id);

        // Gunakan rentang waktu dari sesi untuk cek overlap (lebih aman daripada hanya sesi_id)
        $start = $sesiDipilih->jam_mulai;
        $end = $sesiDipilih->jam_selesai;

        // Cek bentrok Rombel (pada hari yang sama) berdasarkan overlap waktu
        $cekSlot = Jadwal::where('rombel_id', $request->rombel_id)
            ->where('hari', $request->hari)
            ->where(function($q) use ($start, $end) {
                $q->where(function($q2) use ($start, $end) {
                    $q2->where('jam_mulai', '<', $end)
                       ->where('jam_selesai', '>', $start);
                });
            })->first();

        if ($cekSlot) {
            return redirect()->route('jadwal.index')
                ->with(['error' => "Gagal! Rombel sudah memiliki jadwal yang waktunya bertumpuk.", 'open_rombel' => $request->rombel_id]);
        }

        // Cek bentrok Guru (pada hari yang sama) berdasarkan overlap waktu
        $bentrokGuru = Jadwal::where('hari', $request->hari)
            ->where('guru_id', $request->guru_id)
            ->where(function($q) use ($start, $end) {
                $q->where('jam_mulai', '<', $end)
                  ->where('jam_selesai', '>', $start);
            })->first();

        if ($bentrokGuru) {
            return redirect()->route('jadwal.index')
                ->with(['error' => "Gagal! Guru sudah terjadwal pada waktu yang sama.", 'open_rombel' => $request->rombel_id]);
        }

        Jadwal::create([
            'rombel_id' => $request->rombel_id,
            'mapel_id'  => $request->mapel_id,
            'guru_id'   => $request->guru_id,
            'sesi_id'   => $request->sesi_id,
            'hari'      => $request->hari,
            'jam_mulai' => $sesiDipilih->jam_mulai,
            'jam_selesai' => $sesiDipilih->jam_selesai,
        ]);

        // PENTING: Redirect ke Index (bukan back) agar query data terulang, 
        // dan bawa ID rombel di session agar JS otomatis membuka kelas tersebut
        return redirect()->route('jadwal.index')->with([
            'success' => 'Jadwal berhasil disimpan.',
            'open_rombel' => $request->rombel_id
        ]);
    }

    public function destroy(Jadwal $jadwal)
    {
        try {
            $rombelId = $jadwal->rombel_id; // Simpan ID sebelum dihapus
            $jadwal->delete();
            
            return redirect()->route('jadwal.index')->with([
                'success' => 'Jadwal berhasil dihapus.',
                'open_rombel' => $rombelId
            ]);
        } catch (\Exception $e) {
            return redirect()->route('jadwal.index')->with('error', 'Gagal menghapus data.');
        }
    }
}