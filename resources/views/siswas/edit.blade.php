@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edit Data Siswa</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('siswas.update', $siswa->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>NISN</label>
                        <input type="text" name="nisn" class="form-control @error('nisn') is-invalid @enderror" value="{{ old('nisn', $siswa->nisn) }}">
                        @error('nisn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama_siswa" class="form-control @error('nama_siswa') is-invalid @enderror" value="{{ old('nama_siswa', $siswa->nama_siswa) }}">
                        @error('nama_siswa') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-control">
                            <option value="L" {{ $siswa->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ $siswa->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Ganti Foto (Opsional)</label>
                        <input type="file" name="foto" class="form-control-file">
                        @if($siswa->foto)
                            <img src="{{ asset('storage/'.$siswa->foto) }}" width="80" class="mt-2 img-thumbnail">
                        @endif
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Update Data</button>
                    <a href="{{ route('siswas.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection