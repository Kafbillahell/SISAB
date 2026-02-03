@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Rekap Presensi Kompleks</h1>
        <div class="btn-group">
            <a href="{{ route('presensi.scanner') }}" class="btn btn-primary btn-sm shadow-sm">
                <i class="fas fa-camera fa-sm text-white-50"></i> Buka Scanner
            </a>
            <button onclick="window.print()" class="btn btn-secondary btn-sm shadow-sm">
                <i class="fas fa-print fa-sm text-white-50"></i> Cetak Laporan
            </button>
        </div>
    </div>

    <div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Filter Data Presensi</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('presensi.index') }}" method="GET" class="row">
            <div class="col-md-3 mb-2">
                <label class="small font-weight-bold">Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control" 
                    value="{{ request('start_date') ?? date('Y-m-d') }}">
            </div>
            <div class="col-md-3 mb-2">
                <label class="small font-weight-bold">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control" 
                    value="{{ request('end_date') ?? date('Y-m-d') }}">
            </div>
            <div class="col-md-4 mb-2">
                <label class="small font-weight-bold">Pilih Kelas (Rombel)</label>
                <select name="rombel_id" class="form-control" required>
                    {{-- Hilangkan opsi "Semua Kelas", ganti jadi placeholder --}}
                    <option value="">-- Silakan Pilih Kelas --</option>
                    @foreach($rombels as $r)
                        <option value="{{ $r->id }}" {{ request('rombel_id') == $r->id ? 'selected' : '' }}>
                            {{ $r->nama_rombel }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-search"></i> Cari
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="bg-primary text-white text-center">
                    <tr>
                        <th>Hari & Waktu</th>
                        <th>Info Siswa</th>
                        <th>Kelas & Wali Kelas</th>
                        <th>Mata Pelajaran</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($presensis as $p)
                    <tr>
                        {{-- ... isi data tabel (sama seperti kode kamu sebelumnya) ... --}}
                        <td class="text-center">
                            <span class="font-weight-bold">{{ \Carbon\Carbon::parse($p->waktu_scan)->translatedFormat('l') }}</span><br>
                            <small>{{ \Carbon\Carbon::parse($p->waktu_scan)->format('d/m/Y H:i') }}</small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('storage/' . $p->siswa->foto) }}" class="rounded-circle mr-2" width="40" height="40" style="object-fit: cover;">
                                <div>
                                    <strong>{{ $p->siswa->nama_siswa }}</strong><br>
                                    <small class="text-muted">NISN: {{ $p->siswa->nisn }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-info">{{ $p->jadwal->rombel->nama_rombel }}</span><br>
                            <small>Wali: {{ $p->jadwal->rombel->guru->nama_guru ?? 'Belum Diatur' }}</small>
                        </td>
                        <td>
                            <strong>{{ $p->jadwal->mapel->nama_mapel }}</strong><br>
                            <small class="text-primary">{{ $p->jadwal->jam_mulai }} - {{ $p->jadwal->jam_selesai }}</small>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-{{ $p->keterangan == 'Hadir' ? 'success' : 'warning' }} p-2">
                                {{ strtoupper($p->keterangan) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            @if(!request('rombel_id'))
                                <div class="text-primary">
                                    <i class="fas fa-arrow-up fa-2x mb-3"></i><br>
                                    <strong>Silakan pilih kelas terlebih dahulu</strong> untuk melihat data presensi.
                                </div>
                            @else
                                <div class="text-muted">
                                    <i class="fas fa-search fa-2x mb-3"></i><br>
                                    Tidak ada data presensi untuk kelas **{{ $rombels->where('id', request('rombel_id'))->first()->nama_rombel ?? '' }}** pada periode ini.
                                </div>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection