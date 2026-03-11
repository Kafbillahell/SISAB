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
                    @forelse($penilaians as $penilaian)
                        <div class="mb-4 pb-4 border-bottom">
                            <h5 class="font-weight-bold row align-items-center">
                                <div class="col">
                                    <i class="fas fa-calendar-alt text-primary mr-2"></i> {{ $penilaian->periode->nama_periode }}
                                </div>
                                <div class="col-auto">
                                    <span class="badge badge-info text-right" style="font-size: 14px;">Penilai: {{ $penilaian->penilai->name }}</span>
                                </div>
                            </h5>
                            <div class="table-responsive mt-3">
                                <table class="table table-hover table-bordered">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th>Aspek Penilaian</th>
                                            <th width="120" class="text-center">Skor (1-5)</th>
                                        </tr>
                                    </thead>
                                <tbody>
                                    <tr>
                                        <td class="align-middle"><strong>1. Tanggung Jawab</strong><br><small class="text-muted">Mampu menyelesaikan tugas yang diberikan dan mengakui kesalahan jika ada.</small></td>
                                        <td class="text-center align-middle">
                                            <span class="badge badge-{{ $penilaian->tanggung_jawab >= 4 ? 'success' : ($penilaian->tanggung_jawab == 3 ? 'warning' : 'danger') }} p-2" style="font-size: 14px; min-width: 40px;">
                                                {{ $penilaian->tanggung_jawab }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-middle"><strong>2. Kejujuran</strong><br><small class="text-muted">Tidak menyontek saat ujian atau mengakui hasil karya sendiri.</small></td>
                                        <td class="text-center align-middle">
                                            <span class="badge badge-{{ $penilaian->kejujuran >= 4 ? 'success' : ($penilaian->kejujuran == 3 ? 'warning' : 'danger') }} p-2" style="font-size: 14px; min-width: 40px;">
                                                {{ $penilaian->kejujuran }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-middle"><strong>3. Sopan Santun (Etika)</strong><br><small class="text-muted">Cara siswa berkomunikasi dengan baik kepada guru dan sesama teman.</small></td>
                                        <td class="text-center align-middle">
                                            <span class="badge badge-{{ $penilaian->sopan_santun >= 4 ? 'success' : ($penilaian->sopan_santun == 3 ? 'warning' : 'danger') }} p-2" style="font-size: 14px; min-width: 40px;">
                                                {{ $penilaian->sopan_santun }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-middle"><strong>4. Kemandirian</strong><br><small class="text-muted">Sejauh mana siswa bisa mengerjakan tugas tanpa selalu bergantung pada orang lain.</small></td>
                                        <td class="text-center align-middle">
                                            <span class="badge badge-{{ $penilaian->kemandirian >= 4 ? 'success' : ($penilaian->kemandirian == 3 ? 'warning' : 'danger') }} p-2" style="font-size: 14px; min-width: 40px;">
                                                {{ $penilaian->kemandirian }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-middle"><strong>5. Kerja Sama (Gotong Royong)</strong><br><small class="text-muted">Kemampuan siswa untuk bekerja dengan baik dalam tim atau kelompok.</small></td>
                                        <td class="text-center align-middle">
                                            <span class="badge badge-{{ $penilaian->kerja_sama >= 4 ? 'success' : ($penilaian->kerja_sama == 3 ? 'warning' : 'danger') }} p-2" style="font-size: 14px; min-width: 40px;">
                                                {{ $penilaian->kerja_sama }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                                </table>
                            </div>

                            <hr>
                            <h6 class="font-weight-bold">Catatan:</h6>
                            <div class="p-3 bg-light rounded border" style="min-height: 80px;">
                                {{ $penilaian->catatan ?: 'Tidak ada catatan untuk siswa ini.' }}
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="fas fa-clipboard-list fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-muted">Siswa ini belum memiliki penilaian sikap untuk periode yang dipilih.</h5>
                            <p class="mb-4">Silakan berikan penilaian pertama kali.</p>
                            <a href="{{ route('penilaian-sikap.form', ['siswa_id' => $siswa->id, 'periode_id' => request('periode_id')]) }}" class="btn btn-primary"><i class="fas fa-plus mr-1"></i> Beri Penilaian Sekarang</a>
                        </div>
                    @endforelse
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
