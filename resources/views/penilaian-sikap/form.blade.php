@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Form Penilaian Sikap</h1>
        <a href="{{ route('penilaian-sikap.index') }}" class="btn btn-secondary btn-sm shadow-sm"><i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali</a>
    </div>

    <div class="row">
        <div class="col-xl-8 col-lg-8 d-flex flex-column">
            <div class="card shadow mb-4 flex-grow-1">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Beri Penilaian Sikap</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('penilaian-sikap.store', $siswa->id) }}" method="POST">
                        @csrf
                        
                        <div class="alert alert-info">
                            <small>
                                <strong>Panduan Nilai:</strong> 1 (Sangat Kurang), 2 (Kurang), 3 (Cukup), 4 (Baik), 5 (Sangat Baik)
                            </small>
                        </div>
                        
                        @php
                            $aspek = [
                                'tanggung_jawab' => 'Tanggung Jawab (Menyelesaikan tugas, mengakui kesalahan)',
                                'kejujuran' => 'Kejujuran (Tidak menyontek, mengakui hasil karya sendiri)',
                                'sopan_santun' => 'Sopan Santun & Etika (Cara komunikasi ke guru/teman)',
                                'kemandirian' => 'Kemandirian (Mengerjakan tugas tanpa bergantung orang lain)',
                                'kerja_sama' => 'Kerja Sama / Gotong Royong (Bekerja dalam tim)',
                            ];
                        @endphp

                        @foreach($aspek as $field => $label)
                        <div class="form-group row align-items-center mb-3">
                            <label class="col-sm-5 col-form-label font-weight-bold">{{ $label }} <span class="text-danger">*</span></label>
                            <div class="col-sm-7 d-flex flex-wrap align-items-center mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                <div class="custom-control custom-radio custom-control-inline mr-3 mb-2">
                                    <input type="radio" id="{{ $field }}_{{ $i }}" name="{{ $field }}" class="custom-control-input" value="{{ $i }}" {{ (old($field, $penilaian->$field ?? 0) == $i) ? 'checked' : '' }} required>
                                    <label class="custom-control-label" for="{{ $field }}_{{ $i }}">{{ $i }}</label>
                                </div>
                                @endfor
                            </div>
                            @error($field)
                                <div class="col-sm-12 text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <hr class="mt-2 mb-3">
                        @endforeach

                        <div class="form-group">
                            <label for="catatan" class="font-weight-bold">Catatan Tambahan (Opsional)</label>
                            <textarea class="form-control @error('catatan') is-invalid @enderror" id="catatan" name="catatan" rows="4" placeholder="Berikan catatan tambahan jika ada (misal: Siswa sangat aktif berkembang di minggu kedua)">{{ old('catatan', $penilaian->catatan ?? '') }}</textarea>
                            @error('catatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group text-right mb-0">
                            <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save mr-1"></i> Simpan Penilaian</button>
                        </div>
                    </form>
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
                            <strong>Status Penilaian:</strong>
                            @if($penilaian)
                                <span class="badge badge-success float-right p-1"><i class="fas fa-check"></i> Sudah Dinilai</span>
                            @else
                                <span class="badge badge-warning float-right p-1"><i class="fas fa-clock"></i> Belum Dinilai</span>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card shadow mb-4 flex-grow-1">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info"><i class="fas fa-info-circle mr-1"></i> Rubrik Penilaian</h6>
                </div>
                <div class="card-body py-3">
                    <div class="mb-2">
                        <strong class="text-success small">5 - Sangat Baik:</strong>
                        <p class="small text-muted mb-0">Selalu menunjukkan sikap positif, menjadi contoh bagi yang lain.</p>
                    </div>
                    <div class="mb-2">
                        <strong class="text-primary small">4 - Baik:</strong>
                        <p class="small text-muted mb-0">Sering menunjukkan sikap positif, jarang melakukan pelanggaran.</p>
                    </div>
                    <div class="mb-2">
                        <strong class="text-warning small">3 - Cukup:</strong>
                        <p class="small text-muted mb-0">Kadang menunjukkan sikap positif, perlu sedikit bimbingan.</p>
                    </div>
                    <div class="mb-2">
                        <strong class="text-danger small">2 - Kurang:</strong>
                        <p class="small text-muted mb-0">Sering menunjukkan sikap negatif, perlu bimbingan intensif.</p>
                    </div>
                    <div>
                        <strong class="text-dark small">1 - Sangat Kurang:</strong>
                        <p class="small text-muted mb-0">Selalu menunjukkan sikap negatif, perlu penanganan khusus.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
