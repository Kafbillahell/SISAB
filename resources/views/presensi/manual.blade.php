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
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
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
                        <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', $selectedTanggal ?? date('Y-m-d')) }}" readonly>
                    </div>
                    <div class="col-md-5">
                        <label class="small">Pilih Jadwal (Hari ini)</label>
                        <select name="jadwal_id" class="form-control" tabindex="-1" style="pointer-events: none; background-color: #e9ecef;">
                            <option value="">-- Pilih Jadwal --</option>
                            @foreach($jadwals as $j)
                                <option value="{{ $j->id }}" {{ (isset($selectedJadwal) && $selectedJadwal == $j->id) ? 'selected' : '' }}>
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
                                <th class="text-center">#</th>
                                <th>Nama Siswa</th>
                                <th class="text-center">Foto</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($siswas as $idx => $s)
                            <tr>
                                <td class="align-middle text-center">{{ $idx + 1 }}</td>
                                <td class="align-middle">
                                    {{ $s->nama_siswa }}
                                    @php
                                        $pm = $presensiMap[$s->id] ?? null;
                                        $savedK = strtolower(trim((string)($pm['keterangan'] ?? '')));
                                        $savedS = strtolower(trim((string)($pm['status'] ?? '')));
                                    @endphp
                                    @if(in_array($savedS, ['sakit', 'izin', 'alpa']))
                                        <span class="text-danger ml-1" title="Sudah diinput manual">
                                            <i class="fas fa-exclamation-circle"></i>
                                        </span>
                                    @endif
                                </td>
                                <td class="align-middle text-center">
                                    <img src="{{ $s->foto ? asset('storage/'.$s->foto) : 'https://ui-avatars.com/api/?name='.urlencode($s->nama_siswa) }}" width="40" height="40" class="rounded-circle" style="object-fit:cover">
                                </td>
                                <td class="align-middle text-center">
                                    @if($savedK === 'hadir' || $savedS === 'hadir')
                                        <div class="badge badge-success">Hadir (otomatis)</div>
                                    @else
                                        <select name="status[{{ $s->id }}]" class="form-control form-control-sm d-inline-block mx-auto" style="width: auto; min-width: 140px;">
                                            @if(!$savedS)
                                                <option value="" selected disabled hidden>Pilih</option>
                                            @endif
                                            <option value="Sakit" {{ ($savedS == 'sakit') ? 'selected' : '' }}>Sakit</option>
                                            <option value="Izin" {{ ($savedS == 'izin') ? 'selected' : '' }}>Izin</option>
                                            <option value="Alpa" {{ ($savedS == 'alpa') ? 'selected' : '' }}>Alpa</option>
                                            @if($savedS == 'sakit' || $savedS == 'izin' || $savedS == 'alpa')
                                                <option value="cancel" class="text-danger font-weight-bold">Batal / Hapus</option>
                                            @endif
                                        </select>
                                    @endif
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

@push('scripts')
@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: '{{ session('success') }}',
        timer: 1500,
        showConfirmButton: false
    });
</script>
@endif
@endpush
