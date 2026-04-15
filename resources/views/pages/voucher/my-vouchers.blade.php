@extends('layouts.app')

@section('title', 'Voucher Saya')

@section('content')
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Voucher Milikku</h1>
            <a href="{{ route('vouchers.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Tukar Voucher Baru
            </a>
        </div>

        <!-- Points Card -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-info font-weight-bold text-uppercase mb-1">Poin Saya Saat Ini</div>
                        <div class="h3 mb-0 text-gray-800">{{ $totalPoints }}</div>
                        <a href="{{ route('points.myPoints') }}" class="small text-info">
                            <i class="fas fa-arrow-right"></i> Lihat Detail Poin
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Status Voucher</h6>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs" id="voucherTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="activeTab" data-toggle="tab" 
                                data-target="#activeContent" type="button" role="tab">
                            <i class="fas fa-check-circle text-success"></i> Voucher Aktif
                            <span class="badge badge-primary ml-2">{{ $vouchers->total() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="usedTab" data-toggle="tab" 
                                data-target="#usedContent" type="button" role="tab">
                            <i class="fas fa-history text-muted"></i> Riwayat Penggunaan
                            <span class="badge badge-secondary ml-2">{{ $usedVouchers->total() }}</span>
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="voucherTabContent">
                    <!-- Active Vouchers Tab -->
                    <div class="tab-pane fade show active" id="activeContent" role="tabpanel">
                        <div class="mt-3">
                            <div class="row">
                                @forelse($vouchers as $studentVoucher)
                                    <div class="col-md-6 col-lg-4 mb-4">
                                        <div class="card border-left-success shadow h-100">
                                            <div class="card-body">
                                                <h5 class="card-title">{{ $studentVoucher->voucher->name }}</h5>
                                                
                                                @if($studentVoucher->voucher->description)
                                                    <p class="card-text text-muted small">{{ $studentVoucher->voucher->description }}</p>
                                                @endif

                                                <div class="mb-3">
                                                    <small class="text-success">
                                                        <i class="fas fa-check-circle"></i> Aktif
                                                    </small>
                                                </div>

                                                <div class="mb-2">
                                                    <small class="text-muted">
                                                        Ditukar: {{ \Carbon\Carbon::parse($studentVoucher->redeemed_at)->format('d M Y H:i') }}
                                                    </small>
                                                </div>

                                                <p class="card-text text-muted small mb-3">
                                                    <i class="fas fa-info-circle"></i> Voucher ini dapat digunakan untuk menghindari pengurangan poin saat Anda terlambat.
                                                </p>

                                                <button class="btn btn-sm btn-success btn-block" disabled 
                                                        title="Voucher akan otomatis digunakan saat Anda terlambat">
                                                    <i class="fas fa-hourglass-start"></i> Menunggu Digunakan
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle"></i> Anda belum memiliki voucher aktif. 
                                            <a href="{{ route('vouchers.index') }}">Tukar poin Anda dengan voucher!</a>
                                        </div>
                                    </div>
                                @endforelse
                            </div>

                            <!-- Pagination untuk active -->
                            @if($vouchers->hasPages())
                                <div class="d-flex justify-content-center">
                                    {{ $vouchers->links() }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Used Vouchers Tab -->
                    <div class="tab-pane fade" id="usedContent" role="tabpanel">
                        <div class="mt-3">
                            @if($usedVouchers->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Nama Voucher</th>
                                                <th>Tanggal Ditukar</th>
                                                <th>Tanggal Digunakan</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($usedVouchers as $studentVoucher)
                                                <tr>
                                                    <td>{{ $studentVoucher->voucher->name }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($studentVoucher->redeemed_at)->format('d M Y H:i') }}</td>
                                                    <td>
                                                        @if($studentVoucher->used_at)
                                                            {{ \Carbon\Carbon::parse($studentVoucher->used_at)->format('d M Y H:i') }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-success">
                                                            <i class="fas fa-check"></i> Digunakan
                                                        </span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">
                                                        Belum ada voucher yang digunakan
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination untuk used -->
                                @if($usedVouchers->hasPages())
                                    <div class="d-flex justify-content-center">
                                        {{ $usedVouchers->links() }}
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-info-circle"></i> Belum ada riwayat penggunaan voucher.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Section -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h6 class="m-0">Cara Menggunakan Voucher</h6>
                    </div>
                    <div class="card-body">
                        <ol class="mb-0">
                            <li>Tukar poin Anda dengan voucher dari katalog</li>
                            <li>Voucher akan masuk ke akun Anda secara otomatis</li>
                            <li>Saat Anda terlambat, voucher akan otomatis digunakan</li>
                            <li>Poin pengurangan akan dibatalkan oleh voucher</li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h6 class="m-0">Keuntungan Voucher</h6>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            <li><strong>Gratis dari Poin:</strong> Tidak perlu membayar uang</li>
                            <li><strong>Otomatis:</strong> Tidak perlu meminta atau mengajukan</li>
                            <li><strong>Fleksibel:</strong> Bisa ditukar kapan saja</li>
                            <li><strong>Nilai Tinggi:</strong> Mencegah pengurangan poin saat terlambat</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .nav-tabs .nav-link {
            border: none;
            color: #6c757d;
            padding: 1rem 1.5rem;
            border-bottom: 3px solid transparent;
        }

        .nav-tabs .nav-link.active {
            color: #3166cc;
            background-color: transparent;
            border-bottom-color: #3166cc;
        }

        .badge {
            font-size: 0.75rem;
        }

        .card {
            border-radius: 8px;
        }
    </style>
@endsection
