@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Periode</h1>
        <button class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#tambahPeriodeModal">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Periode
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success mt-3">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger mt-3">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Periode Penilaian</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>Nama Periode</th>
                            <th width="150" class="text-center">Status Aktif</th>
                            <th width="150" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($periodes as $index => $periode)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $periode->nama_periode }}</td>
                            <td class="text-center">
                                @if($periode->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-secondary">Tidak Aktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editPeriodeModal{{ $periode->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('periode.destroy', $periode->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus periode ini? Semua data penilaian sikap pada periode ini akan ikut terhapus.')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editPeriodeModal{{ $periode->id }}" tabindex="-1" role="dialog" aria-labelledby="editPeriodeModalLabel{{ $periode->id }}" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editPeriodeModalLabel{{ $periode->id }}">Edit Periode</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="{{ route('periode.update', $periode->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body text-left">
                                            <div class="form-group">
                                                <label for="nama_periode">Nama Periode</label>
                                                <input type="text" class="form-control" id="nama_periode" name="nama_periode" value="{{ $periode->nama_periode }}" placeholder="Contoh: Semester 1 Tahun 2026/2027" required>
                                            </div>
                                            <div class="form-group form-check">
                                                <input type="checkbox" class="form-check-input" id="is_active{{ $periode->id }}" name="is_active" value="1" {{ $periode->is_active ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_active{{ $periode->id }}">Aktifkan Periode</label>
                                            </div>
                                            <small class="text-muted">Centang untuk memberikan akses menambah penilaian pada periode ini.</small>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">Belum ada data periode.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Tambah Modal -->
<div class="modal fade" id="tambahPeriodeModal" tabindex="-1" role="dialog" aria-labelledby="tambahPeriodeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahPeriodeModalLabel">Tambah Periode Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('periode.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nama_periode_new">Nama Periode</label>
                        <input type="text" class="form-control" id="nama_periode_new" name="nama_periode" placeholder="Contoh: Semester 1 Tahun 2026/2027" required>
                    </div>
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" id="is_active_new" name="is_active" value="1" checked>
                        <label class="form-check-label" for="is_active_new">Aktifkan Periode</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
