@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Rombongan Belajar (Rombel)</h1>
        <button class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#addModal">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Rombel
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow">{{ session('success') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nama Rombel</th>
                            <th>Tingkat/Kelas</th>
                            <th>Wali Kelas</th>
                            <th>Tahun Ajaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rombels as $r)
                        <tr>
                            <td><strong>{{ $r->nama_rombel }}</strong></td>
                            <td>{{ $r->kelas->nama_kelas }} ({{ $r->kelas->tingkat }})</td>
                            <td>{{ $r->guru->nama_guru ?? 'Belum Ditentukan' }}</td>
                            <td>{{ $r->tahunAjaran->tahun }} - {{ $r->tahunAjaran->semester }}</td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal{{ $r->id }}">Edit</button>
                                <form action="{{ route('rombel.destroy', $r->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm" onclick="return confirm('Hapus Rombel?')">Hapus</button>
                                </form>
                            </td>
                        </tr>

                        <div class="modal fade" id="editModal{{ $r->id }}" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <form action="{{ route('rombel.update', $r->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Rombel</h5>
                                            <button class="close" type="button" data-dismiss="modal"><span>×</span></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Nama Rombel</label>
                                                <input type="text" name="nama_rombel" class="form-control" value="{{ $r->nama_rombel }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Pilih Kelas</label>
                                                <select name="kelas_id" class="form-control" required>
                                                    @foreach($kelas as $k)
                                                        <option value="{{ $k->id }}" {{ $r->kelas_id == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }} ({{ $k->tingkat }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Wali Kelas</label>
                                                <select name="guru_id" class="form-control">
                                                    <option value="">-- Tanpa Wali Kelas --</option>
                                                    @foreach($gurus as $g)
                                                        <option value="{{ $g->id }}" {{ $r->guru_id == $g->id ? 'selected' : '' }}>{{ $g->nama_guru }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Tahun Ajaran</label>
                                                <select name="tahun_ajaran_id" class="form-control" required>
                                                    @foreach($tahunAjarans as $ta)
                                                        <option value="{{ $ta->id }}" {{ $r->tahun_ajaran_id == $ta->id ? 'selected' : '' }}>{{ $ta->tahun }} ({{ $ta->semester }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('rombel.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Rombongan Belajar</h5>
                    <button class="close" type="button" data-dismiss="modal"><span>×</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Rombel (Contoh: X RPL 1)</label>
                        <input type="text" name="nama_rombel" class="form-control" placeholder="X RPL 1" required>
                    </div>
                    <div class="form-group">
                        <label>Pilih Kelas</label>
                        <select name="kelas_id" class="form-control" required>
                            @foreach($kelas as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kelas }} ({{ $k->tingkat }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Wali Kelas</label>
                        <select name="guru_id" class="form-control">
                            <option value="">-- Pilih Guru --</option>
                            @foreach($gurus as $g)
                                <option value="{{ $g->id }}">{{ $g->nama_guru }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tahun Ajaran</label>
                        <select name="tahun_ajaran_id" class="form-control" required>
                            @foreach($tahunAjarans as $ta)
                                <option value="{{ $ta->id }}" {{ $ta->is_active ? 'selected' : '' }}>{{ $ta->tahun }} ({{ $ta->semester }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection