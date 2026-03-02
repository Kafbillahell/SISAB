<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class GuruController extends Controller
{
    public function index(Request $request)
{
    // Mengambil data real-time dari API
    try {
        $response = Http::timeout(10)->get('https://zieapi.zielabs.id/api/getguru?tahun=2025');
        $json = $response->json();
        
        // Memastikan mengambil array data yang benar
        $allGurus = $json['data'] ?? ($json['results'] ?? $json);
    } catch (\Exception $e) {
        $allGurus = [];
    }

    $collection = collect($allGurus);

    // Filter Pencarian
    if ($request->filled('search')) {
        $search = strtolower($request->search);
        $collection = $collection->filter(function($item) use ($search) {
            $nama = strtolower($item['nama_guru'] ?? $item['nama'] ?? '');
            $nip = strtolower($item['nip'] ?? '');
            return str_contains($nama, $search) || str_contains($nip, $search);
        });
    }

    // Filter Jenis Kelamin
    if ($request->filled('jk')) {
        $collection = $collection->filter(function($item) use ($request) {
            $jk = strtoupper($item['jk'] ?? ($item['jenis_kelamin'] ?? ''));
            return $jk === strtoupper($request->jk);
        });
    }

    // Mengambil NIP lokal untuk pengecekan status
    $nipTerdaftar = Guru::pluck('nip')->toArray();
    $gurus = $collection->all();

    return view('guru.index', compact('gurus', 'nipTerdaftar'));
}

    public function sync($nip)
{
    try {
        // Ambil data dari API
        $response = Http::timeout(10)->get('https://zieapi.zielabs.id/api/getguru?tahun=2025');
        $json = $response->json();

        // Cari data guru secara fleksibel di berbagai kemungkinan key JSON
        $allGurus = $json['data'] ?? ($json['results'] ?? $json);
        $dataApi = collect($allGurus)->firstWhere('nip', $nip);

        // Jika tetap tidak ketemu, coba bersihkan spasi (antisipasi data kotor di API)
        if (!$dataApi) {
            $dataApi = collect($allGurus)->first(function($item) use ($nip) {
                return trim($item['nip'] ?? '') == trim($nip);
            });
        }

        if (!$dataApi) {
            return redirect()->route('guru.index')->with('error', 'NIP ' . $nip . ' tidak ditemukan dalam respon API ZieLabs.');
        }

        DB::beginTransaction();

        // Gunakan NIP sebagai email dasar, pastikan unik
        $email = ($dataApi['nip'] ?? 'guru_' . uniqid()) . '@smkn1cianjur.sch.id';
        
        // Buat Akun User (atau update jika sudah ada user dengan email tersebut)
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name'     => $dataApi['nama_guru'] ?? ($dataApi['nama'] ?? 'Guru Baru'),
                'password' => Hash::make($dataApi['nip'] ?? '12345678'),
                'role'     => 'guru',
            ]
        );

        // Simpan/Update Data Guru
        Guru::updateOrCreate(
            ['nip' => $nip],
            [
                'user_id'       => $user->id,
                'nama_guru'     => $dataApi['nama_guru'] ?? ($dataApi['nama'] ?? 'Guru Baru'),
                'jenis_kelamin' => strtoupper($dataApi['jk'] ?? ($dataApi['jenis_kelamin'] ?? 'L')),
            ]
        );

        DB::commit();
        return redirect()->route('guru.index')->with('success', 'Berhasil meregistrasi: ' . ($dataApi['nama_guru'] ?? $nip));

    } catch (\Exception $e) {
        DB::rollback();
        return redirect()->route('guru.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}

    public function destroy(Guru $guru)
    {
        $user = User::find($guru->user_id);
        if ($user) $user->delete();
        $guru->delete();
        return redirect()->route('guru.index')->with('success', 'Data berhasil dihapus.');
    }
}