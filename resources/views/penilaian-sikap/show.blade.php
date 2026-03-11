{{-- 
    File: penilaian-sikap/show.blade.php
    Fungsi: Menampilkan detail riwayat dan pemetaan penilaian sikap siswa.
    Menyajikan visualisasi Grafik Radar (menggunakan Chart.js) untuk melihat kesimbangan 
    sikap, serta riwayat timeline historis penilaian dari berbagai periode belajar.
--}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Penilaian Sikap</h1>
        <a href="{{ route('penilaian-sikap.index') }}" class="btn btn-secondary btn-sm shadow-sm"><i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali</a>
    </div>

    <div class="row">
        <div class="col-xl-8 col-lg-8 d-flex flex-column">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter mr-2"></i>Filter Data Penilaian</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('penilaian-sikap.show', $siswa->id) }}" method="GET">
                        <div class="row">
                            <div class="col-md-9 mb-3">
                                <label for="periode_id" class="font-weight-bold">Periode Penilaian</label>
                                <select name="periode_id" id="periode_id" class="form-control" onchange="this.form.submit()">
                                    <option value="">-- Semua Periode --</option>
                                    @foreach($periodes as $periode)
                                        <option value="{{ $periode->id }}" {{ request('periode_id') == $periode->id ? 'selected' : '' }}>
                                            {{ $periode->nama_periode }} {{ $periode->is_active ? '(Aktif)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search mr-1"></i> Tampilkan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow mb-4 flex-grow-1">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Hasil Penilaian</h6>
                    <a href="{{ route('penilaian-sikap.form', ['siswa_id' => $siswa->id, 'periode_id' => request('periode_id')]) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit Nilai</a>
                </div>
                <div class="card-body">
                    {{-- TAB NAVIGASI --}}
                    <ul class="nav nav-tabs mb-4" id="penilaianTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="grafik-tab" data-toggle="tab" href="#grafik" role="tab" aria-controls="grafik" aria-selected="true"><i class="fas fa-chart-pie mr-2"></i>Grafik Sikap (Radar)</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="riwayat-tab" data-toggle="tab" href="#riwayat" role="tab" aria-controls="riwayat" aria-selected="false"><i class="fas fa-history mr-2"></i>Riwayat & Feedback</a>
                        </li>
                    </ul>

                    <div class="tab-content" id="penilaianTabContent">
                        {{-- TAB GRAFIK RADAR --}}
                        <div class="tab-pane fade show active" id="grafik" role="tabpanel" aria-labelledby="grafik-tab">
                            @if($penilaians->count() > 0)
                                @php
                                    // Ambil penilaian terbaru untuk grafik
                                    $penilaianTerbaru = $penilaians->first();
                                    $labels = ['Tanggung Jawab', 'Kejujuran', 'Sopan Santun', 'Kemandirian', 'Kerja Sama'];
                                    $dataScores = [
                                        $penilaianTerbaru->tanggung_jawab,
                                        $penilaianTerbaru->kejujuran,
                                        $penilaianTerbaru->sopan_santun,
                                        $penilaianTerbaru->kemandirian,
                                        $penilaianTerbaru->kerja_sama
                                    ];
                                @endphp
                                <div class="text-center mb-4">
                                    <h5 class="font-weight-bold text-gray-800">Pemetaan Sikap: {{ $penilaianTerbaru->periode->nama_periode }}</h5>
                                    <p class="text-muted small">Visualisasi area sikap dominan dan yang perlu ditingkatkan (Skala 1-5)</p>
                                </div>
                                <div class="chart-area d-flex justify-content-center align-items-center" style="height: 350px;">
                                    <canvas id="radarChart"></canvas>
                                </div>
                                <div class="mt-4 text-center">
                                    <span class="badge badge-info px-3 py-2"><i class="fas fa-info-circle mr-1"></i> Semakin luas jaring, semakin seimbang sikap siswa.</span>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-chart-area fa-3x text-gray-300 mb-3"></i>
                                    <h5 class="text-muted">Data grafik belum tersedia.</h5>
                                </div>
                            @endif
                        </div>

                        {{-- TAB RIWAYAT TIMELINE --}}
                        <div class="tab-pane fade" id="riwayat" role="tabpanel" aria-labelledby="riwayat-tab">
                            @forelse($penilaians as $penilaian)
                                <div class="timeline-item mb-4 pb-4 border-bottom position-relative pl-4" style="border-left: 3px solid #4e73df;">
                                    <div class="position-absolute bg-primary rounded-circle" style="width: 15px; height: 15px; left: -9px; top: 5px; border: 3px solid white;"></div>
                                    
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h5 class="font-weight-bold text-primary mb-0">
                                            <i class="fas fa-calendar-alt mr-2"></i> {{ $penilaian->periode->nama_periode }}
                                        </h5>
                                        <span class="badge badge-light border text-dark">
                                            Penilai: {{ $penilaian->penilai->name }} | {{ $penilaian->created_at->format('d M Y') }}
                                        </span>
                                    </div>
    
                                    <div class="row mt-3">
                                        <div class="col-md-6 mb-3">
                                            <h6 class="font-weight-bold text-gray-800 text-sm mb-2">Rincian Skor</h6>
                                            <div class="d-flex flex-wrap">
                                                <div class="badge badge-{{ $penilaian->tanggung_jawab >= 4 ? 'success' : 'warning' }} mr-2 mb-2 p-2">Tgjwb: {{ $penilaian->tanggung_jawab }}</div>
                                                <div class="badge badge-{{ $penilaian->kejujuran >= 4 ? 'success' : 'warning' }} mr-2 mb-2 p-2">Jujur: {{ $penilaian->kejujuran }}</div>
                                                <div class="badge badge-{{ $penilaian->sopan_santun >= 4 ? 'success' : 'warning' }} mr-2 mb-2 p-2">Sopan: {{ $penilaian->sopan_santun }}</div>
                                                <div class="badge badge-{{ $penilaian->kemandirian >= 4 ? 'success' : 'warning' }} mr-2 mb-2 p-2">Mandiri: {{ $penilaian->kemandirian }}</div>
                                                <div class="badge badge-{{ $penilaian->kerja_sama >= 4 ? 'success' : 'warning' }} mr-2 mb-2 p-2">Kerjasama: {{ $penilaian->kerja_sama }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="font-weight-bold text-gray-800 text-sm mb-2">Catatan Feedback</h6>
                                            <div class="p-3 bg-light rounded border border-left-info" style="min-height: 80px; font-size: 0.9rem;">
                                                {!! nl2br(e($penilaian->catatan ?: 'Tidak ada catatan/feedback untuk periode ini.')) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5">
                                    <i class="fas fa-history fa-3x text-gray-300 mb-3"></i>
                                    <h5 class="text-muted">Siswa ini belum memiliki riwayat penilaian sikap.</h5>
                                    <p class="mb-4">Silakan berikan penilaian pertama kali melalui halaman daftar kelas.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-4 d-flex flex-column">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Profil Siswa</h6>
                </div>
                <div class="card-body text-center">
                    @if(isset($siswa->foto) && $siswa->foto)
                        <img src="{{ $siswa->foto }}" class="rounded-circle mb-2 img-fluid shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-gray-200 d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 80px; height: 80px;">
                            <i class="fas fa-user fa-3x text-gray-400"></i>
                        </div>
                    @endif
                    
                    <h5 class="font-weight-bold mb-1">{{ strtoupper($siswa->nama_siswa) }}</h5>
                    <p class="text-muted mb-3">{{ $siswa->nisn }}</p>
                    
                    <ul class="list-group list-group-flush text-left">
                        <li class="list-group-item px-0">
                            <strong>Gender:</strong> 
                            <span class="float-right">{{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                        </li>
                        <li class="list-group-item px-0">
                            <strong>Predikat (Penilaian Terakhir):</strong>
                            @if($penilaians->count() > 0)
                                @php
                                    $penilaian = $penilaians->first();
                                    $total = $penilaian->tanggung_jawab + $penilaian->kejujuran + $penilaian->sopan_santun + $penilaian->kemandirian + $penilaian->kerja_sama;
                                    $predikat = '-';
                                    $badge = 'secondary';
                                    if ($total >= 21) { $predikat = 'Sangat Baik'; $badge = 'success'; }
                                    elseif ($total >= 16) { $predikat = 'Baik'; $badge = 'primary'; }
                                    elseif ($total >= 11) { $predikat = 'Cukup'; $badge = 'warning'; }
                                    elseif ($total >= 6) { $predikat = 'Kurang'; $badge = 'danger'; }
                                    else { $predikat = 'Sangat Kurang'; $badge = 'dark'; }
                                @endphp
                                <span class="badge badge-{{ $badge }} float-right p-1 mt-1">{{ $predikat }}</span>
                            @else
                                <span class="badge badge-warning float-right p-1 mt-1"><i class="fas fa-clock"></i> Belum Dinilai</span>
                            @endif
                        </li>
                        <li class="list-group-item px-0">
                            <strong>Total Skor (Penilaian Terakhir):</strong>
                            @if($penilaians->count() > 0)
                                <span class="badge badge-light border text-dark float-right p-2" style="font-size: 14px;">{{ $total }} / 25</span>
                            @else
                                <span class="badge badge-light border text-dark float-right">-</span>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card shadow mb-4 flex-grow-1">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info"><i class="fas fa-chart-pie mr-1"></i> Kategori Skor</h6>
                </div>
                <div class="card-body py-3">
                    <p class="small text-muted mb-2">Total skor dari 5 aspek (Maks 25).</p>
                    <ul class="list-group list-group-flush text-left small">
                        <li class="list-group-item px-0 py-1 d-flex justify-content-between align-items-center">
                            <span><strong>21 - 25</strong></span>
                            <span class="badge badge-success px-2 py-1">Sangat Baik</span>
                        </li>
                        <li class="list-group-item px-0 py-1 d-flex justify-content-between align-items-center">
                            <span><strong>16 - 20</strong></span>
                            <span class="badge badge-primary px-2 py-1">Baik</span>
                        </li>
                        <li class="list-group-item px-0 py-1 d-flex justify-content-between align-items-center">
                            <span><strong>11 - 15</strong></span>
                            <span class="badge badge-warning px-2 py-1">Cukup</span>
                        </li>
                        <li class="list-group-item px-0 py-1 d-flex justify-content-between align-items-center">
                            <span><strong>6 - 10</strong></span>
                            <span class="badge badge-danger px-2 py-1">Kurang</span>
                        </li>
                        <li class="list-group-item px-0 py-1 d-flex justify-content-between align-items-center border-bottom-0">
                            <span><strong>5</strong></span>
                            <span class="badge badge-dark px-2 py-1 text-white">Sangat Kurang</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if($penilaians->count() > 0)
        const ctx = document.getElementById('radarChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: {!! json_encode($labels ?? []) !!},
                    datasets: [{
                        label: 'Skor Sikap',
                        data: {!! json_encode($dataScores ?? []) !!},
                        backgroundColor: 'rgba(78, 115, 223, 0.2)',
                        borderColor: 'rgba(78, 115, 223, 1)',
                        pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                        borderWidth: 2
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        r: {
                            min: 0,
                            max: 5,
                            angleLines: { color: 'rgba(0, 0, 0, 0.1)' },
                            grid: { color: 'rgba(0, 0, 0, 0.1)' },
                            pointLabels: {
                                font: { size: 14, family: "'Nunito', sans-serif", weight: 'bold' },
                                color: '#5a5c69'
                            },
                            ticks: {
                                stepSize: 1,
                                display: false // Sembunyikan angka di tengah mesh
                            }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            titleFont: { size: 14 },
                            bodyFont: { size: 14 },
                            displayColors: false,
                            callbacks: {
                                label: function(context) { return context.label + ': ' + context.raw + ' / 5 Skor'; }
                            }
                        }
                    }
                }
            });
        }
        @endif
    });
</script>
@endpush
