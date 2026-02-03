@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Tambah Tahun Ajaran</div>
                <div class="card-body">
                    <form action="{{ route('tahun-ajaran.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label>Tahun (Contoh: 2024/2025)</label>
                            <input type="text" name="tahun" class="form-control" placeholder="2024/2025" required>
                        </div>
                        <div class="mb-3">
                            <label>Semester</label>
                            <select name="semester" class="form-control" required>
                                <option value="Ganjil">Ganjil</option>
                                <option value="Genap">Genap</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Simpan</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Daftar Tahun Ajaran</div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tahun</th>
                                <th>Semester</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tahunAjarans as $ta)
                            <tr>
                                <td>{{ $ta->tahun }}</td>
                                <td>{{ $ta->semester }}</td>
                                <td>
                                    @if($ta->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Non-Aktif</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!$ta->is_active)
                                        <form action="{{ route('tahun-ajaran.activate', $ta->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-info text-white">Aktifkan</button>
                                        </form>
                                        
                                        <form action="{{ route('tahun-ajaran.destroy', $ta->id) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus?')">Hapus</button>
                                        </form>
                                    @else
                                        <button class="btn btn-sm btn-outline-secondary" disabled>Active Now</button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection