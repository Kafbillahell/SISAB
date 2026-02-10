@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Kelas</h1>
        <a href="{{ route('jurusan.index') }}" class="btn btn-sm btn-success shadow-sm">
            <i class="fas fa-graduation-cap fa-sm text-white-50"></i> Kelola Master Jurusan
        </a>
    </div>

    <div class="row">
        {{-- FORM TAMBAH KELAS --}}
        <div class="col-md-4">
            <div class="card shadow mb-4 border-left-primary">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tambah Rombongan Belajar</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('kelas.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="small font-weight-bold">Tingkat</label>
                            <select name="tingkat" class="form-control" required>
                                <option value="10">10 (X)</option>
                                <option value="11">11 (XI)</option>
                                <option value="12">12 (XII)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="small font-weight-bold">Pilih Jurusan</label>
                            <select name="jurusan_id" class="form-control" required>
                                <option value="">-- Pilih Jurusan --</option>
                                @foreach($jurusans as $j)
                                    <option value="{{ $j->id }}">{{ $j->kode_jurusan }} - {{ $j->nama_jurusan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="small font-weight-bold">Nama Rombel (Contoh: 1)</label>
                            <input type="text" name="nama_kelas" class="form-control" placeholder="Contoh: 1" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm btn-block shadow-sm">Simpan Kelas</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- TABEL DATA KELAS --}}
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-light text-center">
                                <tr>
                                    <th>Tingkat</th>
                                    <th>Jurusan</th>
                                    <th>Nama Kelas</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($kelas as $k)
                                <tr>
                                    <td class="text-center">{{ $k->tingkat }}</td>
                                    <td>
                                        <span class="badge badge-info px-2">{{ $k->jurusan->kode_jurusan ?? 'N/A' }}</span>
                                    </td>
                                    <td>{{ $k->nama_kelas }}</td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            {{-- Tombol Edit Trigger Modal --}}
                                            <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editKelas{{ $k->id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            
                                            {{-- Tombol Delete --}}
                                            <form action="{{ route('kelas.destroy', $k->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kelas ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm ml-1">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                {{-- MODAL EDIT UNTUK SETIAP BARIS --}}
                                <div class="modal fade" id="editKelas{{ $k->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog shadow-lg" role="document">
                                        <div class="modal-content border-0">
                                            <form action="{{ route('kelas.update', $k->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header bg-warning">
                                                    <h5 class="modal-title font-weight-bold text-white">Edit Data Kelas</h5>
                                                    <button class="close text-white" type="button" data-dismiss="modal"><span>&times;</span></button>
                                                </div>
                                                <div class="modal-body text-left">
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">Tingkat</label>
                                                        <select name="tingkat" class="form-control" required>
                                                            <option value="10" {{ $k->tingkat == '10' ? 'selected' : '' }}>10 (X)</option>
                                                            <option value="11" {{ $k->tingkat == '11' ? 'selected' : '' }}>11 (XI)</option>
                                                            <option value="12" {{ $k->tingkat == '12' ? 'selected' : '' }}>12 (XII)</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">Jurusan</label>
                                                        <select name="jurusan_id" class="form-control" required>
                                                            @foreach($jurusans as $j)
                                                                <option value="{{ $j->id }}" {{ $k->jurusan_id == $j->id ? 'selected' : '' }}>
                                                                    {{ $j->kode_jurusan }} - {{ $j->nama_jurusan }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">Nama Rombel</label>
                                                        <input type="text" name="nama_kelas" class="form-control" value="{{ $k->nama_kelas }}" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-warning btn-sm font-weight-bold">Update Data</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                {{-- END MODAL --}}

                                @empty
                                <tr><td colspan="4" class="text-center text-muted">Belum ada data kelas.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection