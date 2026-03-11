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
Route::get('/siswas/search-api/{nisn}', [SiswaController::class, 'searchByNisn'])->name('siswas.searchApi');

// --- Route Guest (Belum Login) ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// --- Route Authenticated (Sudah Login) ---
Route::middleware(['auth'])->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/', function () { return view('pages.index'); });
    Route::get('/dashboard', function () { return view('pages.index'); })->name('dashboard');

    // --- Manajemen Guru (DIPERBAIKI: Hanya didefinisikan sekali di sini) ---
    Route::get('/guru/refresh', function() {
        \Illuminate\Support\Facades\Cache::forget('data_guru_api_2025');
        return redirect()->route('guru.index')->with('success', 'Data Cloud diperbarui!');
    })->name('guru.refresh');
    Route::get('/guru/sync/{nip}', [GuruController::class, 'sync'])->name('guru.sync');
    Route::resource('guru', GuruController::class);

    // --- Manajemen Siswa ---
    Route::get('/siswas/sync/{nisn}', [SiswaController::class, 'sync'])->name('siswas.sync'); 
    Route::resource('siswas', SiswaController::class);

    // --- Manajemen User & Sesi ---
    Route::resource('users', UserController::class);
    Route::resource('sesi', SesiController::class);

    // --- Data Akademik ---
    Route::resource('tahun-ajaran', TahunAjaranController::class)->except(['create', 'edit', 'update']);
    Route::post('tahun-ajaran/{id}/activate', [TahunAjaranController::class, 'activate'])->name('tahun-ajaran.activate');
    
    // Jurusan
    Route::get('/jurusan', [JurusanController::class, 'index'])->name('jurusan.index');
    Route::post('/jurusan/store', [JurusanController::class, 'store'])->name('jurusan.store');
    Route::put('/jurusan/{jurusan}', [JurusanController::class, 'update'])->name('jurusan.update');
    Route::delete('/jurusan/{jurusan}', [JurusanController::class, 'destroy'])->name('jurusan.destroy');

    // Kelas, Mapel, Rombel
    Route::resource('kelas', KelasController::class);
    Route::resource('mapel', MapelController::class); 
    Route::resource('rombel', RombelController::class);
    Route::resource('anggota-rombel', AnggotaRombelController::class)->except(['edit', 'update', 'show']);

    // --- Absensi & Jadwal ---
    Route::resource('jadwal', JadwalController::class); 
    Route::get('/presensi', [PresensiController::class, 'index'])->name('presensi.index');
    Route::get('/presensi/scanner', [PresensiController::class, 'scanner'])->name('presensi.scanner');
    Route::post('/presensi/store', [PresensiController::class, 'store'])->name('presensi.store');
    Route::get('/presensi/daftar-siswa/{rombel}', [PresensiController::class, 'daftarSiswa'])->name('presensi.daftarSiswa');
    
    // Manual input menggunakan import controller di atas
    Route::get('/presensi/manual', [ManualPresensiController::class, 'index'])->name('presensi.manual');
    Route::post('/presensi/manual', [ManualPresensiController::class, 'store'])->name('presensi.manual.store');

    // --- Penilaian Sikap ---
    Route::get('/penilaian-sikap', [\App\Http\Controllers\PenilaianSikapController::class, 'index'])->name('penilaian-sikap.index');
    Route::post('/penilaian-sikap/store-massal', [\App\Http\Controllers\PenilaianSikapController::class, 'storeMassal'])->name('penilaian-sikap.storeMassal');
    Route::get('/penilaian-sikap/{siswa_id}/form', [\App\Http\Controllers\PenilaianSikapController::class, 'form'])->name('penilaian-sikap.form');
    Route::post('/penilaian-sikap/{siswa_id}/store', [\App\Http\Controllers\PenilaianSikapController::class, 'store'])->name('penilaian-sikap.store');
    Route::get('/penilaian-sikap/{siswa_id}/show', [\App\Http\Controllers\PenilaianSikapController::class, 'show'])->name('penilaian-sikap.show');

    // --- Manajemen Periode ---
    Route::resource('periode', PeriodeController::class)->except(['create', 'show', 'edit']);

    // --- Pengaturan Lokasi (GPS) ---
    Route::get('/settings/lokasi', [SettingController::class, 'index'])->name('settings.lokasi');
    Route::post('/settings/lokasi', [SettingController::class, 'update'])->name('settings.lokasi.update');
});