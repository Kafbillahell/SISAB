@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Input Manual Presensi</h1>
            <p class="mb-0 text-muted small">Tandai siswa yang <strong>Sakit</strong>, <strong>Izin</strong>, atau <strong>Alpa</strong></p>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('presensi.manual') }}" class="form-inline mb-3">
                <div class="form-group mr-2">
                    <label class="small mr-2">Pilih Kelas</label>
                    <select name="rombel_id" class="form-control" onchange="this.form.submit()">
                        <option value="">-- Pilih --</option>
                        @foreach($rombels as $r)
                            <option value="{{ $r->id }}" {{ (isset($selectedRombel) && $selectedRombel->id == $r->id) ? 'selected' : '' }}>{{ $r->nama_rombel }}</option>
                        @endforeach
                    </select>
                </div>
            </form>

            @if(isset($selectedRombel))
            <form method="POST" action="{{ route('presensi.manual.store') }}">
                @csrf
                <input type="hidden" name="rombel_id" value="{{ $selectedRombel->id }}">

                <div class="form-row align-items-end mb-3">
                    <div class="col-md-3">
                        <label class="small">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', date('Y-m-d')) }}">
                    </div>
                    <div class="col-md-5">
                        <label class="small">Pilih Jadwal (Hari ini)</label>
                        <select name="jadwal_id" class="form-control">
                            <option value="">-- Pilih Jadwal --</option>
                            @foreach($jadwals as $j)
                                <option value="{{ $j->id }}" {{ ($defaultJadwalId == $j->id) ? 'selected' : '' }}>
                                    {{ $j->hari }} - {{ $j->mapel->nama_mapel }} - {{ $j->guru->nama_guru }} ({{ $j->jam_mulai }}-{{ $j->jam_selesai }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-success btn-block">Simpan</button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Nama Siswa</th>
                                <th>Foto</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($siswas as $idx => $s)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td>{{ $s->nama_siswa }}</td>
                                <td>
                                    <img src="{{ $s->foto ? asset('storage/'.$s->foto) : 'https://ui-avatars.com/api/?name='.urlencode($s->nama_siswa) }}" width="40" height="40" class="rounded-circle" style="object-fit:cover">
                                </td>
                                <td>
                                    <select name="status[{{ $s->id }}]" class="form-control">
                                        <option value="">-- Tidak Diubah --</option>
                                        <option value="Sakit">Sakit</option>
                                        <option value="Izin">Izin</option>
                                        <option value="Alpa">Alpa</option>
                                    </select>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>
            @else
                <div class="alert alert-info">Tidak ada kelas terpilih. Jika Anda wali kelas, halaman ini akan otomatis menampilkan kelas Anda.</div>
            @endif
        </div>
    </div>
</div>
@endsection
