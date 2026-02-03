@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-11">
            <div class="card border-0 shadow-lg overflow-hidden" style="border-radius: 20px;">
                <div class="row no-gutters">
                    {{-- PANEL KIRI: INFO & FILTER --}}
                    <div class="col-lg-4 bg-gradient-primary p-5 d-flex flex-column justify-content-center text-white">
                        <div class="mb-4">
                            <i class="fas fa-id-badge fa-3x mb-3 text-white-50"></i>
                            <h2 class="font-weight-bold">Smart Attendance</h2>
                            <p class="text-white-50">Sistem presensi wajah otomatis sesuai jadwal pelajaran.</p>
                        </div>

                        @if(auth()->user()->role == 'admin')
                        <div class="mb-4 p-3 rounded-lg" style="background: rgba(255,255,255,0.1); border: 1px dashed rgba(255,255,255,0.3);">
                            <label class="small font-weight-bold text-uppercase mb-2 d-block text-white-50">Mode Admin: Pilih Guru</label>
                            <form action="{{ route('presensi.scanner') }}" method="GET" id="adminFilterForm">
                                <select name="guru_id" class="form-control form-control-sm border-0 shadow-sm" onchange="this.form.submit()">
                                    <option value="">-- Pilih Identitas Guru --</option>
                                    @foreach($allGurus as $g)
                                        <option value="{{ $g->id }}" {{ $targetGuruId == $g->id ? 'selected' : '' }}>
                                            {{ $g->nama_guru }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                        @endif

                        @if($jadwalAktif)
                        <div class="bg-white-core p-4 rounded-lg shadow-sm" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); backdrop-filter: blur(10px);">
                            <div class="mb-2">
                                <small class="text-white-50 text-uppercase letter-spacing-1">Guru Mengajar</small>
                                <h6 class="font-weight-bold">{{ $jadwalAktif->guru->nama_guru }}</h6>
                            </div>
                            <hr style="border-top: 1px solid rgba(255,255,255,0.1)">
                            <small class="text-white-50 text-uppercase letter-spacing-1">Mata Pelajaran</small>
                            <h4 class="font-weight-bold mb-3">{{ $jadwalAktif->mapel->nama_mapel }}</h4>
                            
                            <div class="d-flex flex-wrap align-items-center" style="gap: 5px;">
                                <div class="badge badge-pill badge-light px-3 py-2">
                                    <i class="fas fa-users mr-1"></i> {{ $jadwalAktif->rombel->nama_rombel }}
                                </div>
                                <div class="badge badge-pill badge-success px-3 py-2">
                                    <i class="fas fa-clock mr-1"></i> {{ substr($jadwalAktif->jam_mulai, 0, 5) }} - {{ substr($jadwalAktif->jam_selesai, 0, 5) }}
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="alert alert-warning border-0 shadow-sm text-dark">
                            <i class="fas fa-calendar-times mr-2"></i> 
                            @if(auth()->user()->role == 'admin' && !$targetGuruId)
                                Silakan pilih guru terlebih dahulu.
                            @else
                                Tidak ada jadwal aktif untuk guru ini.
                            @endif
                        </div>
                        @endif

                        <div class="mt-auto pt-4">
                            <a href="{{ route('presensi.index') }}" class="btn btn-link text-white text-decoration-none p-0">
                                <i class="fas fa-chevron-left mr-2"></i> Kembali ke Dashboard
                            </a>
                        </div>
                    </div>

                    {{-- PANEL KANAN: SCANNER AREA --}}
                    <div class="col-lg-8 bg-light p-4 p-md-5">
                        @php
                            // Syarat Scanner Muncul: 
                            // 1. Ada jadwal aktif
                            // 2. Jika admin, HARUS sudah pilih guru
                            $bolehScan = ($jadwalAktif && (auth()->user()->role !== 'admin' || $targetGuruId));
                        @endphp

                        @if($bolehScan)
                            <div class="scanner-container position-relative mx-auto shadow-lg bg-black rounded-xl overflow-hidden" style="max-width: 600px; border-radius: 30px; border: 8px solid #fff; transition: all 0.3s ease;" id="scannerWrapper">
                                <video id="video" autoplay muted playsinline style="width: 100%; height: auto; transform: scaleX(-1); display: block;"></video>
                                
                                <div class="scanner-overlay">
                                    <div class="face-frame"></div>
                                    <div class="scan-line"></div>
                                </div>

                                <div class="position-absolute fixed-bottom p-4 text-center">
                                    <div id="result" class="badge badge-primary shadow-lg px-4 py-3" style="font-size: 1rem; border-radius: 50px; background: rgba(78, 115, 223, 0.9); backdrop-filter: blur(5px);">
                                        <i class="fas fa-sync fa-spin mr-2"></i> Menyiapkan Sensor Wajah...
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <img src="https://illustrations.popsy.co/white/calendar.svg" style="width: 200px;" class="mb-4">
                                @if(auth()->user()->role == 'admin' && !$targetGuruId)
                                    <h4 class="text-primary font-weight-bold">Mode Admin: Standby</h4>
                                    <p class="text-secondary">Silakan pilih <strong>Identitas Guru</strong> pada panel kiri<br>untuk membuka akses scanner.</p>
                                    <i class="fas fa-arrow-left fa-2x text-primary mt-3 animate-bounce-left"></i>
                                @else
                                    <h4 class="text-muted">Scanner Tidak Tersedia</h4>
                                    <p class="text-secondary">Tidak ditemukan jadwal aktif untuk saat ini.</p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-black { background-color: #000; }
    .letter-spacing-1 { letter-spacing: 1px; }
    .scanner-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; pointer-events: none; }
    .face-frame { width: 250px; height: 250px; border: 2px solid rgba(255, 255, 255, 0.3); border-radius: 40px; position: relative; box-shadow: 0 0 0 1000px rgba(0, 0, 0, 0.5); }
    .face-frame::before { content: ''; position: absolute; width: 40px; height: 40px; border: 4px solid #4e73df; top: -4px; left: -4px; border-radius: 15px 0 0 0; border-width: 4px 0 0 4px; }
    .face-frame::after { content: ''; position: absolute; width: 40px; height: 40px; border: 4px solid #4e73df; bottom: -4px; right: -4px; border-radius: 0 0 15px 0; border-width: 0 4px 4px 0; }
    .scan-line { position: absolute; width: 100%; height: 4px; background: linear-gradient(to bottom, transparent, #4e73df, transparent); box-shadow: 0 0 15px #4e73df; animation: scanning 3s infinite linear; }
    @keyframes scanning { 0% { top: 10%; opacity: 0; } 50% { opacity: 1; } 100% { top: 90%; opacity: 0; } }
    @keyframes bounceLeft { 0%, 100% { transform: translateX(0); } 50% { transform: translateX(-15px); } }
    .animate-bounce-left { animation: bounceLeft 1s infinite; }
</style>

@if($bolehScan)
<script src="{{ asset('js/face-api/face-api.js') }}"></script>
<script>
    const video = document.getElementById('video');
    const result = document.getElementById('result');
    const scannerWrapper = document.getElementById('scannerWrapper');
    let isProcessing = false;

    const students = [
        @foreach($daftarSiswa as $s)
        { id: "{{ $s->id }}", nama: "{{ $s->nama_siswa }}", foto: "{{ asset('storage/' . $s->foto) }}" },
        @endforeach
    ];

    async function initAI() {
        try {
            const MODEL_URL = '/models';
            await Promise.all([
                faceapi.nets.ssdMobilenetv1.loadFromUri(MODEL_URL),
                faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
                faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)
            ]);

            result.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Sinkronisasi Wajah...';
            const labeledDescriptors = await loadSiswaDescriptors();
            
            if (labeledDescriptors.length === 0) {
                result.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i> Foto Siswa Belum Ada';
                return;
            }

            const faceMatcher = new faceapi.FaceMatcher(labeledDescriptors, 0.6);
            const stream = await navigator.mediaDevices.getUserMedia({ 
                video: { width: 640, height: 480, facingMode: "user" } 
            });
            video.srcObject = stream;

            result.className = "badge badge-success shadow-lg px-4 py-3";
            result.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Sistem Siap';

            video.addEventListener('play', () => {
                setInterval(async () => {
                    if (isProcessing) return;
                    const detections = await faceapi.detectAllFaces(video).withFaceLandmarks().withFaceDescriptors();
                    if (detections.length > 0) {
                        const bestMatch = faceMatcher.findBestMatch(detections[0].descriptor);
                        if (bestMatch.label !== 'unknown') {
                            const [nama, id] = bestMatch.label.split('|');
                            handleAbsensi(id, nama);
                        }
                    }
                }, 1000);
            });
        } catch (err) {
            result.className = "badge badge-danger shadow-lg px-4 py-3";
            result.innerHTML = '<i class="fas fa-times-circle mr-2"></i> Kamera Tidak Aktif';
        }
    }

    async function loadSiswaDescriptors() {
        return Promise.all(
            students.map(async s => {
                try {
                    const img = await faceapi.fetchImage(s.foto);
                    const detection = await faceapi.detectSingleFace(img).withFaceLandmarks().withFaceDescriptor();
                    if(!detection) return null;
                    return new faceapi.LabeledFaceDescriptors(`${s.nama}|${s.id}`, [detection.descriptor]);
                } catch (e) { return null; }
            })
        ).then(res => res.filter(r => r !== null));
    }

    async function handleAbsensi(siswaId, nama) {
        isProcessing = true;
        
        // Feedback Visual & Audio yang lembut
        result.className = "badge badge-warning shadow-lg px-4 py-3";
        result.innerHTML = `<i class="fas fa-user-check mr-2"></i> Mengenali: ${nama}...`;

        try {
            const response = await fetch("{{ route('presensi.store') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({
                    siswa_id: siswaId,
                    jadwal_id: "{{ $jadwalAktif->id }}"
                })
            });

            const data = await response.json();
            if (data.status === 'success') {
                result.className = "badge badge-success shadow-lg px-4 py-3";
                result.innerHTML = `<i class="fas fa-check-double mr-2"></i> Hadir: ${nama}`;
                
                // AUDIO: Volume 0.2 (Sangat lembut agar tidak kaget)
                let audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3'); 
                audio.volume = 0.2;
                audio.play();

                // VISUAL: Border Hijau
                scannerWrapper.style.borderColor = "#28a745";
                setTimeout(() => { scannerWrapper.style.borderColor = "#fff"; }, 2000);
            } else {
                result.innerHTML = `<i class="fas fa-info-circle mr-2"></i> ${nama} Sudah Absen`;
            }
        } catch (e) {
            result.innerHTML = `<i class="fas fa-exclamation-circle mr-2"></i> Koneksi Gagal`;
        }

        setTimeout(() => {
            isProcessing = false;
            result.className = "badge badge-primary shadow-lg px-4 py-3";
            result.innerHTML = '<i class="fas fa-user-shield mr-2"></i> Memindai Wajah...';
        }, 5000);
    }
    
    initAI();
</script>
@endif
@endsection