{{-- 
    File: guru/index.blade.php
    Fungsi: Menampilkan halaman utama untuk memanajemen data sinkronisasi API guru secara keseluruhan.
    Dilengkapi tabel pendaftaran (registrasi) terstruktur, filter pencarian, dan alat identifikasi akun.
--}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Manajemen Guru</h1>


    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- CARD FILTER --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter mr-2"></i>Filter Data Guru</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('guru.index') }}" method="GET" class="row">
                <div class="col-md-6 mb-3">
                    <label class="small font-weight-bold">Cari Nama/NIP</label>
                    <input type="text" name="search" class="form-control" placeholder="Ketik nama atau NIP..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="small font-weight-bold">L/P</label>
                    <select name="jk" class="form-control">
                        <option value="">Semua</option>
                        <option value="L" {{ request('jk') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ request('jk') == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-block">Filter</button>
                    <a href="{{ route('guru.index') }}" class="btn btn-secondary ml-2"><i class="fas fa-undo"></i></a>
                </div>
            </form>
        </div>
    </div>

    {{-- TABEL DATA --}}
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th width="50">Foto</th>
                            <th>Identitas</th>
                            <th>Nama Guru</th>
                            <th class="text-center">Gender</th>
                            <th>Status Akun</th>
                            <th width="180">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($gurus as $guru)
                            @php 
                                $nip = $guru['nip'] ?? '';
                                $isReg = in_array($nip, $nipTerdaftar);
                                $nama = $guru['nama_guru'] ?? ($guru['nama'] ?? 'Tanpa Nama');
                                $jk = strtoupper($guru['jk'] ?? ($guru['jenis_kelamin'] ?? 'L'));
                            @endphp
                            <tr>
                                <td class="text-center align-middle">
                                    <div class="rounded-circle bg-gray-200 d-flex align-items-center justify-content-center mx-auto" style="width: 40px; height: 40px;">
                                        <i class="fas fa-user-tie {{ $isReg ? 'text-primary' : 'text-gray-400' }}"></i>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <small class="text-muted d-block">NIP:</small>
                                    <strong>{{ $nip ?: '-' }}</strong>
                                </td>
                                <td class="align-middle text-uppercase">{{ $nama }}</td>
                                <td class="text-center align-middle">
                                    @if($jk == 'L')
                                        <span class="badge badge-info px-2">Laki-laki</span>
                                    @else
                                        <span class="badge badge-danger px-2">Perempuan</span>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    @if($isReg)
                                        <span class="badge badge-success px-2 shadow-sm"><i class="fas fa-check-circle mr-1"></i> Terdaftar</span>
                                    @else
                                        <span class="badge badge-light border px-2 text-muted">Belum Registrasi</span>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    @if(!$isReg && $nip)
                                        <a href="{{ route('guru.sync', $nip) }}" class="btn btn-primary btn-sm btn-block shadow-sm">
                                            <i class="fas fa-user-plus mr-1"></i> Registrasi Akun
                                        </a>
                                    @elseif($isReg)
                                        <div class="d-flex">
                                            <button class="btn btn-light btn-sm border disabled flex-grow-1 mr-1">Tersinkron</button>
                                            @php 
                                                $guruLokal = \App\Models\Guru::where('nip', $nip)->first();
                                            @endphp
                                            @if($guruLokal)
                                            <form action="{{ route('guru.destroy', $guruLokal->id) }}" method="POST" onsubmit="return confirm('Hapus data?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                            </form>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-danger small">Data Invalid</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted font-italic">Data tidak ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection