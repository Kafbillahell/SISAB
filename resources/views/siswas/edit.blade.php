@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-warning text-dark">
            <h6 class="m-0 font-weight-bold">Edit Data & Update Wajah Siswa</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('siswas.update', $siswa->id) }}" method="POST" enctype="multipart/form-data" id="siswaForm">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>NISN</label>
                            <input type="text" name="nisn" class="form-control" value="{{ $siswa->nisn }}" required>
                        </div>
                        <div class="form-group">
                            <label>Nama Siswa</label>
                            <input type="text" name="nama_siswa" class="form-control" value="{{ $siswa->nama_siswa }}" required>
                        </div>
                        <div class="form-group">
                            <label>Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-control">
                                <option value="L" {{ $siswa->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ $siswa->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <hr>
                        <button type="submit" id="btnSubmit" class="btn btn-warning btn-block">
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('siswas.index') }}" class="btn btn-secondary btn-block">Batal</a>
                    </div>

                    <div class="col-md-7 text-center">
                        <label class="font-weight-bold">Update Wajah (Opsional)</label>
                        <div class="position-relative mx-auto rounded-lg overflow-hidden bg-dark shadow" style="width: 400px; height: 300px;">
                            <video id="video" width="400" height="300" autoplay muted style="transform: scaleX(-1);"></video>
                            <canvas id="overlay" class="position-absolute" style="top: 0; left: 0;"></canvas>
                            
                            <div id="cameraFeedback" class="position-absolute w-100 p-2" style="bottom: 0; background: rgba(0,0,0,0.6); color: #fff; font-size: 0.8rem;">
                                Menyiapkan Kamera...
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="button" id="btnCapture" class="btn btn-success shadow-sm">
                                <i class="fas fa-camera mr-2"></i> Ambil Foto Baru
                            </button>
                            <input type="file" name="foto" id="fotoFile" class="d-none">
                            
                            <div id="snapshotContainer" class="mt-3">
                                <p class="small mb-1">Foto Saat Ini / Hasil Baru:</p>
                                <img id="photoPreview" src="{{ $siswa->foto ? asset('storage/'.$siswa->foto) : 'https://via.placeholder.com/150' }}" 
                                     class="img-thumbnail" width="150" style="height: 150px; object-fit: cover;">
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
    const fotoFileInput = document.getElementById('fotoFile');
    const photoPreview = document.getElementById('photoPreview');
    const canvasOverlay = document.getElementById('overlay');

    let modelsLoaded = false;

    async function setupCamera() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: { width: 400, height: 300 } });
            video.srcObject = stream;

            const MODEL_URL = '/models';
            await Promise.all([
                faceapi.nets.ssdMobilenetv1.loadFromUri(MODEL_URL),
                faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL)
            ]);
            
            modelsLoaded = true;
            detectFaceStatus();
        } catch (err) {
            feedback.innerHTML = '<span class="text-danger">Gagal akses kamera!</span>';
        }
    }

    async function detectFaceStatus() {
        if (!modelsLoaded) return;

        const displaySize = { width: video.width, height: video.height };
        faceapi.matchDimensions(canvasOverlay, displaySize);

        setInterval(async () => {
            const detections = await faceapi.detectSingleFace(video).withFaceLandmarks();
            const ctx = canvasOverlay.getContext('2d');
            ctx.clearRect(0, 0, canvasOverlay.width, canvasOverlay.height);

            if (!detections) {
                feedback.innerHTML = '<span class="text-danger"><i class="fas fa-exclamation-triangle"></i> Wajah Tidak Terdeteksi!</span>';
                btnCapture.disabled = true;
            } else {
                const resizedDetections = faceapi.resizeResults(detections, displaySize);
                
                // Gambar Kerangka (Mirroring Fix)
                ctx.save();
                ctx.scale(-1, 1);
                ctx.translate(-canvasOverlay.width, 0);
                faceapi.draw.drawFaceLandmarks(canvasOverlay, resizedDetections);
                ctx.restore();

                const score = detections.detection.score;
                if (score < 0.8) {
                    feedback.innerHTML = '<span class="text-warning"><i class="fas fa-lightbulb"></i> Pencahayaan Kurang</span>';
                    btnCapture.disabled = true;
                } else {
                    feedback.innerHTML = '<span class="text-success"><i class="fas fa-check-circle"></i> Posisi Bagus!</span>';
                    btnCapture.disabled = false;
                }
            }
        }, 100);
    }

    btnCapture.addEventListener('click', () => {
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');
        
        ctx.translate(canvas.width, 0);
        ctx.scale(-1, 1);
        ctx.drawImage(video, 0, 0);

        const dataUrl = canvas.toDataURL('image/jpeg');
        photoPreview.src = dataUrl;
        
        const file = dataURLtoFile(dataUrl, 'update_wajah_{{ $siswa->id }}.jpg');
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        fotoFileInput.files = dataTransfer.files;

        alert("Foto wajah baru telah disiapkan. Klik 'Simpan Perubahan' untuk memperbarui.");
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