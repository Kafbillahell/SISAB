@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Manajemen User</h1>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <button class="btn btn-primary" data-toggle="modal" data-target="#addModal">
                <i class="fas fa-plus"></i> Tambah User
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge {{ $user->role == 'admin' ? 'badge-danger' : ($user->role == 'guru' ? 'badge-warning' : 'badge-info') }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editModal{{ $user->id }}" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal{{ $user->id }}" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>

                        <div class="modal fade" id="editModal{{ $user->id }}" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit User: {{ $user->name }}</h5>
                                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                    </div>
                                    <form action="{{ route('users.update', $user->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Nama</label>
                                                <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Role</label>
                                                <select name="role" class="form-control">
                                                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                                    <option value="guru" {{ $user->role == 'guru' ? 'selected' : '' }}>Guru</option>
                                                    <option value="siswa" {{ $user->role == 'siswa' ? 'selected' : '' }}>Siswa</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Password Baru (Kosongkan jika tidak ganti)</label>
                                                <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Update Data</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="deleteModal{{ $user->id }}" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title">Konfirmasi Hapus</h5>
                                        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        Apakah anda yakin ingin menghapus user <b>{{ $user->name }}</b>? Data yang dihapus tidak bisa dikembalikan.
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Ya, Hapus!</button>
                                        </form>
                                    </div>
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
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Tambah User Baru</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('users.store') }}" method="POST">
    @csrf
    <div class="modal-body">
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Masukkan Nama" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="Masukkan Email" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password Minimal 6 karakter" minlength="6" required>
            <small class="form-text text-muted">Pastikan password sulit ditebak.</small>
        </div>
        <div class="form-group">
            <label>Role</label>
            <select name="role" class="form-control @error('role') is-invalid @enderror" required>
                <option value="" disabled selected>- Pilih Role -</option>
                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="guru" {{ old('role') == 'guru' ? 'selected' : '' }}>Guru</option>
                <option value="siswa" {{ old('role') == 'siswa' ? 'selected' : '' }}>Siswa</option>
            </select>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan User</button>
    </div>
</form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    function attachMessages(form){
        var inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(function(el){
            el.addEventListener('invalid', function(e){
                el.setCustomValidity('');
                if (el.validity.valid) return;
                var name = el.name || '';
                var msg = 'Field tidak valid';
                if (el.validity.valueMissing){
                    if (name === 'name') msg = 'Nama tidak boleh kosong';
                    else if (name === 'email') msg = 'Email tidak boleh kosong';
                    else if (name === 'password') msg = 'Password tidak boleh kosong';
                    else if (name === 'role') msg = 'Pilih role';
                    else msg = 'Field ini wajib diisi';
                } else if (el.validity.typeMismatch){
                    if (el.type === 'email') msg = 'Format email tidak valid';
                    else msg = 'Format input tidak valid';
                } else if (el.validity.tooShort){
                    if (name === 'password') msg = 'Password minimal 6 karakter';
                    else msg = 'Nilai terlalu pendek';
                } else {
                    msg = 'Format input tidak valid';
                }
                el.setCustomValidity(msg);
            });
            el.addEventListener('input', function(){ el.setCustomValidity(''); });
            el.addEventListener('change', function(){ el.setCustomValidity(''); });
        });
    }
    document.querySelectorAll('form').forEach(attachMessages);
});
</script>

@endsection