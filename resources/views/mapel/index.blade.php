@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Mata Pelajaran</h1>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tambah Mapel</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('mapel.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Kode Mapel</label>
                            <input type="text" name="kode_mapel" class="form-control" placeholder="Contoh: BIN-10" required>
                        </div>
                        <div class="form-group">
                            <label>Nama Mata Pelajaran</label>
                            <input type="text" name="nama_mapel" class="form-control" placeholder="Contoh: Bahasa Indonesia" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Simpan</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Mata Pelajaran</h6>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama Mapel</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mapels as $m)
                                <tr>
                                    <td>{{ $m->kode_mapel }}</td>
                                    <td>{{ $m->nama_mapel }}</td>
                                    <td>
                                        <form action="{{ route('mapel.destroy', $m->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal{{ $m->id }}">Edit</button>
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus mapel ini?')">Hapus</button>
                                        </form>
                                    </td>
                                </tr>

                                <div class="modal fade" id="editModal{{ $m->id }}" tabindex="-1" role="dialog">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="{{ route('mapel.update', $m->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Mata Pelajaran</h5>
                                                    <button class="close" type="button" data-dismiss="modal"><span>×</span></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Kode Mapel</label>
                                                        <input type="text" name="kode_mapel" class="form-control" value="{{ $m->kode_mapel }}" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Nama Mapel</label>
                                                        <input type="text" name="nama_mapel" class="form-control" value="{{ $m->nama_mapel }}" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Update</button>
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
    </div>
</div>
@endsection