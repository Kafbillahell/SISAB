@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-11">
            <div class="card border-0 shadow-lg overflow-hidden" style="border-radius: 20px;">
                <div class="row no-gutters">
                    <div class="col-lg-4 bg-gradient-primary p-5 d-flex flex-column justify-content-center text-white">
                        <div class="mb-4">
                            <i class="fas fa-id-badge fa-3x mb-3 text-white-50"></i>
                            <h2 class="font-weight-bold">Smart Attendance</h2>
                            <p class="text-white-50">Silakan posisikan wajah Anda tepat di dalam area frame scanner.</p>
                        </div>

                        @if($jadwalAktif)
                        <div class="bg-white-core p-4 rounded-lg shadow-sm" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); backdrop-filter: blur(10px);">
                            <small class="text-white-50 text-uppercase letter-spacing-1">Mata Pelajaran</small>
                            <h4 class="font-weight-bold mb-3">{{ $jadwalAktif->mapel->nama_mapel }}</h4>
                            
                            <div class="d-flex align-items-center">
                                <div class="badge badge-pill badge-light px-3 py-2 mr-2">
                                    <i class="fas fa-users mr-1"></i> {{ $jadwalAktif->rombel->nama_rombel }}
                                </div>
                                <div class="badge badge-pill badge-success px-3 py-2">
                                    <i class="fas fa-clock mr-1"></i> Aktif
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="alert alert-warning border-0 shadow-sm">
                            <i class="fas fa-calendar-times mr-2"></i> Tidak ada jadwal aktif.
                        </div>
                        @endif

                        <div class="mt-auto pt-4">
                            <a href="{{ route('presensi.index') }}" class="btn btn-link text-white text-decoration-none p-0">
                                <i class="fas fa-chevron-left mr-2"></i> Kembali ke Dashboard
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-8 bg-light p-4 p-md-5">
                        <div class="scanner-container position-relative mx-auto shadow-lg bg-black rounded-xl overflow-hidden" style="max-width: 600px; border-radius: 30px; border: 8px solid #fff;">
                            
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom Styling */
    .bg-black { background-color: #000; }
    .letter-spacing-1 { letter-spacing: 1px; }
    
    .scanner-overlay {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        display: flex; align-items: center; justify-content: center;
        pointer-events: none;
    }

    /* Frame Kotak Wajah */
    .face-frame {
        width: 250px; height: 250px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 40px;
        position: relative;
        box-shadow: 0 0 0 1000px rgba(0, 0, 0, 0.5); /* Efek redup di luar kotak */
    }

    .face-frame::before, .face-frame::after {
        content: ''; position: absolute; width: 40px; height: 40px;
        border: 4px solid #4e73df;
    }

    /* Siku-siku Frame */
    .face-frame { border: 2px solid rgba(255,255,255,0.2); }
    .face-frame::before { top: -4px; left: -4px; border-right: 0; border-bottom: 0; border-radius: 15px 0 0 0; }
    .face-frame::after { bottom: -4px; right: -4px; border-left: 0; border-top: 0; border-radius: 0 0 15px 0; }

    /* Garis Scan Running */
    .scan-line {
        position: absolute;
        width: 100%; height: 4px;
        background: linear-gradient(to bottom, transparent, #4e73df, transparent);
        box-shadow: 0 0 15px #4e73df;
        animation: scanning 3s infinite linear;
    }

    @keyframes scanning {
        0% { top: 10%; opacity: 0; }
        50% { opacity: 1; }
        100% { top: 90%; opacity: 0; }
    }
</style>

<script src="{{ asset('js/face-api/face-api.js') }}"></script>

<script>
    const video = document.getElementById('video');
    const result = document.getElementById('result');
    let isProcessing = false; // Flag agar tidak absen berkali-kali

    // 1. Data Siswa dari Database (Dikirim lewat Controller)
    const students = [
        @foreach($daftarSiswa as $s)
        { 
            id: "{{ $s->id }}", 
            nama: "{{ $s->nama_siswa }}", 
            foto: "{{ asset('storage/' . $s->foto) }}" 
        },
        @endforeach
    ];

    async function initAI() {
        try {
            // 2. Load Model AI dari folder /public/models
            const MODEL_URL = '/models';
            await Promise.all([
                faceapi.nets.ssdMobilenetv1.loadFromUri(MODEL_URL),
                faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
                faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)
            ]);

            result.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Mendaftarkan Wajah Siswa...';

            // 3. Daftarkan "Sidik Jari" Wajah Siswa
            const labeledDescriptors = await loadSiswaDescriptors();
            
            if (labeledDescriptors.length === 0) {
                result.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i> Foto Siswa Belum Ada';
                return;
            }

            const faceMatcher = new faceapi.FaceMatcher(labeledDescriptors, 0.6);

            // 4. Jalankan Kamera
            const stream = await navigator.mediaDevices.getUserMedia({ 
                video: { width: 640, height: 480, facingMode: "user" } 
            });
            video.srcObject = stream;

            result.className = "badge badge-success shadow-lg px-4 py-3";
            result.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Sistem Siap - Silakan Scan';

            // 5. Mulai Deteksi Real-time
            video.addEventListener('play', () => {
                setInterval(async () => {
                    if (isProcessing) return; // Skip jika sedang proses simpan data

                    const detections = await faceapi.detectAllFaces(video)
                        .withFaceLandmarks()
                        .withFaceDescriptors();

                    if (detections.length > 0) {
                        const bestMatch = faceMatcher.findBestMatch(detections[0].descriptor);
                        
                        if (bestMatch.label !== 'unknown') {
                            const [nama, id] = bestMatch.label.split('|');
                            handleAbsensi(id, nama); // Panggil fungsi simpan absen
                        }
                    }
                }, 1000); // Cek setiap 1 detik
            });

        } catch (err) {
            console.error(err);
            result.className = "badge badge-danger shadow-lg px-4 py-3";
            result.innerHTML = '<i class="fas fa-times-circle mr-2"></i> Error: Model Gagal Dimuat';
        }
    }

    async function loadSiswaDescriptors() {
        return Promise.all(
            students.map(async s => {
                try {
                    const img = await faceapi.fetchImage(s.foto);
                    const detection = await faceapi.detectSingleFace(img).withFaceLandmarks().withFaceDescriptor();
                    return new faceapi.LabeledFaceDescriptors(`${s.nama}|${s.id}`, [detection.descriptor]);
                } catch (e) {
                    console.warn("Gagal scan foto: " + s.nama);
                    return null;
                }
            })
        ).then(res => res.filter(r => r !== null));
    }

    async function handleAbsensi(siswaId, nama) {
        isProcessing = true;
        result.className = "badge badge-warning shadow-lg px-4 py-3";
        result.innerHTML = `<i class="fas fa-user-check mr-2"></i> Mengenali: ${nama}...`;

        try {
            const response = await fetch("{{ route('presensi.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    siswa_id: siswaId,
                    jadwal_id: "{{ $jadwalAktif->id ?? '' }}"
                })
            });

            const data = await response.json();

            if (data.status === 'success') {
                result.className = "badge badge-success shadow-lg px-4 py-3";
                result.innerHTML = `<i class="fas fa-check-double mr-2"></i> Hadir: ${nama}`;
                new Audio('https://www.soundjay.com/buttons/beep-07a.mp3').play();
            } else {
                result.innerHTML = `<i class="fas fa-info-circle mr-2"></i> ${nama} Sudah Absen`;
            }
        } catch (e) {
            result.innerHTML = `<i class="fas fa-exclamation-circle mr-2"></i> Gagal Simpan Absen`;
        }

        // Delay 5 detik agar tidak spam absen
        setTimeout(() => {
            isProcessing = false;
            result.className = "badge badge-primary shadow-lg px-4 py-3";
            result.innerHTML = '<i class="fas fa-user-shield mr-2"></i> Sensor Aktif - Memindai...';
        }, 5000);
    }

    // Jalankan AI
    initAI();
</script>
@endsection