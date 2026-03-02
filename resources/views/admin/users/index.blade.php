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
                                <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editModal{{ $user->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal{{ $user->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        
                        @include('admin.users.partials.modals', ['user' => $user])
                        
                        @empty
                        <tr>
                            <td colspan="3" class="text-center">Tidak ada data {{ $role }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                
                <div class="d-flex justify-content-center mt-3">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- @include('admin.users.partials.modals', ['user' => $user]) --}}

@endsection