@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Manajemen Anggota Rombel</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('anggota-rombel.index') }}" method="GET" class="form-inline">
                <label class="mr-2">Pilih Rombongan Belajar:</label>
                <select name="rombel_id" class="form-control mr-2" onchange="this.form.submit()">
                    <option value="">-- Pilih Rombel --</option>
                    @foreach($rombels as $r)
                        <option value="{{ $r->id }}" {{ request('rombel_id') == $r->id ? 'selected' : '' }}>
                            {{ $r->nama_rombel }} ({{ $r->tahunAjaran->tahun }})
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    @if($selectedRombel)
    <div class="row">
        <div class="col-md-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Siswa di {{ $selectedRombel->nama_rombel }}</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>NISN</th>
                                    <th>Nama Siswa</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($anggotas as $agt)
                                <tr>
                                    <td>{{ $agt->siswa->nisn }}</td>
                                    <td>{{ $agt->siswa->nama_siswa }}</td>
                                    <td>
                                        <form action="{{ route('anggota-rombel.destroy', $agt->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-danger btn-sm" onclick="return confirm('Keluarkan siswa?')">Keluarkan</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center">Belum ada siswa.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card shadow mb-4 border-left-success">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Tambah Siswa ke Rombel</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('anggota-rombel.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="rombel_id" value="{{ $selectedRombel->id }}">
                        
                        <div class="form-group">
                            <label>Pilih Siswa (Bisa banyak sekaligus):</label>
                            <select name="siswa_id[]" class="form-control" multiple style="height: 300px;" required>
                                @foreach($siswas as $s)
                                    <option value="{{ $s->id }}">{{ $s->nisn }} - {{ $s->nama_siswa }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Tahan Ctrl untuk memilih lebih dari satu.</small>
                        </div>
                        <button type="submit" class="btn btn-success btn-block">Tambahkan Siswa</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection