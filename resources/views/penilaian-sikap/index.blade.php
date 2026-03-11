@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Penilaian Sikap (Afektif)</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- FILTER DATA --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter mr-2"></i>Filter Data Siswa</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('penilaian-sikap.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="periode_id" class="font-weight-bold">Periode Penilaian</label>
                        <select name="periode_id" id="periode_id" class="form-control">
                            <option value="">-- Semua Periode --</option>
                            @foreach($periodes as $periode)
                                <option value="{{ $periode->id }}" {{ request('periode_id') == $periode->id ? 'selected' : '' }}>
                                    {{ $periode->nama_periode }} {{ $periode->is_active ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="jurusan_id" class="font-weight-bold">Jurusan</label>
                        <select name="jurusan_id" id="jurusan_id" class="form-control" onchange="this.form.submit()">
                            <option value="">-- Pilih Jurusan --</option>
                            @foreach($jurusans as $jurusan)
                                <option value="{{ $jurusan->id }}" {{ request('jurusan_id') == $jurusan->id ? 'selected' : '' }}>
                                    {{ $jurusan->nama_jurusan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="kelas_id" class="font-weight-bold">Kelas</label>
                        <select name="kelas_id" id="kelas_id" class="form-control">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($semuaKelas as $kelas)
                                <option value="{{ $kelas->id }}" data-jurusan="{{ $kelas->jurusan_id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                                    {{ $kelas->tingkat }} {{ $kelas->nama_kelas }}
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

    {{-- TABEL DATA --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-list mr-2"></i>Daftar Siswa</h6>
            @if(request('periode_id'))
                <span class="badge badge-info px-3 py-2">Periode Terpilih</span>
            @endif
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th width="50">No</th>
                            <th>NISN</th>
                            <th>Nama Siswa</th>
                            <th>Gender</th>
                            <th width="150" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswas as $index => $siswa)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $siswa->nisn }}</td>
                            <td>{{ strtoupper($siswa->nama_siswa) }}</td>
                            <td class="text-center">
                                @if($siswa->jenis_kelamin == 'L')
                                    <span class="badge badge-info px-2">Laki-laki</span>
                                @else
                                    <span class="badge badge-danger px-2">Perempuan</span>
                                @endif
                            </td>
                            <td class="text-center align-middle">
                                <div class="d-inline-flex">
                                    <a href="{{ route('penilaian-sikap.show', ['siswa_id' => $siswa->id, 'periode_id' => request('periode_id')]) }}" class="btn btn-info btn-sm mr-2" title="Detail">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                    <a href="{{ route('penilaian-sikap.form', ['siswa_id' => $siswa->id, 'periode_id' => request('periode_id')]) }}" class="btn btn-primary btn-sm" title="Beri Nilai">
                                        <i class="fas fa-edit"></i> Nilai
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Data siswa tidak ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const jurusanSelect = document.getElementById('jurusan_id');
        const kelasSelect = document.getElementById('kelas_id');
        const options = Array.from(kelasSelect.options);

        function filterKelas() {
            const selectedJurusan = jurusanSelect.value;
            
            // Set first option (-- Pilih Kelas --) visible
            options[0].style.display = 'block';
            
            for (let i = 1; i < options.length; i++) {
                const option = options[i];
                const optionJurusan = option.getAttribute('data-jurusan');
                
                if (!selectedJurusan || optionJurusan === selectedJurusan) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                    // Deselect if hidden
                    if (option.selected) {
                        kelasSelect.value = '';
                    }
                }
            }
        }

        jurusanSelect.addEventListener('change', filterKelas);
        
        // Run on load to handle pre-selected values
        filterKelas();
    });
</script>
@endpush
