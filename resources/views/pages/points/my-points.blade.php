@extends('layouts.app')

@section('title', 'Poin Saya')

@section('content')
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Poin Saya</h1>
            <a href="{{ route('vouchers.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-gift fa-sm text-white-50"></i> Tukar Poin dengan Voucher
            </a>
        </div>

        <!-- Points Card -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-primary font-weight-bold text-uppercase mb-1">Total Poin Saya</div>
                        <div class="h3 mb-0 text-gray-800">{{ $totalPoints }}</div>
                        <small class="text-muted">
                            @if($totalPoints >= 15)
                                <i class="fas fa-check text-success"></i> Anda bisa menukar voucher!
                            @else
                                <i class="fas fa-info-circle"></i> Butuh {{ 15 - $totalPoints }} poin lagi untuk menukar voucher
                            @endif
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Poin -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-clock text-success"></i> Tepat Waktu
                        </h5>
                        <p class="card-text">+10 Poin</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-exclamation-circle text-danger"></i> Terlambat
                        </h5>
                        <p class="card-text">-5 Poin</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Riwayat Presensi dengan Poin -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Riwayat Presensi dan Poin</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="presenciTable">
                        <thead class="bg-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Kelas/Rombel</th>
                                <th>Mata Pelajaran</th>
                                <th>Jam Mulai</th>
                                <th>Waktu Scan</th>
                                <th>Status</th>
                                <th>Poin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($presensis as $presensi)
                                <tr>
                                    <td>{{ $presensi->tanggal ? \Carbon\Carbon::parse($presensi->tanggal)->format('d M Y') : '-' }}</td>
                                    <td>
                                        {{ $presensi->jadwal?->rombel?->nama_rombel ?? '-' }}
                                    </td>
                                    <td>
                                        {{ $presensi->jadwal?->mapel?->nama_mapel ?? '-' }}
                                    </td>
                                    <td>
                                        {{ $presensi->jadwal?->jam_mulai ? \Carbon\Carbon::parse($presensi->jadwal->jam_mulai)->format('H:i') : '-' }}
                                    </td>
                                    <td>
                                        {{ $presensi->waktu_scan ? \Carbon\Carbon::parse($presensi->waktu_scan)->format('H:i') : '-' }}
                                    </td>
                                    <td>
                                        @if($presensi->status === 'Hadir')
                                            <span class="badge badge-success">Hadir</span>
                                        @elseif($presensi->status === 'Sakit')
                                            <span class="badge badge-warning">Sakit</span>
                                        @elseif($presensi->status === 'Izin')
                                            <span class="badge badge-info">Izin</span>
                                        @else
                                            <span class="badge badge-danger">Alfa</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($presensi->points > 0)
                                            <span class="badge badge-success">+{{ $presensi->points }}</span>
                                        @elseif($presensi->points < 0)
                                            <span class="badge badge-danger">{{ $presensi->points }}</span>
                                        @else
                                            <span class="badge badge-secondary">0</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        <i class="fas fa-inbox"></i> Belum ada riwayat presensi
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $presensis->links() }}
                </div>
            </div>
        </div>
    </div>

    <style>
        .table-hover tbody tr:hover {
            background-color: #f5f5f5;
        }
        
        .badge {
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }
    </style>
@endsection
