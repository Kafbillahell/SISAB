@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edit Data & Wajah Siswa</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('siswas.update', $siswa->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>NISN</label>
                            <input type="text" name="nisn" class="form-control" value="{{ $siswa->nisn }}" required>
                        </div>
                        <div class="form-group">
                            <label>Nama Siswa</label>
                            <input type="text" name="nama_siswa" class="form-control" value="{{ $siswa->nama_siswa }}" required>
                        </div>
                    </div>
                    <div class="col-md-6 text-center">
                        <div class="form-group">
                            <label class="d-block">Foto Wajah Saat Ini</label>
                            <div class="mb-3">
                                <img id="preview" src="{{ $siswa->foto ? asset('storage/'.$siswa->foto) : 'https://via.placeholder.com/150' }}" 
                                     class="img-thumbnail" style="width: 200px; height: 200px; object-fit: cover;">
                            </div>
                            <input type="file" name="foto" id="fotoInput" class="form-control-file border p-1" accept="image/*" onchange="previewImage()">
                            <small class="text-info">Biarkan kosong jika tidak ingin mengubah foto wajah.</small>
                        </div>
                    </div>
                </div>
                <hr>
                <button type="submit" class="btn btn-warning">Update Data</button>
                <a href="{{ route('siswas.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

<script>
    function previewImage() {
        const file = document.getElementById('fotoInput').files[0];
        const preview = document.getElementById('preview');
        const reader = new FileReader();
        reader.onloadend = () => preview.src = reader.result;
        if (file) reader.readAsDataURL(file);
    }
</script>
@endsection