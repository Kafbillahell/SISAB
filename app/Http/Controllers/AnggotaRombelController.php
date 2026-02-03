<?php

namespace App\Http\Controllers;

use App\Models\AnggotaRombel;
use App\Models\Rombel;
use App\Models\Siswa;
use Illuminate\Http\Request;

class AnggotaRombelController extends Controller
{
    public function index(Request $request)
    {
        $rombels = Rombel::with(['kelas', 'tahunAjaran'])->get();
        $selectedRombel = null;
        $anggotas = [];
        $siswas = [];

        if ($request->rombel_id) {
            $selectedRombel = Rombel::findOrFail($request->rombel_id);
            $anggotas = AnggotaRombel::with('siswa')->where('rombel_id', $request->rombel_id)->get();
            
            // Ambil siswa yang BELUM masuk ke rombel mana pun di tahun ajaran yang sama
            // Untuk sementara, kita ambil yang belum jadi anggota di rombel ini saja
            $siswaIdsInRombel = AnggotaRombel::where('rombel_id', $request->rombel_id)->pluck('siswa_id');
            $siswas = Siswa::whereNotIn('id', $siswaIdsInRombel)->get();
        }

        return view('anggota_rombel.index', compact('rombels', 'selectedRombel', 'anggotas', 'siswas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'rombel_id' => 'required|exists:rombels,id',
            'siswa_id' => 'required|array',
        ]);

        foreach ($request->siswa_id as $id) {
            AnggotaRombel::create([
                'rombel_id' => $request->rombel_id,
                'siswa_id' => $id
            ]);
        }

        return redirect()->back()->with('success', 'Siswa berhasil ditambahkan ke Rombel.');
    }

    public function destroy($id)
    {
        $anggota = AnggotaRombel::findOrFail($id);
        $anggota->delete();
        return redirect()->back()->with('success', 'Siswa dikeluarkan dari Rombel.');
    }
}