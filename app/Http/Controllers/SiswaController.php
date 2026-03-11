<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SiswaController extends Controller
{
    // Fungsi private pembantu: Mengambil data siswa dari API eksternal (ZieLabs) dengan Cache & Timeout lebih panjang
    // Digunakan oleh method index(), searchByNisn(), dan sync() untuk mencegah request berulang
    private function getSiswaFromApi()
    {
        // Simpan data selama 30 menit (1800 detik)
        return Cache::remember('data_siswa_api_2025', 1800, function () {
            // Naikkan timeout ke 120 detik karena payload data 1.6MB+ sangat besar
            $response = Http::timeout(120)->get('https://zieapi.zielabs.id/api/getsiswa?tahun=2025');
            
            if ($response->successful()) {
                return $response->json()['data'] ?? [];
            }
            
            return [];
        });
    }

    // Menampilkan halaman daftar siswa yang diambil dari API
    // Termasuk logika filter pencarian berdasarkan nama, NISN, jurusan, rombel, dan jenis kelamin
    public function index(Request $request)
    {
        // 1. Ambil data (dari Cache jika sudah pernah didownload)
        $allSiswas = $this->getSiswaFromApi();
        $collection = collect($allSiswas);

        // 2. Ambil list untuk dropdown filter
        $list_jurusan = $collection->pluck('jurusan')->unique()->filter()->sort()->values();
        $list_kelas = $collection->pluck('nama_rombel')->unique()->filter()->sort()->values();

        // 3. Logika Filtering
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $collection = $collection->filter(function($item) use ($search) {
                return str_contains(strtolower($item['nama'] ?? ''), $search) || 
                       str_contains(strtolower($item['nisn'] ?? ''), $search);
            });
        }

        if ($request->filled('jurusan')) {
            $collection = $collection->where('jurusan', $request->jurusan);
        }

        if ($request->filled('kelas')) {
            $collection = $collection->where('nama_rombel', $request->kelas);
        }

        if ($request->filled('jk')) {
            $collection = $collection->filter(function($item) use ($request) {
                $jk = $item['jk'] ?? ($item['jenis_kelamin'] ?? '');
                return $jk == $request->jk;
            });
        }

        $siswas = $collection->all();

        return view('siswas.index', compact('siswas', 'list_jurusan', 'list_kelas'));
    }

    // Menampilkan halaman form pendaftaran wajah siswa (menggunakan antarmuka kamera scanner)
    public function create()
    {
        return view('siswas.create');
    }

    // Memproses dan menyimpan data pendaftaran siswa baru beserta file foto wajah hasil scan
    // Sekaligus membuat akun login (User) untuk siswa tersebut pada sistem
    public function store(Request $request)
    {
        $request->validate([
            'nisn'          => 'required|unique:siswas,nisn',
            'nama_siswa'    => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'foto'          => 'required|image|mimes:jpeg,png,jpg|max:2048', 
        ]);

        try {
            DB::beginTransaction();

            // 1. Buat Akun User
            $user = User::create([
                'name'     => $request->nama_siswa,
                'email'    => $request->nisn . '@student.sch.id',
                'password' => Hash::make($request->nisn),
                'role'     => 'siswa',
            ]);

            // 2. Simpan Foto
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('foto_siswa', 'public');
            }

            // 3. Simpan ke tabel Siswa
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

    // Berfungsi sebagai endpoint AJAX untuk mencari rincian data API berdasarkan NISN (mengambil dari cache)
    public function searchByNisn($nisn)
    {
        $allSiswas = $this->getSiswaFromApi();
        $data = collect($allSiswas)->firstWhere('nisn', $nisn);

        if ($data) {
            return response()->json([
                'success' => true,
                'nama' => $data['nama'],
                'jk'   => $data['jk'] ?? ($data['jenis_kelamin'] ?? 'L')
            ]);
        }

        return response()->json(['success' => false], 404);
    }

    // Mengarahkan ke halaman registrasi wajah/akun, dengan secara otomatis mengisi (pre-fill) data
    // menggunakan informasi siswa yang ada di API berdasarkan NISN
    public function sync($nisn)
    {
        // Ambil data dari Cache (Instan, tidak download ulang)
        $allSiswas = $this->getSiswaFromApi();
        $siswa = collect($allSiswas)->firstWhere('nisn', $nisn);

        if (!$siswa) {
            return redirect()->route('siswas.index')->with('error', 'Data siswa tidak ditemukan di API.');
        }

        return view('siswas.create', [
            'nisn' => $nisn,
            'nama' => $siswa['nama'],
            'jk'   => $siswa['jk'] ?? ($siswa['jenis_kelamin'] ?? 'L')
        ]);
    }
}