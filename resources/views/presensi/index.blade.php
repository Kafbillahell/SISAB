@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Analitik Presensi Terpadu</h1>
            <p class="mb-0 text-muted small">Laporan detail kehadiran siswa per mata pelajaran</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('presensi.scanner') }}" class="btn btn-primary btn-sm shadow-sm">
                <i class="fas fa-camera fa-sm"></i> Scanner
            </a>
            <button onclick="window.print()" class="btn btn-secondary btn-sm shadow-sm">
                <i class="fas fa-print fa-sm"></i> Export / Cetak
            </button>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="card shadow mb-4 border-left-primary">
        <div class="card-body">
            <form action="{{ route('presensi.index') }}" method="GET" class="row align-items-end">
                <div class="col-md-3">
                    <label class="small font-weight-bold">1. Pilih Kelas</label>
                    <select name="rombel_id" class="form-control" onchange="this.form.submit()">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($rombels as $r)
                            <option value="{{ $r->id }}" {{ request('rombel_id') == $r->id ? 'selected' : '' }}>
                                {{ $r->nama_rombel }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if(request('rombel_id'))
                <div class="col-md-3">
                    <label class="small font-weight-bold">2. Pilih Mata Pelajaran</label>
                    <select name="mapel_id" class="form-control" onchange="this.form.submit()">
                        <option value="">-- Semua Mapel --</option>
                        @foreach($mapels as $m)
                            <option value="{{ $m->id }}" {{ request('mapel_id') == $m->id ? 'selected' : '' }}>
                                {{ $m->nama_mapel }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="small font-weight-bold">3. Rentang Waktu</label>
                    <div class="input-group">
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date', date('Y-m-01')) }}">
                        <div class="input-group-append"><span class="input-group-text">s/d</span></div>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date', date('Y-m-d')) }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-search"></i> Cari
                    </button>
                </div>
                @endif
            </form>
        </div>
    </div>

    @if(request('rombel_id') && $siswa_stats->count() > 0)
    {{-- Ringkasan Dashboard --}}
    <div class="row">
        {{-- Kehadiran Kelas --}}
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Avg. Kehadiran ({{ $rombels->where('id', request('rombel_id'))->first()->nama_rombel }})
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistik_kelas['persentase_hadir'] }}%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info Mapel --}}
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Status Pantauan</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                {{ request('mapel_id') ? $mapels->where('id', request('mapel_id'))->first()->nama_mapel : 'Semua Mata Pelajaran' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info Pertemuan --}}
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Intensitas Pertemuan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $total_sesi }} Kali Sesi</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Detail Statistik Murid --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary">
            <h6 class="m-0 font-weight-bold text-white">Ranking Kehadiran Siswa</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered" width="100%" cellspacing="0">
                    <thead class="bg-light text-dark">
                        <tr class="text-center">
                            <th>Siswa</th>
                            <th width="100">Hadir</th>
                            <th width="100">Izin/Sakit</th>
                            <th width="100">Alpa</th>
                            <th width="150">Persentase</th>
                            <th width="200">Visual Performance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($siswa_stats->sortByDesc('total_hadir') as $stat)
                        @php 
                            $persen = $stat->persen; // Menggunakan data dari controller
                            $color = $persen >= 85 ? 'success' : ($persen >= 75 ? 'primary' : ($persen >= 60 ? 'warning' : 'danger'));
                        @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $stat->foto ? asset('storage/'.$stat->foto) : 'https://ui-avatars.com/api/?name='.urlencode($stat->nama_siswa) }}" 
                                         class="rounded-circle mr-3" width="35" height="35" style="object-fit: cover;">
                                    <div>
                                        <div class="font-weight-bold text-gray-800">{{ $stat->nama_siswa }}</div>
                                        <small class="text-muted">NISN: {{ $stat->nisn }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center font-weight-bold text-success">{{ $stat->total_hadir }}</td>
                            <td class="text-center text-info">{{ $stat->total_izin }}</td>
                            <td class="text-center text-danger">{{ $stat->total_alpa }}</td>
                            <td class="text-center">
                                <span class="badge badge-{{ $color }} px-3 py-2">
                                    {{ $persen }}%
                                </span>
                            </td>
                            <td>
                                <div class="progress mt-2" style="height: 12px;">
                                    <div class="progress-bar bg-{{ $color }}" role="progressbar" 
                                        style="width: {{ $persen }}%" 
                                        aria-valuenow="{{ $persen }}" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Log Histori Terakhir --}}
    <div class="card shadow mb-4">
        <a href="#collapseLog" class="d-block card-header py-3 no-arrow" data-toggle="collapse" role="button">
            <h6 class="m-0 font-weight-bold text-secondary text-center"><i class="fas fa-history"></i> Lihat Log Presensi Terakhir (Histori)</h6>
        </a>
        <div class="collapse" id="collapseLog">
            <div class="card-body">
                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Nama</th>
                            <th>Mapel</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($presensis->take(20) as $p)
                        <tr>
                            <td><small>{{ \Carbon\Carbon::parse($p->waktu_scan)->format('d/m H:i') }}</small></td>
                            <td>{{ $p->siswa->nama_siswa }}</td>
                            <td>{{ $p->jadwal->mapel->nama_mapel }}</td>
                            <td><span class="badge badge-light">{{ $p->keterangan }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @else
        <div class="card shadow mb-4">
            <div class="card-body text-center py-5">
                <img src="https://illustrations.popsy.co/amber/searching.svg" style="width: 200px;" class="mb-4">
                <h5 class="text-gray-800">Belum ada data untuk ditampilkan</h5>
                <p class="text-muted">Silakan pilih <strong>Kelas</strong> dan tentukan <strong>Mata Pelajaran</strong> untuk melihat statistik detail.</p>
            </div>
        </div>
    @endif
</div>
@endsection