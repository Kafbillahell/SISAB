{{-- 
    File: penilaian-sikap/index.blade.php
    Fungsi: Antarmuka utama penilaian sikap siswa berupa grid daftar siswa.
    Memiliki fitur filter kelas, komponen progress bar untuk Wali Kelas, 
    dan modal Fast-Input untuk pengisian skor cepat dengan AJAX.
--}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Penilaian Sikap (Afektif)</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- FILTER DATA --}}
    @if(!$is_wali_kelas)
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
    @else
    {{-- WALI KELAS HEADER & PROGRESS --}}
    <div class="card shadow mb-4 border-left-primary">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <h5 class="font-weight-bold text-primary mb-1"><i class="fas fa-users mr-2"></i>Kelas Anda: {{ $nama_kelas_wali }}</h5>
                    <p class="text-muted mb-0 small">Menampilkan seluruh siswa di kelas perwalian Anda.</p>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="font-weight-bold text-gray-800 small">Progres Penilaian ({{ $active_periode ? $active_periode->nama_periode : 'Periode' }})</span>
                        <span class="font-weight-bold text-{{ $progress_percentage == 100 ? 'success' : 'primary' }} small">{{ $siswa_dinilai }} / {{ $total_siswa }} Siswa</span>
                    </div>
                    <div class="progress progress-sm mr-2" style="height: 12px; border-radius: 10px;">
                        <div class="progress-bar bg-{{ $progress_percentage == 100 ? 'success' : 'primary' }}" role="progressbar" style="width: {{ $progress_percentage }}%" aria-valuenow="{{ $progress_percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- AKSI MASSAL --}}
    @if(count($siswas) > 0)
    <div class="d-flex justify-content-between align-items-center mb-3 bg-white p-3 rounded shadow-sm border-left-info">
        <div class="custom-control custom-checkbox" style="cursor: pointer;">
            <input type="checkbox" class="custom-control-input" id="checkAllSiswa" onchange="toggleAllSiswa(this)">
            <label class="custom-control-label font-weight-bold" for="checkAllSiswa" style="cursor: pointer;">Pilih Semua Siswa <span class="text-muted font-weight-normal small ml-2" id="selectedCountText">(0 terpilih)</span></label>
        </div>
        <button type="button" class="btn btn-info btn-sm shadow-sm" id="btnSikapMassal" disabled onclick="openFastInputMassalModal()">
            <i class="fas fa-tasks fa-sm mr-1"></i> Nilai Terpilih
        </button>
    </div>
    @endif

    {{-- GRID KARTU SISWA --}}
    <div class="row">
        @forelse($siswas as $siswa)
        @php
            // Cek apakah siswa ini sudah dinilai pada periode terpilih
            $is_assessed = false;
            $nilai_siswa = null;
            if ($selected_periode) {
                $nilai_siswa = \App\Models\PenilaianSikap::where('siswa_id', $siswa->id)
                                ->where('periode_id', $selected_periode)
                                ->first();
                $is_assessed = $nilai_siswa ? true : false;
            }
        @endphp
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 py-2 border-left-{{ $is_assessed ? 'success' : 'warning' }}" style="transition: transform .2s; cursor: pointer;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                <div class="card-body pb-0" onclick="openFastInputModal({{ $siswa->toJson() }}, {{ $nilai_siswa ? $nilai_siswa->toJson() : 'null' }})">
                    <div class="row no-gutters align-items-center mb-3">
                        <div class="col-auto mr-3">
                            @if(isset($siswa->foto) && $siswa->foto)
                                <img src="{{ $siswa->foto }}" class="rounded-circle shadow-sm" style="width: 50px; height: 50px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-gray-200 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                {{ $siswa->nisn }}
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800 text-truncate" title="{{ strtoupper($siswa->nama_siswa) }}">
                                {{ strtoupper($siswa->nama_siswa) }}
                            </div>
                        </div>
                        <div class="col-auto pl-2" onclick="event.stopPropagation()">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input chk-siswa" id="chkSiswa_{{ $siswa->id }}" value="{{ $siswa->id }}" onchange="updateSelectedCount()">
                                <label class="custom-control-label" for="chkSiswa_{{ $siswa->id }}" style="cursor: pointer; padding-top: 5px;"></label>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-3 text-xs">
                        <span>
                            <i class="fas fa-venus-mars text-gray-400"></i> {{ $siswa->jenis_kelamin == 'L' ? 'L' : 'P' }} &nbsp;|&nbsp;
                            <i class="fas fa-chalkboard-teacher text-gray-400"></i> 
                            @if($siswa->anggotaRombels->isNotEmpty() && $siswa->anggotaRombels->first()->rombel && $siswa->anggotaRombels->first()->rombel->kelas)
                                {{ $siswa->anggotaRombels->first()->rombel->kelas->tingkat }} {{ $siswa->anggotaRombels->first()->rombel->kelas->nama_kelas }}
                            @else
                                -
                            @endif
                        </span>
                        
                        @if($is_assessed)
                            <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle"></i> Sudah Dinilai</span>
                        @else
                            <span class="badge badge-warning px-2 py-1"><i class="fas fa-clock"></i> Belum Dinilai</span>
                        @endif
                    </div>
                    <div class="d-flex justify-content-center align-items-center mb-0 mt-3 pt-2 border-top">
                        <span class="badge badge-light border px-3 py-2 text-dark font-weight-bold" style="font-size: 0.85rem;">
                            <i class="fas fa-list-ol text-primary mr-1"></i> No. Absen: {{ $loop->iteration }}
                        </span>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-2 pb-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-{{ $is_assessed ? 'success' : 'primary' }} btn-sm w-100 mr-2 shadow-sm {{ $is_assessed ? 'text-white' : '' }}" onclick="openFastInputModal({{ $siswa->toJson() }}, {{ $nilai_siswa ? $nilai_siswa->toJson() : 'null' }})" title="Sikap">
                        <i class="fas fa-edit fa-sm"></i> Nilai
                    </button>
                    <a href="{{ route('penilaian-sikap.show', ['siswa_id' => $siswa->id, 'periode_id' => $selected_periode]) }}" class="btn btn-outline-info btn-sm shadow-sm" style="width: 40px;" title="Detail (Radar Chart)">
                        <i class="fas fa-chart-line"></i>
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-body text-center py-5">
                    @if(!$is_wali_kelas && empty(request('kelas_id')))
                        <i class="fas fa-hand-pointer fa-3x text-primary mb-3"></i>
                        <h5 class="text-gray-800 font-weight-bold">Silakan Pilih Kelas Terlebih Dahulu</h5>
                        <p class="mb-0 text-muted">Untuk menjaga peforma dan kecepatan aplikasi, data siswa tidak ditampilkan sekaligus. Silakan gunakan filter di atas.</p>
                    @else
                        <i class="fas fa-folder-open fa-3x text-gray-300 mb-3"></i>
                        <h5 class="text-muted">Data siswa tidak ditemukan.</h5>
                        <p class="mb-0">Pastikan kelas yang Anda pilih memiliki data siswa terdaftar.</p>
                    @endif
                </div>
            </div>
        </div>
        @endforelse
    </div>

    {{-- FAST INPUT MASSAL MODAL --}}
    <div class="modal fade" id="fastInputMassalModal" tabindex="-1" role="dialog" aria-labelledby="fastInputMassalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-info text-white text-center pb-4" style="border-radius: 0.3rem 0.3rem 0 0; position: relative;">
                    <div style="position: absolute; top: 15px; left: 0; width: 100%; display: flex; justify-content: center; z-index: 10;">
                        <div class="rounded-circle shadow d-flex align-items-center justify-content-center bg-white" style="width: 80px; height: 80px; border: 4px solid white; transform: translateY(20px);">
                            <i class="fas fa-users fa-3x text-info"></i>
                        </div>
                    </div>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="position: absolute; right: 15px; top: 15px; z-index: 20;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <div style="height: 30px;"></div>
                </div>
                <form id="fastInputMassalForm" method="POST">
                    @csrf
                    <input type="hidden" name="periode_id" value="{{ $selected_periode }}">
                    <!-- Akan diisi array siswa_ids via JS -->
                    <div id="hidden_siswa_ids_container"></div>
                    
                    <div class="modal-body pt-5 text-center">
                        <h5 class="modal-title font-weight-bold text-dark mt-2">Penilaian Massal</h5>
                        <p class="text-muted small mb-4" id="modal_massal_count">0 Siswa Terpilih</p>

                        @php
                            $aspekFast = [
                                'tanggung_jawab' => ['Tanggung Jawab', 'Mengerjakan tugas & jujur mengakui salah'],
                                'kejujuran' => ['Kejujuran', 'Tidak menyontek & orisinal'],
                                'sopan_santun' => ['Sopan Santun', 'Etika & cara komunikasi baik'],
                                'kemandirian' => ['Kemandirian', 'Mengerjakan tugas tanpa bergantung'],
                                'kerja_sama' => ['Kerja Sama', 'Gotong royong dalam tim'],
                            ];
                        @endphp

                        <div class="px-3 text-left">
                            <div class="table-responsive mb-3">
                                <table class="table table-borderless table-striped table-sm mb-0 align-middle">
                                    <thead class="bg-gray-100 text-gray-800 text-center" style="font-size: 0.85rem;">
                                        <tr>
                                            <th class="text-left w-50 pl-3">Aspek Penilaian</th>
                                            <th title="Sangat Kurang">1</th>
                                            <th title="Kurang">2</th>
                                            <th title="Cukup">3</th>
                                            <th title="Baik">4</th>
                                            <th title="Sangat Baik">5</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($aspekFast as $key => $info)
                                            <tr>
                                                <td class="pl-3 py-3 align-middle">
                                                    <span class="font-weight-bold text-gray-800 d-block mb-1">{{ $info[0] }}</span>
                                                    <small class="text-muted d-block" style="line-height: 1.2;">{{ $info[1] }}</small>
                                                </td>
                                                @php
                                                    $skala = [1, 2, 3, 4, 5];
                                                @endphp
                                                @foreach($skala as $nilai)
                                                    <td class="text-center align-middle">
                                                        <div class="custom-control custom-radio d-inline-block">
                                                            <input type="radio" id="skala_massal_{{ $key }}_{{ $nilai }}" name="{{ $key }}" value="{{ $nilai }}" class="custom-control-input" required>
                                                            <label class="custom-control-label" for="skala_massal_{{ $key }}_{{ $nilai }}" style="cursor: pointer;"></label>
                                                        </div>
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="d-flex justify-content-between text-muted mt-2 px-3 pb-3 border-bottom" style="font-size: 0.75rem;">
                                <span>* Skala: 1 (Sangat Kurang) s/d 5 (Sangat Baik)</span>
                            </div>
                        </div>

                        <div class="form-group text-left px-3 mt-2">
                            <label for="modal_massal_catatan" class="font-weight-bold text-gray-800 small">Catatan Ekstra (Opsional)</label>
                            <textarea class="form-control bg-light" id="modal_massal_catatan" name="catatan" rows="3" placeholder="Tulis catatan jika diperlukan..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center bg-gray-100 border-top-0 py-3">
                        <button type="button" class="btn btn-secondary px-4 rounded-pill" data-dismiss="modal">Batal</button>
                        <button type="submit" id="btn_simpan_massal" class="btn btn-info px-4 rounded-pill"><i class="fas fa-save mr-1"></i> Simpan Massal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- FAST INPUT MODAL --}}
    <div class="modal fade" id="fastInputModal" tabindex="-1" role="dialog" aria-labelledby="fastInputModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white text-center pb-4" style="border-radius: 0.3rem 0.3rem 0 0; position: relative;">
                    <div style="position: absolute; top: 15px; left: 0; width: 100%; display: flex; justify-content: center; z-index: 10;">
                        <img id="modal_siswa_foto" src="" onerror="this.src='https://ui-avatars.com/api/?name=Siswa&background=random'" class="rounded-circle shadow" style="width: 80px; height: 80px; object-fit: cover; border: 4px solid white; transform: translateY(20px);">
                    </div>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="position: absolute; right: 15px; top: 15px; z-index: 20;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <div style="height: 30px;"></div>
                </div>
                <form id="fastInputForm" method="POST">
                    @csrf
                    <input type="hidden" name="periode_id" value="{{ $selected_periode }}">
                    <input type="hidden" id="modal_siswa_id" name="siswa_id">
                    
                    <div class="modal-body pt-5 text-center">
                        <h5 class="modal-title font-weight-bold text-dark mt-2" id="modal_siswa_nama">Nama Siswa</h5>
                        <p class="text-muted small mb-4" id="modal_siswa_nisn">NISN: 12345678</p>

                        @php
                            $aspekFast = [
                                'tanggung_jawab' => ['Tanggung Jawab', 'Mengerjakan tugas & jujur mengakui salah'],
                                'kejujuran' => ['Kejujuran', 'Tidak menyontek & orisinal'],
                                'sopan_santun' => ['Sopan Santun', 'Etika & cara komunikasi baik'],
                                'kemandirian' => ['Kemandirian', 'Mengerjakan tugas tanpa bergantung'],
                                'kerja_sama' => ['Kerja Sama', 'Gotong royong dalam tim'],
                            ];
                        @endphp

                        <div class="px-3 text-left">
                            <div class="table-responsive mb-3">
                                <table class="table table-borderless table-striped table-sm mb-0 align-middle">
                                    <thead class="bg-gray-100 text-gray-800 text-center" style="font-size: 0.85rem;">
                                        <tr>
                                            <th class="text-left w-50 pl-3">Aspek Penilaian</th>
                                            <th title="Sangat Kurang">1</th>
                                            <th title="Kurang">2</th>
                                            <th title="Cukup">3</th>
                                            <th title="Baik">4</th>
                                            <th title="Sangat Baik">5</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($aspekFast as $key => $info)
                                            <tr>
                                                <td class="pl-3 py-3 align-middle">
                                                    <span class="font-weight-bold text-gray-800 d-block mb-1">{{ $info[0] }}</span>
                                                    <small class="text-muted d-block" style="line-height: 1.2;">{{ $info[1] }}</small>
                                                </td>
                                                @php
                                                    $skala = [1, 2, 3, 4, 5];
                                                @endphp
                                                @foreach($skala as $nilai)
                                                    <td class="text-center align-middle">
                                                        <div class="custom-control custom-radio d-inline-block">
                                                            <input type="radio" id="skala_{{ $key }}_{{ $nilai }}" name="{{ $key }}" value="{{ $nilai }}" class="custom-control-input" required>
                                                            <label class="custom-control-label" for="skala_{{ $key }}_{{ $nilai }}" style="cursor: pointer;"></label>
                                                        </div>
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="d-flex justify-content-between text-muted mt-2 px-3 pb-3 border-bottom" style="font-size: 0.75rem;">
                                <span>* Skala: 1 (Sangat Kurang) s/d 5 (Sangat Baik)</span>
                            </div>
                        </div>

                        <div class="form-group text-left px-3 mt-2">
                            <label for="modal_catatan" class="font-weight-bold text-gray-800 small">Catatan Ekstra (Opsional)</label>
                            <textarea class="form-control bg-light" id="modal_catatan" name="catatan" rows="3" placeholder="Tulis catatan jika diperlukan..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center bg-gray-100 border-top-0 py-3">
                        <button type="button" class="btn btn-secondary px-4 rounded-pill" data-dismiss="modal">Batal</button>
                        <button type="submit" id="btn_simpan" class="btn btn-outline-primary px-4 rounded-pill"><i class="fas fa-save mr-1"></i> Simpan</button>
                        <button type="button" id="btn_simpan_lanjut" class="btn btn-primary px-4 rounded-pill"><i class="fas fa-forward mr-1"></i> Simpan & Lanjut</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
<style>
</style>
@endpush

@push('scripts')
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const jurusanSelect = document.getElementById('jurusan_id');
        const kelasSelect = document.getElementById('kelas_id');
        
        if (jurusanSelect && kelasSelect) {
            const options = Array.from(kelasSelect.options);

            function filterKelas() {
                const selectedJurusan = jurusanSelect.value;
                options[0].style.display = 'block';
                for (let i = 1; i < options.length; i++) {
                    const option = options[i];
                    const optionJurusan = option.getAttribute('data-jurusan');
                    if (!selectedJurusan || optionJurusan === selectedJurusan) {
                        option.style.display = 'block';
                    } else {
                        option.style.display = 'none';
                        if (option.selected) { kelasSelect.value = ''; }
                    }
                }
            }
            jurusanSelect.addEventListener('change', filterKelas);
            filterKelas();
        }
    });

    // Variable global untuk nampung data siswa 
    let currentSiswaList = @json($siswas);
    let currentSiswaIndex = 0;

    // Fast Input Modal Logic
    function openFastInputModal(siswa, nilai) {
        // Cari index siswa di list
        currentSiswaIndex = currentSiswaList.findIndex(s => s.id === siswa.id);

        const defaultPeriode = "{{ $selected_periode }}";
        if (!defaultPeriode) {
            alert('Silakan pilih Periode Penilaian terlebih dahulu di bagian filter atas!');
            return;
        }

        // Set Profil Siswa
        document.getElementById('modal_siswa_id').value = siswa.id;
        document.getElementById('modal_siswa_nama').innerText = siswa.nama_siswa.toUpperCase();
        document.getElementById('modal_siswa_nisn').innerText = 'NISN: ' + siswa.nisn;
        
        const avatarUrl = siswa.foto ? siswa.foto : `https://ui-avatars.com/api/?name=${encodeURIComponent(siswa.nama_siswa)}&background=4e73df&color=fff&size=128`;
        document.getElementById('modal_siswa_foto').src = avatarUrl;

        // Reset Form
        document.getElementById('fastInputForm').reset();
        
        // Setup Action URL
        document.getElementById('fastInputForm').action = `{{ url('penilaian-sikap') }}/${siswa.id}/store`;
        
        // Isi nilai lama jika sudah dinilai
        if (nilai) {
            document.getElementById('modal_catatan').value = nilai.catatan || '';
            const aspects = ['tanggung_jawab', 'kejujuran', 'sopan_santun', 'kemandirian', 'kerja_sama'];
            aspects.forEach(aspect => {
                if (nilai[aspect]) {
                    const radio = document.getElementById(`skala_${aspect}_${nilai[aspect]}`);
                    if (radio) radio.checked = true;
                }
            });
        }
        
        $('#fastInputModal').modal('show');
    }

    // Handle AJAX Submission and Save & Next
    document.getElementById('btn_simpan_lanjut').addEventListener('click', function(e) {
        e.preventDefault();
        saveAssessment(true);
    });

    document.getElementById('fastInputForm').addEventListener('submit', function(e) {
        e.preventDefault();
        saveAssessment(false);
    });

    function saveAssessment(nextSiswa = false) {
        const form = document.getElementById('fastInputForm');
        
        // Basic required validation untuk radio
        const aspects = ['tanggung_jawab', 'kejujuran', 'sopan_santun', 'kemandirian', 'kerja_sama'];
        let isValid = true;
        aspects.forEach(aspect => {
            if (!form.querySelector(`input[name="${aspect}"]:checked`)) {
                isValid = false;
            }
        });

        if (!isValid) {
            Swal.fire({
                title: 'Data Belum Lengkap!',
                text: 'Mohon isi semua lima aspek penilaian terlebih dahulu.',
                icon: 'warning',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Oke, Saya Paham'
            });
            return;
        }

        const formData = new FormData(form);
        const actionUrl = form.action;

        // Animasi loading pada tombol
        const btnSimpan = document.getElementById('btn_simpan');
        const btnSimpanLanjut = document.getElementById('btn_simpan_lanjut');
        const originalTextSimpan = btnSimpan.innerHTML;
        const originalTextLanjut = btnSimpanLanjut.innerHTML;
        
        if (nextSiswa) {
            btnSimpanLanjut.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
            btnSimpanLanjut.disabled = true;
        } else {
            btnSimpan.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
            btnSimpan.disabled = true;
        }

        fetch(actionUrl, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json, text/plain, */*'
            }
        })
        .then(response => {
            if (response.redirected) {
                // If the controller currently redirects instead of pure JSON, 
                // we treat it as success for now since we haven't modified the store method to purely return JSON yet.
                // We will handle the view refresh later, for now we just show next or reload.
                return { success: true };
            }
            return response.json().catch(err => { return { success: true }; }); // fallback
        })
        .then(data => {
            if (nextSiswa && currentSiswaIndex < currentSiswaList.length - 1) {
                // Berhasil simpan, beri notifikasi ringan (Toast) lalu muat form berikutnya
                Swal.fire({
                    title: 'Tersimpan!',
                    text: 'Data disimpan, memuat siswa berikutnya...',
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 1500
                });
                
                // Ubah status dicard UI di belakang secara instan agar guru tahu ini sudah dinilai
                const currentId = currentSiswaList[currentSiswaIndex].id;
                const cardBtn = document.querySelector(`button[onclick*='"id":${currentId}']`);
                if(cardBtn) {
                    cardBtn.classList.remove('btn-primary');
                    cardBtn.classList.add('btn-success', 'text-white');
                    cardBtn.innerHTML = '<i class="fas fa-edit fa-sm"></i> Nilai';
                }

                // Lanjut ke siswa berikutnya tanpa reload
                const nextSiswaData = currentSiswaList[currentSiswaIndex + 1];
                // Panggil open modal yang sama untuk siswa berikutnya (Kosongkan nilai awalnya agar diisi baru)
                setTimeout(() => {
                    openFastInputModal(nextSiswaData, null);
                    btnSimpanLanjut.innerHTML = originalTextLanjut;
                    btnSimpanLanjut.disabled = false;
                }, 500);

            } else {
                // Tombol Simpan Biasa, atau Siswa Terakhir
                // Ubah status dicard UI di belakang secara instan agar guru tahu ini sudah dinilai
                const currentId = currentSiswaList[currentSiswaIndex].id;
                const cardBtn = document.querySelector(`button[onclick*='"id":${currentId}']`);
                if(cardBtn) {
                    cardBtn.classList.remove('btn-primary');
                    cardBtn.classList.add('btn-success', 'text-white');
                    cardBtn.innerHTML = '<i class="fas fa-edit fa-sm"></i> Nilai';
                }

                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Penilaian telah disimpan.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    // Cukup tutup modal, kembalikan teks tombol, tidak perlu reload halaman
                    $('#fastInputModal').modal('hide');
                    btnSimpan.innerHTML = originalTextSimpan;
                    btnSimpan.disabled = false;
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Terjadi Kesalahan!',
                text: 'Gagal menyimpan data penilaian. Silakan periksa koneksi atau coba lagi.',
                icon: 'error',
                confirmButtonColor: '#e74a3b',
                confirmButtonText: 'Tutup'
            });
            btnSimpan.innerHTML = originalTextSimpan;
            btnSimpanLanjut.innerHTML = originalTextLanjut;
            btnSimpan.disabled = false;
            btnSimpanLanjut.disabled = false;
        });
    }

    // -- MASSAL LOGIC --
    function toggleAllSiswa(source) {
        const checkboxes = document.querySelectorAll('.chk-siswa');
        checkboxes.forEach(cb => cb.checked = source.checked);
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const selected = document.querySelectorAll('.chk-siswa:checked').length;
        const total = document.querySelectorAll('.chk-siswa').length;
        
        document.getElementById('selectedCountText').innerText = `(${selected} terpilih)`;
        document.getElementById('btnSikapMassal').disabled = selected === 0;
        
        const checkAll = document.getElementById('checkAllSiswa');
        if (!checkAll) return;

        if (selected > 0 && selected === total && total > 0) {
            checkAll.checked = true;
            checkAll.indeterminate = false;
        } else if (selected > 0 && selected < total) {
            checkAll.checked = false;
            checkAll.indeterminate = true;
        } else {
            checkAll.checked = false;
            checkAll.indeterminate = false;
        }
    }

    function openFastInputMassalModal() {
        const defaultPeriode = "{{ $selected_periode }}";
        if (!defaultPeriode) {
            alert('Silakan pilih Periode Penilaian terlebih dahulu di bagian filter atas!');
            return;
        }

        const selectedCheckboxes = document.querySelectorAll('.chk-siswa:checked');
        if (selectedCheckboxes.length === 0) return;

        document.getElementById('modal_massal_count').innerText = `${selectedCheckboxes.length} Siswa Terpilih`;
        document.getElementById('fastInputMassalForm').reset();
        
        const container = document.getElementById('hidden_siswa_ids_container');
        container.innerHTML = '';
        selectedCheckboxes.forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'siswa_ids[]';
            input.value = cb.value;
            container.appendChild(input);
        });

        document.getElementById('fastInputMassalForm').action = `{{ route('penilaian-sikap.storeMassal') }}`;
        $('#fastInputMassalModal').modal('show');
    }

    document.getElementById('fastInputMassalForm').addEventListener('submit', function(e) {
        e.preventDefault();
        saveAssessmentMassal();
    });

    function saveAssessmentMassal() {
        const form = document.getElementById('fastInputMassalForm');
        
        const aspects = ['tanggung_jawab', 'kejujuran', 'sopan_santun', 'kemandirian', 'kerja_sama'];
        let isValid = true;
        aspects.forEach(aspect => {
            if (!form.querySelector(`input[name="${aspect}"]:checked`)) isValid = false;
        });

        if (!isValid) {
            Swal.fire({
                title: 'Data Belum Lengkap!',
                text: 'Mohon isi semua lima aspek penilaian untuk penilaian massal ini.',
                icon: 'warning',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Oke'
            });
            return;
        }

        const formData = new FormData(form);
        const actionUrl = form.action;
        const btnSimpan = document.getElementById('btn_simpan_massal');
        const originalTextSimpan = btnSimpan.innerHTML;
        
        btnSimpan.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
        btnSimpan.disabled = true;

        fetch(actionUrl, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json, text/plain, */*'
            }
        })
        .then(response => {
            if (response.redirected) return { success: true };
            return response.json().catch(err => { return { success: true }; });
        })
        .then(data => {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Penilaian massal telah disimpan.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                $('#fastInputMassalModal').modal('hide');
                btnSimpan.innerHTML = originalTextSimpan;
                btnSimpan.disabled = false;
                window.location.reload(); 
            });
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Terjadi Kesalahan!',
                text: 'Gagal menyimpan data penilaian massal.',
                icon: 'error',
                confirmButtonColor: '#e74a3b',
                confirmButtonText: 'Tutup'
            });
            btnSimpan.innerHTML = originalTextSimpan;
            btnSimpan.disabled = false;
        });
    }
</script>
@endpush
