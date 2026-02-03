@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Manajemen Siswa</h1>
    
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
            <h6 class="m-0 font-weight-bold text-primary">Daftar Siswa</h6>
            <a href="{{ route('siswas.create') }}" class="btn btn-primary btn-sm shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Siswa
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>NISN</th>
                            <th>Nama</th>
                            <th>L/P</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswas as $siswa)
                        <tr>
                            <td class="text-center">
    @if($siswa->foto)
        <div class="position-relative d-inline-block">
            <img src="{{ asset('storage/'.$siswa->foto) }}" width="50" height="50" class="rounded-circle shadow-sm" style="object-fit: cover;">
            <span class="badge badge-success position-absolute" style="bottom: -5px; right: -5px; font-size: 0.6rem;">
                <i class="fas fa-check"></i> Ready
            </span>
        </div>
    @else
        <span class="badge badge-danger">Wajah Belum Terdaftar</span>
    @endif
</td>
                            <td>{{ $siswa->nisn }}</td>
                            <td>{{ $siswa->nama_siswa }}</td>
                            <td>{{ $siswa->jenis_kelamin }}</td>
                            <td>
                                <form action="{{ route('siswas.destroy', $siswa->id) }}" method="POST">
                                    <a href="{{ route('siswas.edit', $siswa->id) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Data kosong</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $siswas->links() }}
            </div>
        </div>
    </div>
</div>
@endsection