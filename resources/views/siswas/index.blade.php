@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Manajemen Siswa (Data API)</h1>
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Siswa Tahun 2025</h6>
            {{-- Tombol tambah disembunyikan karena data dari API --}}
            <span class="badge badge-info">Sumber Data: ZieLabs API</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>NISN</th>
                            <th>Nama</th>
                            <th>L/P</th>
                            <th>Kelas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
    @forelse($siswas as $siswa)
    <tr>
        <td class="text-center">
            @if(isset($siswa['foto']) && $siswa['foto'])
                <div class="position-relative d-inline-block">
                    <img src="{{ $siswa['foto'] }}" width="50" height="50" class="rounded-circle shadow-sm" style="object-fit: cover;">
                    <span class="badge badge-success position-absolute" style="bottom: -5px; right: -5px; font-size: 0.6rem;">
                        <i class="fas fa-check"></i>
                    </span>
                </div>
            @else
                <span class="badge badge-secondary">Belum Ada Foto</span>
            @endif
        </td>
        <td>{{ $siswa['nisn'] ?? ($siswa['nis'] ?? '-') }}</td>
        <td>{{ $siswa['nama'] }}</td>
        
        {{-- REVISI JENIS KELAMIN --}}
        <td class="text-center">
            @php 
                $jk = $siswa['jk'] ?? ($siswa['jenis_kelamin'] ?? '-'); 
            @endphp
            @if($jk == 'L')
                <span class="badge badge-info">Laki-laki</span>
            @elseif($jk == 'P')
                <span class="badge badge-danger">Perempuan</span>
            @else
                {{ $jk }}
            @endif
        </td>

        {{-- REVISI KELAS/ROMBEL --}}
        <td>
            {{ $siswa['nama_rombel'] ?? ($siswa['nama_kelas'] ?? ($siswa['kelas'] ?? 'N/A')) }}
        </td>

        <td>
            <a href="#" class="btn btn-info btn-sm">
                <i class="fas fa-eye"></i> Detail
            </a>
            <a href="{{ route('siswas.sync', $siswa['nisn'] ?? $siswa['nis']) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-sync"></i> Registrasi
            </a>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="6" class="text-center">Gagal memuat data dari API atau data kosong.</td>
    </tr>
    @endforelse
</tbody>
                </table>
            </div>
            {{-- Pagination ditiadakan jika data API berbentuk array biasa, atau gunakan koleksi --}}
        </div>
    </div>
</div>
@endsection