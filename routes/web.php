<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    SiswaController,
    UserController,
    GuruController,
    TahunAjaranController,
    KelasController,
    MapelController,
    RombelController,
    JadwalController,
    AnggotaRombelController,
    PresensiController,
    JurusanController,
    SesiController,
    SettingController,
    ManualPresensiController,
    PeriodeController
};

// --- Rute API AJAX ---
// Endpoint untuk mencari data siswa berdasarkan NISN melalui AJAX
Route::get('/siswas/search-api/{nisn}', [SiswaController::class, 'searchByNisn'])->name('siswas.searchApi');

// --- Route Guest (Belum Login) ---
// Rute yang hanya bisa diakses oleh pengguna yang belum login (tamu)
Route::middleware('guest')->group(function () {
    // Menampilkan halaman form login
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    // Memproses data login yang dikirimkan melalui form
    Route::post('/login', [AuthController::class, 'login']);
});

// --- Route Authenticated (Sudah Login) ---
// Rute yang hanya bisa diakses setelah pengguna berhasil login
Route::middleware(['auth'])->group(function () {
    
    // Memproses permintaan logout pengguna
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard
    // Menampilkan halaman utama atau dashboard aplikasi
    Route::get('/', function () { return view('pages.index'); });
    Route::get('/dashboard', function () { return view('pages.index'); })->name('dashboard');

    // --- Manajemen Guru (DIPERBAIKI: Hanya didefinisikan sekali di sini) ---
    // Segarkan data guru dari API atau cloud dan hapus cache
    Route::get('/guru/refresh', function() {
        \Illuminate\Support\Facades\Cache::forget('data_guru_api_2025');
        return redirect()->route('guru.index')->with('success', 'Data Cloud diperbarui!');
    })->name('guru.refresh');
    // Sinkronisasi data guru spesifik berdasarkan NIP dari sumber eksternal
    Route::get('/guru/sync/{nip}', [GuruController::class, 'sync'])->name('guru.sync');
    // Rute resource CRUD lengkap untuk entri Guru (create, read, update, delete)
    Route::resource('guru', GuruController::class);

    // --- Manajemen Siswa ---
    // Sinkronisasi data siswa spesifik berdasarkan NISN dari sumber eksternal
    Route::get('/siswas/sync/{nisn}', [SiswaController::class, 'sync'])->name('siswas.sync'); 
    // Rute resource CRUD lengkap untuk entri Siswa
    Route::resource('siswas', SiswaController::class);

    // --- Manajemen User & Sesi ---
    // Rute resource CRUD lengkap untuk entri User (pengguna sistem)
    Route::resource('users', UserController::class);
    // Rute resource CRUD lengkap untuk manajemen Sesi
    Route::resource('sesi', SesiController::class);

    // --- Data Akademik ---
    // Rute resource CRUD untuk Tahun Ajaran (pengecualian create, edit, update)
    Route::resource('tahun-ajaran', TahunAjaranController::class)->except(['create', 'edit', 'update']);
    // Rute khusus untuk mengaktifkan tahun ajaran tertentu
    Route::post('tahun-ajaran/{id}/activate', [TahunAjaranController::class, 'activate'])->name('tahun-ajaran.activate');
    
    // Jurusan
    // Menampilkan daftar data Jurusan
    Route::get('/jurusan', [JurusanController::class, 'index'])->name('jurusan.index');
    // Menyimpan data Jurusan baru yang ditambahkan
    Route::post('/jurusan/store', [JurusanController::class, 'store'])->name('jurusan.store');
    // Memperbarui data Jurusan yang sudah ada berdasarkan ID
    Route::put('/jurusan/{jurusan}', [JurusanController::class, 'update'])->name('jurusan.update');
    // Menghapus data Jurusan berdasarkan ID
    Route::delete('/jurusan/{jurusan}', [JurusanController::class, 'destroy'])->name('jurusan.destroy');

    // Kelas, Mapel, Rombel
    // Rute resource CRUD lengkap untuk data Kelas
    Route::resource('kelas', KelasController::class);
    // Rute resource CRUD lengkap untuk data Mata Pelajaran (Mapel)
    Route::resource('mapel', MapelController::class); 
    // Rute resource CRUD lengkap untuk data Rombongan Belajar (Rombel)
    Route::resource('rombel', RombelController::class);
    // Rute resource untuk mengelola Anggota Rombel (pengecualian edit, update, show)
    Route::resource('anggota-rombel', AnggotaRombelController::class)->except(['edit', 'update', 'show']);

    // --- Absensi & Jadwal ---
    // Rute resource CRUD lengkap untuk Jadwal pelajaran
    Route::resource('jadwal', JadwalController::class); 
    // Menampilkan halaman utama presensi
    Route::get('/presensi', [PresensiController::class, 'index'])->name('presensi.index');
    // Menampilkan halaman scanner barcode/QR untuk presensi
    Route::get('/presensi/scanner', [PresensiController::class, 'scanner'])->name('presensi.scanner');
    // Menyimpan data presensi yang masuk
    Route::post('/presensi/store', [PresensiController::class, 'store'])->name('presensi.store');
    // Menampilkan daftar siswa untuk dikelola presensinya berdasarkan rombel
    Route::get('/presensi/daftar-siswa/{rombel}', [PresensiController::class, 'daftarSiswa'])->name('presensi.daftarSiswa');
    
    // Manual input menggunakan import controller di atas
    // Menampilkan halaman presensi input secara manual
    Route::get('/presensi/manual', [ManualPresensiController::class, 'index'])->name('presensi.manual');
    // Menyimpan data presensi yang diinput manual
    Route::post('/presensi/manual', [ManualPresensiController::class, 'store'])->name('presensi.manual.store');

    // --- Penilaian Sikap ---
    // Menampilkan daftar penilaian sikap (index)
    Route::get('/penilaian-sikap', [\App\Http\Controllers\PenilaianSikapController::class, 'index'])->name('penilaian-sikap.index');
    // Menyimpan data penilaian sikap secara massal (banyak siswa sekaligus)
    Route::post('/penilaian-sikap/store-massal', [\App\Http\Controllers\PenilaianSikapController::class, 'storeMassal'])->name('penilaian-sikap.storeMassal');
    // Menampilkan form input penilaian sikap untuk siswa tertentu
    Route::get('/penilaian-sikap/{siswa_id}/form', [\App\Http\Controllers\PenilaianSikapController::class, 'form'])->name('penilaian-sikap.form');
    // Menyimpan data penilaian sikap satuan untuk siswa spesifik
    Route::post('/penilaian-sikap/{siswa_id}/store', [\App\Http\Controllers\PenilaianSikapController::class, 'store'])->name('penilaian-sikap.store');
    // Menampilkan detail penilaian sikap seorang siswa
    Route::get('/penilaian-sikap/{siswa_id}/show', [\App\Http\Controllers\PenilaianSikapController::class, 'show'])->name('penilaian-sikap.show');

    // --- Manajemen Periode ---
    // Rute resource untuk mengelola Periode kegiatan (pengecualian create, show, edit)
    Route::resource('periode', PeriodeController::class)->except(['create', 'show', 'edit']);

    // --- Pengaturan Lokasi (GPS) ---
    // Menampilkan halaman pengaturan lokasi kantor/sekolah (GPS)
    Route::get('/settings/lokasi', [SettingController::class, 'index'])->name('settings.lokasi');
    // Menyimpan perubahan pengaturan lokasi
    Route::post('/settings/lokasi', [SettingController::class, 'update'])->name('settings.lokasi.update');
});