@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Rekap Presensi Siswa</h1>
        <a href="{{ route('presensi.scanner') }}" class="btn btn-success btn-sm shadow-sm">
            <i class="fas fa-camera fa-sm text-white-50"></i> Buka Scanner Wajah
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Waktu Scan</th>
                            <th>Nama Siswa</th>
                            <th>Rombel</th>
                            <th>Mata Pelajaran</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($presensis as $p)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($p->waktu_scan)->format('d/m/Y H:i') }}</td>
                            <td>{{ $p->siswa->nama_siswa }}</td>
                            <td>{{ $p->jadwal->rombel->nama_rombel }}</td>
                            <td>{{ $p->jadwal->mapel->nama_mapel }}</td>
                            <td>
                                <span class="badge badge-{{ $p->status == 'Hadir' ? 'success' : ($p->status == 'Alpa' ? 'danger' : 'warning') }}">
                                    {{ $p->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection