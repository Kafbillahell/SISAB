@extends('layouts.app')

@section('content')
<div class="container-fluid" id="jadwal-app">
    {{-- HEADER --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Jadwal Pelajaran</h1>
        <button onclick="resetView()" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-sync fa-sm text-white-50"></i> Reset Tampilan
        </button>
    </div>

    {{-- Alert Success --}}
    @if(session('success'))
        <div class="alert alert-success shadow alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    {{-- STEP 1: PILIH KELAS (ROMBEL) - Tetap sama seperti kode Anda --}}
    <div id="view-kelas">
        {{-- ... (Gunakan kode filter tingkat & jurusan yang sudah Anda miliki) ... --}}
        {{-- Pastikan pada tombol 'Atur Jadwal' memanggil showHari(id, nama) --}}
        <div class="row" id="container-rombel">
            @foreach($rombels as $r)
                {{-- Logika deteksi tingkat & jurusan Anda di sini --}}
                <div class="col-xl-3 col-md-4 mb-4 class-card-item" data-tingkat="10" data-jurusan="IPA">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $r->nama_rombel }}</div>
                            <button onclick="showHari('{{ $r->id }}', '{{ $r->nama_rombel }}')" class="btn btn-primary btn-sm btn-block mt-3 shadow-sm">
                                <i class="fas fa-calendar-alt mr-1"></i> Atur Jadwal
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- STEP 2: GRID JADWAL BERDASARKAN SESI --}}
    <div id="view-hari" style="display: none;">
        <div class="alert alert-info shadow-sm d-flex justify-content-between align-items-center border-0 bg-white" style="border-left: 4px solid #4e73df !important;">
            <span class="text-dark">Mengatur Jadwal: <strong id="selected-kelas-name" class="text-primary"></strong></span>
            <button onclick="showKelas()" class="btn btn-sm btn-outline-primary font-weight-bold"><i class="fas fa-chevron-left mr-1"></i> Kembali ke Pilih Kelas</button>
        </div>
        
        <div class="card shadow border-0 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0 text-center">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th style="width: 150px;">Waktu / Sesi</th>
                                @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                                    <th>{{ $hari }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sesis->sortBy('urutan') as $s)
                                <tr class="{{ $s->is_istirahat ? 'bg-light' : '' }}">
                                    <td class="align-middle bg-white">
                                        <div class="small font-weight-bold text-primary">{{ $s->nama_sesi }}</div>
                                        <span class="badge badge-light border">{{ substr($s->jam_mulai, 0, 5) }} - {{ substr($s->jam_selesai, 0, 5) }}</span>
                                    </td>
                                    
                                    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                                        <td class="p-1 align-middle">
                                            @if($s->is_istirahat)
                                                <div class="py-3 text-muted small font-italic">--- ISTIRAHAT ---</div>
                                            @else
                                                <div id="cell-{{ $hari }}-{{ str_replace(':', '', substr($s->jam_mulai, 0, 5)) }}" 
     class="slot-cell p-2 border rounded"
     onclick="openQuickAdd('{{ $hari }}', '{{ $s->jam_mulai }}', '{{ $s->jam_selesai }}', '{{ $s->id }}')"> <i class="fas fa-plus-circle text-gray-200 fa-lg"></i>
</div>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH (Modifikasi: Jam terisi otomatis) --}}
<div class="modal fade" id="addModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('jadwal.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Tambah Jadwal</h5>
                    <button class="close text-white" type="button" data-dismiss="modal"><span>×</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="rombel_id" id="modal-rombel-id">
                    <input type="hidden" name="sesi_id" id="modal-sesi-id"> <div class="row">
                        <div class="col-md-6">
                            <label>Hari</label>
                            <input type="text" name="hari" id="modal-hari" class="form-control bg-light" readonly>
                        </div>
                        <div class="col-md-6">
                            <label>Waktu</label>
                            <input type="text" id="modal-waktu-label" class="form-control bg-light" readonly>
                        </div>
                    </div>
                    
                    <div class="form-group mt-3">
                        <label>Mata Pelajaran</label>
                        <select name="mapel_id" class="form-control" required>
                            @foreach($mapels as $m)
                                <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Guru</label>
                        <select name="guru_id" class="form-control" required>
                            @foreach($gurus as $g)
                                <option value="{{ $g->id }}">{{ $g->nama_guru }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .slot-cell {
        min-height: 85px;
        background: #fff;
        transition: all 0.2s;
        border: 1px dashed #e3e6f0 !important;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        cursor: pointer;
    }
    .slot-cell:hover { background: #f8f9fc; border-color: #4e73df !important; }
    .slot-filled {
        background: #ffffff !important;
        border: 1px solid #4e73df !important;
        border-left: 4px solid #4e73df !important;
        align-items: flex-start;
        padding: 10px !important;
        cursor: default;
    }
    .btn-delete-slot { opacity: 0; transition: 0.2s; }
    .slot-filled:hover .btn-delete-slot { opacity: 1; }
</style>

<script>
    // Ambil data dari Laravel
    const allJadwals = @json($jadwals);
    
    function showHari(rombelId, rombelNama) {
        document.getElementById('view-kelas').style.display = 'none';
        document.getElementById('view-hari').style.display = 'block';
        document.getElementById('selected-kelas-name').innerText = rombelNama;
        document.getElementById('modal-rombel-id').value = rombelId;
        renderJadwal(rombelId);
    }

    function renderJadwal(rombelId) {
        // 1. Reset semua cell ke keadaan kosong (tombol Plus)
        document.querySelectorAll('.slot-cell').forEach(cell => {
            cell.classList.remove('slot-filled');
            cell.innerHTML = '<i class="fas fa-plus-circle text-gray-200 fa-lg"></i>';
            
            // Ambil kembali data atribut dari elemen untuk memulihkan fungsi klik
            // Kami tidak mengubah cell.onclick di sini agar tetap mengarah ke openQuickAdd asli
        });

        // 2. Filter jadwal berdasarkan rombel yang dipilih
        const filtered = allJadwals.filter(j => j.rombel_id == rombelId);
        
        filtered.forEach(j => {
            // Format jam harus HHMM (contoh 07:10 menjadi 0710)
            const jamKey = j.jam_mulai.substring(0, 5).replace(':', '');
            const cellId = `cell-${j.hari}-${jamKey}`;
            const container = document.getElementById(cellId);

            if (container) {
                container.classList.add('slot-filled');
                // Nonaktifkan klik modal tambah jika sudah ada isinya
                container.setAttribute('onclick', ''); 
                
                container.innerHTML = `
                    <div class="position-relative w-100 text-left">
                        <div class="font-weight-bold text-primary mb-1" style="font-size: 0.8rem; line-height: 1.2;">
                            ${j.mapel ? j.mapel.nama_mapel : 'Mapel Terhapus'}
                        </div>
                        <div class="text-dark small mb-1" style="font-size: 0.7rem;">
                            <i class="fas fa-user-tie mr-1 text-gray-400"></i>${j.guru ? j.guru.nama_guru : 'Guru Terhapus'}
                        </div>
                        <form action="/jadwal/${j.id}" method="POST" class="position-absolute btn-delete-slot" style="top:-5px; right:-5px;">
                            @csrf 
                            @method('DELETE')
                            <button type="submit" class="btn btn-link text-danger p-0" onclick="return confirm('Hapus jadwal ini?')">
                                <i class="fas fa-times-circle"></i>
                            </button>
                        </form>
                    </div>`;
            }
        });
    }

    function openQuickAdd(hari, jamMulai, jamSelesai, sesiId) {
        const rombelId = document.getElementById('modal-rombel-id').value;
        
        if(!rombelId) {
            alert("Silakan pilih kelas terlebih dahulu!");
            return;
        }

        // Isi field di dalam modal
        document.getElementById('modal-hari').value = hari;
        document.getElementById('modal-sesi-id').value = sesiId;
        document.getElementById('modal-waktu-label').value = jamMulai.substring(0, 5) + ' - ' + jamSelesai.substring(0, 5);
        
        // Tampilkan modal
        $('#addModal').modal('show');
    }

    function showKelas() {
        document.getElementById('view-kelas').style.display = 'block';
        document.getElementById('view-hari').style.display = 'none';
    }

    function resetView() { window.location.reload(); }
</script>
@endsection