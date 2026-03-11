{{-- 
    File: presensi/index.blade.php
    Fungsi: Menampilkan halaman analitik terpadu rekapitulasi presensi siswa.
    Berisi statistik laporan kehadiran per kelas/mapel, rentang filter pencarian tanggal, 
    serta ringkasan log history absensi terbaru.
--}}
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
                <div class="col-md-2">
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
                <div class="col-md-5">
                    <label class="small font-weight-bold">3. Rentang Waktu</label>
                    <div class="d-flex">
                        <div class="input-group flex-grow-1">
                            <input type="date" id="start_date" name="start_date" class="form-control" value="{{ request('start_date', date('Y-m-d')) }}">
                            <div class="input-group-prepend input-group-append">
                                <span class="input-group-text bg-light border-left-0 border-right-0 px-2">s/d</span>
                            </div>
                            <input type="date" id="end_date" name="end_date" class="form-control" value="{{ request('end_date', date('Y-m-d')) }}">
                        </div>
                        <div class="dropdown ml-2">
                            <button class="btn btn-outline-primary dropdown-toggle h-100" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Pintasan Waktu">
                                <i class="fas fa-history"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                                <h6 class="dropdown-header">Rentang Cepat</h6>
                                <a class="dropdown-item" href="#" onclick="setQuickDate(0)"><i class="fas fa-calendar-day fa-sm fa-fw mr-2 text-gray-400"></i>Hari Ini</a>
                                <a class="dropdown-item" href="#" onclick="setQuickDate(1)"><i class="fas fa-history fa-sm fa-fw mr-2 text-gray-400"></i>1 Bulan Terakhir</a>
                                <a class="dropdown-item" href="#" onclick="setQuickDate(3)"><i class="fas fa-history fa-sm fa-fw mr-2 text-gray-400"></i>3 Bulan Terakhir</a>
                                <a class="dropdown-item" href="#" onclick="setQuickDate(6)"><i class="fas fa-history fa-sm fa-fw mr-2 text-gray-400"></i>6 Bulan Terakhir</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 mt-3 mt-md-0">
                    <label class="small font-weight-bold d-none d-md-block">&nbsp;</label>
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
                            <th width="70">Hadir</th>
                            <th width="70">Izin</th>
                            <th width="70">Sakit</th>
                            <th width="70">Alpa</th>
                            <th width="100">Aksi</th>
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
                            <td class="text-center text-warning">{{ $stat->total_sakit }}</td>
                            <td class="text-center text-danger">{{ $stat->total_alpa }}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#modalDetail{{ $stat->id }}">
                                    <i class="fas fa-list"></i> Detail
                                </button>
                                
                                {{-- Modal Detail --}}
                                <div class="modal fade" id="modalDetail{{ $stat->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
                                        <div class="modal-content text-left">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title"><i class="fas fa-user"></i> Detail Presensi: {{ $stat->nama_siswa }}</h5>
                                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <ul class="nav nav-pills nav-fill mb-3" id="pills-tab-{{ $stat->id }}" role="tablist">
                                                    <li class="nav-item">
                                                        <a class="nav-link active py-1 filter-btn text-secondary" data-target="TR-{{ $stat->id }}" data-filter="Semua" href="#">Semua ({{ count($stat->detail) }})</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link py-1 filter-btn text-secondary" data-target="TR-{{ $stat->id }}" data-filter="Hadir" href="#">Hadir ({{ $stat->total_hadir }})</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link py-1 filter-btn text-secondary" data-target="TR-{{ $stat->id }}" data-filter="Izin" href="#">Izin ({{ $stat->total_izin }})</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link py-1 filter-btn text-secondary" data-target="TR-{{ $stat->id }}" data-filter="Sakit" href="#">Sakit ({{ $stat->total_sakit }})</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link py-1 filter-btn text-secondary" data-target="TR-{{ $stat->id }}" data-filter="Alpa" href="#">Alpa ({{ $stat->total_alpa }})</a>
                                                    </li>
                                                </ul>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-sm table-hover">
                                                        <thead class="bg-light">
                                                            <tr class="text-center">
                                                                <th width="50">No</th>
                                                                <th>Tanggal & Waktu</th>
                                                                <th>Mata Pelajaran</th>
                                                                <th>Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse($stat->detail as $index => $d)
                                                                <tr class="TR-{{ $stat->id }}" data-status="{{ $d->keterangan }}">
                                                                    <td class="text-center">{{ $index + 1 }}</td>
                                                                    <td>{{ \Carbon\Carbon::parse($d->waktu_scan)->locale('id')->translatedFormat('l, d F Y') }}</td>
                                                                    <td>{{ $d->jadwal->mapel->nama_mapel ?? '-' }}</td>
                                                                    <td class="text-center">
                                                                        <span class="badge badge-secondary px-2 py-1">{{ $d->keterangan }}</span>
                                                                        @if($d->latitude && $d->longitude)
                                                                            <a href="https://www.google.com/maps?q={{ $d->latitude }},{{ $d->longitude }}" target="_blank" class="btn btn-sm btn-outline-info ml-1 border-0 p-0" title="Lihat Lokasi">
                                                                                <i class="fas fa-map-marker-alt"></i>
                                                                            </a>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="4" class="text-center text-muted py-3">Belum ada riwayat absensi.</td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
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
                            <td>
                                <span class="badge badge-light">{{ $p->keterangan }}</span>
                                @if($p->latitude && $p->longitude)
                                    <a href="https://www.google.com/maps?q={{ $p->latitude }},{{ $p->longitude }}" target="_blank" class="text-info ml-1" title="Lihat Lokasi">
                                        <i class="fas fa-map-marker-alt fa-xs"></i>
                                    </a>
                                @endif
                            </td>
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

<style>
/* Override nav-pills active state to match neutral theme */
.nav-pills .nav-link.active.text-secondary {
    background-color: #6c757d !important;
    color: #fff !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Handle nav pill states
            const ul = this.closest('ul');
            ul.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            
            // Handle table row filtering
            const targetClass = this.getAttribute('data-target');
            const filterValue = this.getAttribute('data-filter');
            
            const rows = document.querySelectorAll('.' + targetClass);
            rows.forEach(row => {
                if(filterValue === 'Semua') {
                    row.style.display = '';
                } else if(row.getAttribute('data-status') === filterValue) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
});

function setQuickDate(months) {
    const endInput = document.getElementById('end_date');
    const startInput = document.getElementById('start_date');
    const form = startInput.closest('form');
    
    // Tentukan hari ini (untuk end_date)
    const today = new Date();
    const endStr = today.toISOString().split('T')[0];
    
    // Tentukan start date dengan mengurangi x bulan
    const targetDate = new Date(today);
    targetDate.setMonth(targetDate.getMonth() - months);
    const startStr = targetDate.toISOString().split('T')[0];
    
    // Terapkan ke input
    endInput.value = endStr;
    startInput.value = startStr;
    
    // Kirim formulir setelah menetapkan nilai
    if(form) form.submit();
}
</script>
@endsection