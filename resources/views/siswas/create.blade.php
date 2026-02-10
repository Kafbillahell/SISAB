@extends('layouts.app')

@section('content')
<style>
    /* CSS Mirror & Minimalist Style */
    .video-wrapper { 
        position: relative; 
        display: inline-block; 
        overflow: hidden; 
        border-radius: 15px; 
        border: 5px solid #e3e6f0; 
        background: #000; 
    }
    
    #video { 
        transform: scaleX(-1); /* Membuat kamera menjadi mirror */
        -webkit-transform: scaleX(-1);
    }

    .scanner-svg-overlay { 
        position: absolute; 
        top: 0; 
        left: 0; 
        width: 100%; 
        height: 100%; 
        pointer-events: none; 
    }
    
    .blur-mask-bg { fill: rgba(0, 0, 0, 0.6); } 
    
    .outline-path { 
        fill: none; 
        stroke: #ffffff; 
        stroke-width: 3; 
        transition: stroke 0.3s ease, stroke-width 0.3s ease; 
    }
    
    /* Indikator Wajah Siap (Statis Hijau) */
    .outline-ready { 
        stroke: #10b981; 
        stroke-width: 4; 
    }

    #cameraFeedback { 
        position: absolute; 
        bottom: 20px; 
        left: 50%; 
        transform: translateX(-50%); 
        padding: 8px 20px; 
        border-radius: 30px; 
        color: white; 
        font-size: 13px; 
        font-weight: bold; 
        z-index: 20; 
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    #cameraFeedback.status-ready { background: rgba(16, 185, 129, 0.95); box-shadow: 0 0 15px rgba(16, 185, 129, 0.5); }
    #cameraFeedback.status-wait { background: rgba(31, 41, 55, 0.8); }
    .loading-input { background-image: url('https://i.gifer.com/ZZ5H.gif'); background-repeat: no-repeat; background-position: right 10px center; background-size: 20px; }
</style>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold">Registrasi Wajah Siswa</h6>
            <a href="{{ route('siswas.index') }}" class="btn btn-light btn-sm">Kembali</a>
        </div>
        <div class="card-body">
            <form action="{{ route('siswas.store') }}" method="POST" enctype="multipart/form-data" id="siswaForm">
                @csrf
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label class="small font-weight-bold">NISN</label>
                            <input type="text" name="nisn" id="nisnInput" class="form-control" value="{{ $nisn ?? old('nisn') }}" required>
                            <small class="text-muted" id="nisnHelp">Verifikasi data via ZieLabs API</small>
                        </div>
                        <div class="form-group">
                            <label class="small font-weight-bold">Nama Siswa</label>
                            <input type="text" name="nama_siswa" id="nama_siswa" class="form-control" readonly value="{{ $nama ?? old('nama_siswa') }}" required>
                        </div>
                        <div class="form-group">
                            <label class="small font-weight-bold">Jenis Kelamin</label>
                            <select name="jenis_kelamin" id="jkInput" class="form-control" readonly>
                                <option value="L" {{ ($jk ?? old('jenis_kelamin')) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ ($jk ?? old('jenis_kelamin')) == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <hr>
                        <button type="submit" id="btnSubmit" class="btn btn-primary btn-block shadow-sm" disabled>
                            <i class="fas fa-save mr-2"></i>Simpan Registrasi
                        </button>
                    </div>

                    <div class="col-md-7 text-center">
                        <div class="video-wrapper">
                            <video id="video" width="400" height="300" autoplay muted></video>
                            <svg class="scanner-svg-overlay" viewBox="0 0 400 300">
                                <defs>
                                    <mask id="face-hole">
                                        <rect width="400" height="300" fill="white" />
                                        <path d="M200,45 c-40,0 -70,25 -75,85 c-3,40 10,80 30,105 c15,18 30,30 45,30 s30,-12 45,-30 c20,-25 33,-65 30,-105 c-5,-60 -35,-85 -75,-85 Z" fill="black" />
                                    </mask>
                                </defs>
                                <rect width="400" height="300" class="blur-mask-bg" mask="url(#face-hole)" />
                                <path id="faceOutline" class="outline-path" d="M200,45 c-40,0 -70,25 -75,85 c-3,40 10,80 30,105 c15,18 30,30 45,30 s30,-12 45,-30 c20,-25 33,-65 30,-105 c-5,-60 -35,-85 -75,-85 Z" />
                            </svg>
                            <div id="cameraFeedback" class="status-wait">Mencari Wajah...</div>
                        </div>
                        
                        <div class="mt-3">
                            <button type="button" id="btnCapture" class="btn btn-success btn-lg px-4 shadow-sm" disabled>
                                <i class="fas fa-camera mr-2"></i>Ambil Foto
                            </button>
                            <input type="file" name="foto" id="fotoFile" class="d-none" required>
                            
                            <div id="snapshotContainer" class="mt-3 d-none">
                                <img id="photoPreview" src="" class="img-thumbnail rounded-circle" width="100" height="100" style="object-fit: cover; border: 3px solid #10b981; transform: scaleX(-1);">
                                <p class="small text-success font-weight-bold mt-1">Foto Berhasil Ditangkap</p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="{{ asset('js/face-api/face-api.js') }}"></script>
<script>
    const nisnInput = document.getElementById('nisnInput');
    const namaInput = document.getElementById('nama_siswa');
    const jkInput = document.getElementById('jkInput');

    function fetchSiswaData(nisn) {
        if (nisn.length < 5) return;
        nisnInput.classList.add('loading-input');
        fetch(`/siswas/search-api/${nisn}`).then(res => res.json()).then(data => {
            nisnInput.classList.remove('loading-input');
            if (data.success) {
                namaInput.value = data.nama; jkInput.value = data.jk;
                namaInput.readOnly = true; jkInput.readOnly = true;
                document.getElementById('nisnHelp').innerHTML = "<span class='text-success'>Data Terverifikasi</span>";
            } else {
                namaInput.readOnly = false; jkInput.readOnly = false;
                document.getElementById('nisnHelp').innerHTML = "<span class='text-danger'>Data Tidak Ditemukan</span>";
            }
        });
    }
    if (nisnInput.value) { fetchSiswaData(nisnInput.value); }
    nisnInput.addEventListener('blur', function() { fetchSiswaData(this.value); });

    // --- FACE API LOGIC ---
    const video = document.getElementById('video');
    const feedback = document.getElementById('cameraFeedback');
    const btnCapture = document.getElementById('btnCapture');
    const faceOutline = document.getElementById('faceOutline');

    async function init() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: { width: 400, height: 300 } });
            video.srcObject = stream;
            await faceapi.nets.ssdMobilenetv1.loadFromUri('/models');
            validateStream();
        } catch (err) { feedback.innerHTML = "Kamera Tidak Terdeteksi"; }
    }

    async function validateStream() {
        setInterval(async () => {
            if (video.paused || video.ended) return;
            const detection = await faceapi.detectSingleFace(video);
            
            if (!detection) {
                updateUI("Posisikan Wajah", "status-wait", false);
            } else {
                const box = detection.box;
                if (box.width < 150) {
                    updateUI("Mendekat ke Kamera", "status-wait", false);
                } else {
                    updateUI("Wajah Siap!", "status-ready", true);
                }
            }
        }, 400);
    }

    function updateUI(text, className, allowCapture) {
        feedback.innerHTML = text;
        feedback.className = className;
        btnCapture.disabled = !allowCapture;
        
        if(allowCapture) {
            faceOutline.classList.add('outline-ready');
        } else {
            faceOutline.classList.remove('outline-ready');
        }
    }

    btnCapture.addEventListener('click', () => {
        const canvas = document.createElement('canvas');
        canvas.width = 400; 
        canvas.height = 300;
        const ctx = canvas.getContext('2d');
        
        // Membalikkan hasil gambar di canvas agar sinkron dengan video mirror
        ctx.translate(400, 0); 
        ctx.scale(-1, 1);
        ctx.drawImage(video, 0, 0, 400, 300);

        const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
        document.getElementById('photoPreview').src = dataUrl;
        document.getElementById('snapshotContainer').classList.remove('d-none');
        
        const file = dataURLtoFile(dataUrl, 'face.jpg');
        const dt = new DataTransfer();
        dt.items.add(file);
        document.getElementById('fotoFile').files = dt.files;
        document.getElementById('btnSubmit').disabled = false;
    });

    function dataURLtoFile(dataurl, filename) {
        let arr = dataurl.split(','), bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
        while(n--) u8arr[n] = bstr.charCodeAt(n);
        return new File([u8arr], filename, {type:'image/jpeg'});
    }

    init();
</script>
@endsection