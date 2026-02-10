@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Pengaturan Waktu (Sesi Universal)</h1>
        <button class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#modalSesi">
            <i class="fas fa-plus fa-sm mr-2"></i> Tambah Sesi Jam
        </button>
    </div>

    <div class="alert alert-info shadow-sm border-0 bg-white" style="border-left: 4px solid #36b9cc !important;">
        <i class="fas fa-info-circle mr-2 text-info"></i>
        <strong>Info:</strong> Daftar jam di bawah ini akan berlaku secara otomatis untuk <strong>semua hari</strong> (Senin - Sabtu) di halaman Jadwal Pelajaran.
    </div>

    <div class="card shadow border-0">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary">Master Jam Pelajaran & Istirahat</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr class="text-center">
                            <th width="80">Urutan</th>
                            <th>Nama Sesi / Label</th>
                            <th>Durasi Waktu</th>
                            <th>Tipe</th>
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sesis as $s)
                        <tr class="text-center">
                            <td class="align-middle">
                                <span class="badge badge-secondary shadow-sm px-3">{{ $s->urutan }}</span>
                            </td>
                            <td class="align-middle font-weight-bold text-left px-4">{{ $s->nama_sesi }}</td>
                            <td class="align-middle">
                                <span class="text-success font-weight-bold">{{ substr($s->jam_mulai, 0, 5) }}</span>
                                <i class="fas fa-arrow-right mx-2 text-muted small"></i>
                                <span class="text-danger font-weight-bold">{{ substr($s->jam_selesai, 0, 5) }}</span>
                            </td>
                            <td class="align-middle">
                                {!! $s->is_istirahat 
                                    ? '<span class="badge badge-warning text-dark"><i class="fas fa-coffee mr-1"></i> Istirahat</span>' 
                                    : '<span class="badge badge-info"><i class="fas fa-book-reader mr-1"></i> KBM</span>' 
                                !!}
                            </td>
                            <td class="align-middle">
                                <form action="{{ route('sesi.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Hapus sesi ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light text-danger border"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <p class="text-muted">Belum ada data sesi. Silakan tambah sesi universal pertama Anda.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH (Hapus Input Hari) --}}
<div class="modal fade" id="modalSesi" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0">
            <form action="{{ route('sesi.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">Tambah Sesi Jam</h5>
                    <button class="close text-white" data-dismiss="modal"><span>×</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="small font-weight-bold">URUTAN (JAM KE-)</label>
                        <input type="number" name="urutan" class="form-control" required placeholder="Contoh: 1">
                        <small class="text-muted">Gunakan urutan 1, 2, 3 dst untuk jam pelajaran.</small>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="small font-weight-bold">NAMA SESI</label>
                        <input type="text" name="nama_sesi" class="form-control" required placeholder="Contoh: Jam Pelajaran 1">
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <label class="small font-weight-bold">JAM MULAI</label>
                            <input type="time" name="jam_mulai" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="small font-weight-bold">JAM SELESAI</label>
                            <input type="time" name="jam_selesai" class="form-control" required>
                        </div>
                    </div>

                    <div class="custom-control custom-checkbox mt-3 p-2 bg-light rounded">
                        <input type="checkbox" name="is_istirahat" value="1" class="custom-control-input" id="checkIstirahat">
                        <label class="custom-control-label" for="checkIstirahat">Tandai sebagai Istirahat</label>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="submit" class="btn btn-primary btn-block shadow">SIMPAN SESI</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection