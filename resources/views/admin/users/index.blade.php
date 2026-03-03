@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Manajemen User ({{ ucfirst($role) }})</h1>

    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link {{ $role == 'admin' ? 'active' : '' }}" href="{{ route('users.index', ['role' => 'admin']) }}">Admin</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $role == 'guru' ? 'active' : '' }}" href="{{ route('users.index', ['role' => 'guru']) }}">Guru</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $role == 'siswa' ? 'active' : '' }}" href="{{ route('users.index', ['role' => 'siswa']) }}">Siswa</a>
        </li>
    </ul>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <button class="btn btn-primary" data-toggle="modal" data-target="#addModal">
                <i class="fas fa-plus"></i> Tambah {{ ucfirst($role) }}
            </button>
            <span class="badge badge-primary">Total {{ ucfirst($role) }}: {{ $users->total() }}</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-warning btn-edit-user" data-toggle="modal" data-target="#editUserModal"
                                    data-user-id="{{ $user->id }}"
                                    data-user-name="{{ $user->name }}"
                                    data-user-email="{{ $user->email }}"
                                    data-user-role="{{ $user->role }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger btn-delete-user" data-toggle="modal" data-target="#deleteUserModal"
                                    data-user-id="{{ $user->id }}"
                                    data-user-name="{{ $user->name }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        
                        @empty
                        <tr>
                            <td colspan="3" class="text-center">Tidak ada data {{ $role }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Single Edit Modal (reused for all users) -->
                <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit User</h5>
                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                            </div>
                            <form id="editUserForm" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Nama</label>
                                        <input type="text" name="name" id="editUserName" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" name="email" id="editUserEmail" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Role</label>
                                        <select name="role" id="editUserRole" class="form-control">
                                            <option value="admin">Admin</option>
                                            <option value="guru">Guru</option>
                                            <option value="siswa">Siswa</option>
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
                
                <!-- Single Delete Modal (reused for all users) -->
                <div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title">Konfirmasi Hapus</h5>
                                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                            </div>
                            <div class="modal-body">
                                Apakah anda yakin ingin menghapus user <b id="deleteUserName"></b>? Data yang dihapus tidak bisa dikembalikan.
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                <form id="deleteUserForm" method="POST" action="">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Ya, Hapus!</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- @include('admin.users.partials.modals', ['user' => $user]) --}}

<script>
// Delegated handler: isi modal edit ketika tombol edit diklik
document.addEventListener('click', function (e) {
    var btn = e.target.closest('.btn-edit-user');
    if (!btn) return;
    var id = btn.getAttribute('data-user-id');
    var name = btn.getAttribute('data-user-name');
    var email = btn.getAttribute('data-user-email');
    var role = btn.getAttribute('data-user-role');

    var form = document.getElementById('editUserForm');
    var nameInput = document.getElementById('editUserName');
    var emailInput = document.getElementById('editUserEmail');
    var roleSelect = document.getElementById('editUserRole');

    if (form) {
        form.action = '/users/' + id;
    }
    if (nameInput) nameInput.value = name || '';
    if (emailInput) emailInput.value = email || '';
    if (roleSelect) roleSelect.value = role || 'admin';
});

// Move modal element to <body> to avoid stacking-context issues (z-index/backdrop)
document.addEventListener('DOMContentLoaded', function () {
    var editModal = document.getElementById('editUserModal');
    if (editModal && editModal.parentNode !== document.body) {
        document.body.appendChild(editModal);
    }
});

// Ensure modal always appended to body when shown (covers cases where DOM moved later)
if (window.jQuery) {
    window.jQuery(document).on('show.bs.modal', '#editUserModal', function () {
        var m = window.jQuery(this);
        if (m.parent()[0] !== document.body) m.appendTo('body');
    });
}

// Populate and set action for delete modal
document.addEventListener('click', function (e) {
    var btn = e.target.closest('.btn-delete-user');
    if (!btn) return;
    var id = btn.getAttribute('data-user-id');
    var name = btn.getAttribute('data-user-name');

    var form = document.getElementById('deleteUserForm');
    var nameEl = document.getElementById('deleteUserName');
    if (form) form.action = '/users/' + id;
    if (nameEl) nameEl.textContent = name || '';
});
</script>

@endsection