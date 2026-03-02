@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Manajemen Siswa</h1>

    {{-- CARD FILTER --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter mr-2"></i>Filter Data Siswa</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('siswas.index') }}" method="GET" class="row">
                <div class="col-md-3 mb-3">
                    <label class="small font-weight-bold">Cari Nama/NISN</label>
                    <input type="text" name="search" class="form-control" placeholder="Ketik nama..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="small font-weight-bold">Jurusan</label>
                    <select name="jurusan" class="form-control">
                        <option value="">Semua Jurusan</option>
                        @foreach($list_jurusan as $j)
                            <option value="{{ $j }}" {{ request('jurusan') == $j ? 'selected' : '' }}>{{ $j }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="small font-weight-bold">Kelas</label>
                    <select name="kelas" class="form-control">
                        <option value="">Semua Kelas</option>
                        @foreach($list_kelas as $k)
                            <option value="{{ $k }}" {{ request('kelas') == $k ? 'selected' : '' }}>{{ $k }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="small font-weight-bold">L/P</label>
                    <select name="jk" class="form-control">
                        <option value="">Semua</option>
                        <option value="L" {{ request('jk') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ request('jk') == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-block">Filter</button>
                    <a href="{{ route('siswas.index') }}" class="btn btn-secondary ml-2"><i class="fas fa-undo"></i></a>
                </div>
            </form>
        </div>
    </div>

    {{-- TABEL DATA --}}
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th width="50">Foto</th>
                            <th>Identitas</th>
                            <th>Nama Siswa</th>
                            <th>Gender</th>
                            <th>Kelas & Jurusan</th>
                            <th width="180">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswas as $siswa)
                        <tr>
                            <td class="text-center">
                                @if(isset($siswa['foto']) && $siswa['foto'])
                                    <img src="{{ $siswa['foto'] }}" width="40" height="40" class="rounded-circle shadow-sm" style="object-fit: cover;">
                                @else
                                    <div class="rounded-circle bg-gray-200 d-flex align-items-center justify-content-center mx-auto" style="width: 40px; height: 40px;">
                                        <i class="fas fa-user text-gray-400"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted d-block">NISN:</small>
                                <strong>{{ $siswa['nisn'] ?? $siswa['nis'] }}</strong>
                            </td>
                            <td>{{ strtoupper($siswa['nama']) }}</td>
                            <td class="text-center">
                                @php $jk = $siswa['jk'] ?? ($siswa['jenis_kelamin'] ?? '-'); @endphp
                                @if($jk == 'L')
                                    <span class="badge badge-info px-2">Laki-laki</span>
                                @else
                                    <span class="badge badge-danger px-2">Perempuan</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-light border text-dark">{{ $siswa['nama_rombel'] ?? 'N/A' }}</span>
                                <small class="d-block text-muted mt-1">{{ $siswa['jurusan'] ?? '-' }}</small>
                            </td>
                            <td>
                                <a href="#" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('siswas.sync', $siswa['nisn'] ?? $siswa['nis']) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-camera mr-1"></i> Registrasi Wajah
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Data tidak ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection