@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold">Tambah Siswa & Pendaftaran Wajah Real-time</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('siswas.store') }}" method="POST" enctype="multipart/form-data" id="siswaForm">
                @csrf
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>NISN</label>
                            <input type="text" name="nisn" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Nama Siswa</label>
                            <input type="text" name="nama_siswa" id="nama_siswa" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-control">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <hr>
                        <button type="submit" id="btnSubmit" class="btn btn-primary btn-block" disabled>
                            Simpan Data (Ambil Foto Dulu)
                        </button>
                    </div>

                    <div class="col-md-7 text-center">
                        <label class="font-weight-bold">Pendaftaran Wajah</label>
                        <div class="position-relative mx-auto rounded-lg overflow-hidden bg-dark shadow" style="width: 400px; height: 300px;">
                            <video id="video" width="400" height="300" autoplay muted style="transform: scaleX(-1);"></video>
                            <canvas id="overlay" class="position-absolute" style="top: 0; left: 0;"></canvas>
                            
                            <div id="cameraFeedback" class="position-absolute w-100 p-2" style="bottom: 0; background: rgba(0,0,0,0.6); color: #fff; font-size: 0.8rem;">
                                Menyiapkan Kamera...
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="button" id="btnCapture" class="btn btn-success shadow-sm">
                                <i class="fas fa-camera mr-2"></i> Ambil Foto Wajah
                            </button>
                            <input type="file" name="foto" id="fotoFile" class="d-none">
                            
                            <div id="snapshotContainer" class="mt-3 d-none">
                                <p class="small mb-1">Hasil Foto:</p>
                                <img id="photoPreview" src="" class="img-thumbnail" width="150">
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
    const video = document.getElementById('video');
    const feedback = document.getElementById('cameraFeedback');
    const btnCapture = document.getElementById('btnCapture');
    const btnSubmit = document.getElementById('btnSubmit');
    const fotoFileInput = document.getElementById('fotoFile');
    const photoPreview = document.getElementById('photoPreview');
    const snapshotContainer = document.getElementById('snapshotContainer');

    let modelsLoaded = false;

    async function setupCamera() {
        const stream = await navigator.mediaDevices.getUserMedia({ video: { width: 400, height: 300 } });
        video.srcObject = stream;

        // Load models khusus untuk deteksi saat mendaftar
        const MODEL_URL = '/models';
        await faceapi.nets.ssdMobilenetv1.loadFromUri(MODEL_URL);
        await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
        modelsLoaded = true;
        
        detectFaceStatus();
    }

    async function detectFaceStatus() {
    if (!modelsLoaded) return;

    // Ambil elemen canvas overlay
    const canvas = document.getElementById('overlay');
    const displaySize = { width: video.width, height: video.height };
    faceapi.matchDimensions(canvas, displaySize);

    setInterval(async () => {
        // Deteksi wajah beserta titik kerangkanya (landmarks)
        const detections = await faceapi.detectSingleFace(video)
            .withFaceLandmarks();

        // Bersihkan canvas setiap kali deteksi ulang
        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        if (!detections) {
            feedback.innerHTML = '<span class="text-danger"><i class="fas fa-exclamation-triangle"></i> Wajah Tidak Terdeteksi!</span>';
            btnCapture.disabled = true;
        } else {
            // Resize hasil deteksi agar pas dengan ukuran video
            const resizedDetections = faceapi.resizeResults(detections, displaySize);
            
            // --- BAGIAN MENGGAMBAR KERANGKA ---
            // Kita balikkan canvas secara horizontal agar sinkron dengan video yang di-mirror
            ctx.save();
            ctx.scale(-1, 1);
            ctx.translate(-canvas.width, 0);
            
            // Gambar garis titik wajah (landmarks)
            faceapi.draw.drawFaceLandmarks(canvas, resizedDetections);
            
            ctx.restore();
            // ----------------------------------

            // Logika validasi skor (pencahayaan/kualitas)
            const score = detections.detection.score;
            if (score < 0.8) {
                feedback.innerHTML = '<span class="text-warning"><i class="fas fa-lightbulb"></i> Pencahayaan Kurang / Wajah Buram</span>';
                btnCapture.disabled = true;
            } else {
                feedback.innerHTML = '<span class="text-success"><i class="fas fa-check-circle"></i> Posisi Bagus! Silakan Ambil Foto</span>';
                btnCapture.disabled = false;
            }
        }
    }, 100); // Dipercepat ke 100ms agar gerakan kerangka terasa mulus
}

    btnCapture.addEventListener('click', () => {
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');
        
        // Mirroring kembali saat capture agar hasil foto tidak terbalik
        ctx.translate(canvas.width, 0);
        ctx.scale(-1, 1);
        ctx.drawImage(video, 0, 0);

        const dataUrl = canvas.toDataURL('image/jpeg');
        
        // Tampilkan Pratinjau
        photoPreview.src = dataUrl;
        snapshotContainer.classList.remove('d-none');
        
        // Konversi DataURL ke File Object agar bisa dikirim via Form
        const file = dataURLtoFile(dataUrl, 'wajah_siswa.jpg');
        
        // Masukkan ke input file hidden
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        fotoFileInput.files = dataTransfer.files;

        // Aktifkan tombol simpan
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = "Simpan Data & Wajah";
        alert("Wajah berhasil diambil!");
    });

    function dataURLtoFile(dataurl, filename) {
        var arr = dataurl.split(','), mime = arr[0].match(/:(.*?);/)[1],
            bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
        while(n--){ u8arr[n] = bstr.charCodeAt(n); }
        return new File([u8arr], filename, {type:mime});
    }

    setupCamera();
</script>
@endsection