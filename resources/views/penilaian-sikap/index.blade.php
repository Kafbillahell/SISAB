@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Penilaian Sikap (Afektif)</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- TABEL DATA --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-list mr-2"></i>Daftar Siswa</h6>
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
                                    <a href="{{ route('penilaian-sikap.show', $siswa->id) }}" class="btn btn-info btn-sm mr-2" title="Detail">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                    <a href="{{ route('penilaian-sikap.form', $siswa->id) }}" class="btn btn-primary btn-sm" title="Beri Nilai">
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
