@extends('layouts.app')

@section('title', 'Kelola Voucher')

@section('content')
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Kelola Voucher</h1>
            <a href="{{ route('admin.vouchers.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Voucher
            </a>
        </div>

        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Voucher Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Voucher</h6>
            </div>
            <div class="card-body">
                @if($vouchers->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Nama Voucher</th>
                                    <th>Deskripsi</th>
                                    <th>Point Cost</th>
                                    <th>Ketersediaan</th>
                                    <th>Tipe Penggunaan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($vouchers as $voucher)
                                    <tr>
                                        <td><strong>{{ $voucher->name }}</strong></td>
                                        <td>{{ Str::limit($voucher->description, 50) }}</td>
                                        <td>
                                            <span class="badge badge-primary">{{ $voucher->point_cost }} poin</span>
                                        </td>
                                        <td>
                                            {{ $voucher->quantity - $voucher->used }} / {{ $voucher->quantity }}
                                            <br>
                                            <small class="text-muted">({{ $voucher->used }} digunakan)</small>
                                        </td>
                                        <td>
                                            @if($voucher->usage_type === 'anytime')
                                                <span class="badge badge-success">
                                                    <i class="fas fa-clock"></i> Setiap Saat
                                                </span>
                                            @else
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-hourglass-end"></i> {{ $voucher->valid_minutes }} min Setelah Jam
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-sm" 
                                                onclick="toggleActive({{ $voucher->id }}, {{ $voucher->is_active ? 'false' : 'true' }})"
                                                style="background-color: {{ $voucher->is_active ? '#d4edda' : '#f8d7da' }}; color: {{ $voucher->is_active ? '#155724' : '#721c24' }};">
                                                {{ $voucher->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </button>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.vouchers.edit', $voucher) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form action="{{ route('admin.vouchers.destroy', $voucher) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $vouchers->links() }}
                    </div>
                @else
                    <div class="alert alert-info text-center">
                        <i class="fas fa-inbox"></i> Belum ada voucher. 
                        <a href="{{ route('admin.vouchers.create') }}">Buat yang pertama</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function toggleActive(voucherId, newStatus) {
            fetch(`/admin/vouchers/${voucherId}/toggle-active`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ is_active: newStatus })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    location.reload();
                }
            });
        }
    </script>
@endsection
