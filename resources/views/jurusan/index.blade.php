@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Master Data Jurusan</h1>
        <a href="{{ route('kelas.index') }}" class="btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-door-open fa-sm text-white-50"></i> Kembali ke Manajemen Kelas
        </a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow mb-4 border-left-success">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Tambah Jurusan Baru</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('jurusan.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="small font-weight-bold">Kode Jurusan</label>
                            <input type="text" name="kode_jurusan" class="form-control @error('kode_jurusan') is-invalid @enderror" placeholder="Contoh: RPL" required>
                        </div>
                        <div class="form-group">
                            <label class="small font-weight-bold">Nama Lengkap Jurusan</label>
                            <input type="text" name="nama_jurusan" class="form-control" placeholder="Contoh: Rekayasa Perangkat Lunak" required>
                        </div>
                        <button type="submit" class="btn btn-success btn-sm btn-block shadow-sm">
                            <i class="fas fa-save mr-1"></i> Simpan Jurusan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead class="bg-light text-center">
                                <tr>
                                    <th width="10%">No</th>
                                    <th width="20%">Kode</th>
                                    <th>Nama Jurusan</th>
                                    <th width="20%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($jurusans as $j)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="text-center"><span class="badge badge-info">{{ $j->kode_jurusan }}</span></td>
                                    <td>{{ $j->nama_jurusan }}</td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal{{ $j->id }}">
                                                <i class="fas fa-edit"></i>
                                            </td>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Data jurusan masih kosong.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@foreach($jurusans as $j)
<div class="modal fade" id="editModal{{ $j->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('jurusan.update', $j->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title">Edit Jurusan: {{ $j->kode_jurusan }}</h5>
                    <button class="close" type="button" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Kode Jurusan</label>
                        <input type="text" name="kode_jurusan" class="form-control" value="{{ $j->kode_jurusan }}" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Jurusan</label>
                        <input type="text" name="nama_jurusan" class="form-control" value="{{ $j->nama_jurusan }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning btn-sm">Update Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection