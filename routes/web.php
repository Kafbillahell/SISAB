<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\TahunAjaranController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\MapelController; 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RombelController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\AnggotaRombelController;
use App\Http\Controllers\PresensiController;

Route::middleware(['auth'])->group(function () {
    Route::get('/presensi', [PresensiController::class, 'index'])->name('presensi.index');
    Route::get('/presensi/scanner', [PresensiController::class, 'scanner'])->name('presensi.scanner');
    Route::post('/presensi/store', [PresensiController::class, 'store'])->name('presensi.store');
});

// Dalam group auth


// Route Login
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Group Route yang butuh Login
Route::middleware(['auth'])->group(function () {
    Route::get('/', function () { return view('pages.index'); });
    Route::get('/dashboard', function () { return view('pages.index'); })->name('dashboard'); // Pastikan dashboard punya nama rute
    
    // Manajemen User & Personalia
    Route::resource('users', UserController::class);
    Route::resource('guru', GuruController::class);
    Route::resource('siswas', SiswaController::class);
    
    
    // Data Akademik
    Route::resource('tahun-ajaran', TahunAjaranController::class)->except(['create', 'edit', 'update']);
    Route::post('tahun-ajaran/{id}/activate', [TahunAjaranController::class, 'activate'])->name('tahun-ajaran.activate');
    Route::resource('kelas', KelasController::class);
    Route::resource('mapel', MapelController::class); 
    Route::resource('rombel', RombelController::class);
    Route::resource('jadwal', JadwalController::class); 
    Route::resource('anggota-rombel', AnggotaRombelController::class)->except(['edit', 'update', 'show']);



     
});