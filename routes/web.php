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
    SesiController
};

// --- Rute API AJAX (Bisa diakses login/tidak sesuai kebutuhan) ---
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

    // --- Manajemen Siswa (Custom Routes harus di atas Resource) ---
    // Rute Sync jika Anda menggunakan fungsi sinkronisasi otomatis
    Route::get('/siswas/sync/{nisn}', [SiswaController::class, 'sync'])->name('siswas.sync'); 
    Route::resource('siswas', SiswaController::class);

    // --- Manajemen User & Personalia ---
    Route::resource('users', UserController::class);
    Route::resource('guru', GuruController::class);
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
});