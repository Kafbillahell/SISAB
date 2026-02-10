<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\User;
use App\Models\Rombel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SiswaController extends Controller
{
    /**
     * Menampilkan daftar siswa dari API ZieLabs
     */
    public function index(Request $request)
    {
        $siswas = Cache::remember('data_siswa_api_2025', 1800, function () {
            $response = Http::timeout(60)->get('https://zieapi.zielabs.id/api/getsiswa?tahun=2025');
            return $response->successful() ? ($response->json()['data'] ?? $response->json()) : [];
        });

        if ($request->has('search')) {
            $search = strtolower($request->search);
            $siswas = array_filter($siswas, function($item) use ($search) {
                return str_contains(strtolower($item['nama'] ?? ''), $search) || 
                       str_contains(strtolower($item['nisn'] ?? ''), $search);
            });
        }

        return view('siswas.index', compact('siswas'));
    }

    /**
     * Halaman form pendaftaran wajah (Scanner)
     */
    public function create()
    {
        return view('siswas.create');
    }

    /**
     * Simpan data siswa dan foto wajah hasil scan
     */
    public function store(Request $request)
    {
        $request->validate([
            'nisn'          => 'required|unique:siswas,nisn',
            'nama_siswa'    => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'foto'          => 'required|image|mimes:jpeg,png,jpg|max:2048', // Wajib dari scanner
        ]);

        try {
            DB::beginTransaction();

            // 1. Buat Akun User untuk login Siswa
            $user = User::create([
                'name'     => $request->nama_siswa,
                'email'    => $request->nisn . '@student.sch.id', // Email otomatis dari NISN
                'password' => Hash::make($request->nisn),         // Password default NISN
                'role'     => 'siswa',
            ]);

            // 2. Simpan Foto Wajah
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('foto_siswa', 'public');
            }

            // 3. Simpan ke tabel Siswa lokal
            Siswa::create([
                'user_id'       => $user->id,
                'nisn'          => $request->nisn,
                'nama_siswa'    => $request->nama_siswa,
                'jenis_kelamin' => $request->jenis_kelamin,
                'foto'          => $fotoPath,
            ]);

            DB::commit();
            return redirect()->route('siswas.index')->with('success', 'Wajah siswa berhasil didaftarkan!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal mendaftar: ' . $e->getMessage());
        }
    }

    /**
     * Fungsi Tambahan: Mencari data ke API ZieLabs berdasarkan NISN (AJAX)
     * Gunakan ini di halaman Create agar Nama & JK terisi otomatis
     */
    public function searchByNisn($nisn)
    {
        $response = Http::get("https://zieapi.zielabs.id/api/getsiswa?tahun=2025");
        $data = collect($response->json()['data'] ?? [])->firstWhere('nisn', $nisn);

        if ($data) {
            return response()->json([
                'success' => true,
                'nama' => $data['nama'],
                'jk'   => $data['jk'] ?? 'L'
            ]);
        }

        return response()->json(['success' => false]);
    }

    /**
     * Mengarahkan ke halaman registrasi wajah dengan data dari API
     */
    public function sync($nisn)
    {
        // 1. Ambil data dari Cache/API untuk memastikan siswa ada
        $response = Http::get("https://zieapi.zielabs.id/api/getsiswa?tahun=2025");
        $siswa = collect($response->json()['data'] ?? [])->firstWhere('nisn', $nisn);

        if (!$siswa) {
            return redirect()->route('siswas.index')->with('error', 'Data siswa tidak ditemukan di API.');
        }

        // 2. Arahkan ke halaman create (scanner wajah) sambil membawa data NISN, Nama, dan JK
        // Ini akan mempermudah form create terisi otomatis melalui query string
        return view('siswas.create', [
            'nisn' => $nisn,
            'nama' => $siswa['nama'],
            'jk'   => $siswa['jk'] ?? ($siswa['jenis_kelamin'] ?? 'L')
        ]);
    }
}