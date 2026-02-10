@extends('layouts.app')

@section('content')
<style>
    /* Styling tetap sama dengan Create untuk konsistensi UI */
    .video-wrapper {
        position: relative; width: 400px; height: 300px; margin: 0 auto;
        border-radius: 12px; overflow: hidden; background: #000;
        box-shadow: 0 8px 20px rgba(0,0,0,0.3); border: 2px solid #334155;
    }
    #video { position: absolute; top: 0; left: 0; transform: scaleX(-1); width: 100%; height: 100%; object-fit: cover; }
    .scanner-svg-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 10; pointer-events: none; }
    .blur-mask-bg { fill: rgba(255, 255, 255, 0.4); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); mask: url(#face-hole); -webkit-mask: url(#face-hole); }
    .outline-path { fill: none; stroke: #ffffff; stroke-width: 2.5; stroke-dasharray: 8, 4; filter: drop-shadow(0 0 5px rgba(0, 0, 0, 0.5)); }
    .scan-line { position: absolute; left: 125px; width: 150px; height: 2px; background: linear-gradient(to right, transparent, #3b82f6, transparent); box-shadow: 0 0 15px #3b82f6; z-index: 11; animation: scanAnim 3s infinite ease-in-out; display: none; }
    @keyframes scanAnim { 0% { top: 60px; opacity: 0; } 50% { opacity: 1; } 100% { top: 230px; opacity: 0; } }
    #cameraFeedback { position: absolute; bottom: 15px; left: 50%; transform: translateX(-50%); width: 80%; padding: 6px; border-radius: 50px; background: rgba(0, 0, 0, 0.8); color: #fff; font-size: 0.7rem; font-weight: bold; text-align: center; z-index: 20; }
</style>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-info text-white">
            <h6 class="m-0 font-weight-bold">Edit Data Siswa: {{ $siswa->nama_siswa }}</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('siswas.update', $siswa->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label class="small font-weight-bold">NISN</label>
                            <input type="text" name="nisn" class="form-control" value="{{ $siswa->nisn }}" required>
                        </div>
                        <div class="form-group">
                            <label class="small font-weight-bold">Nama Siswa</label>
                            <input type="text" name="nama_siswa" class="form-control" value="{{ $siswa->nama_siswa }}" required>
                        </div>
                        <div class="form-group">
                            <label class="small font-weight-bold">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-control">
                                <option value="L" {{ $siswa->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ $siswa->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        
                        <div class="alert alert-secondary p-2 small">
                            <i class="fas fa-info-circle mr-1"></i> Biarkan kamera jika tidak ingin mengubah data wajah/foto.
                        </div>

                        <hr>
                        <button type="submit" class="btn btn-info btn-block shadow-sm font-weight-bold">
                            Perbarui Data Siswa
                        </button>
                        <a href="{{ route('siswas.index') }}" class="btn btn-light btn-block border">Kembali</a>
                    </div>

                    <div class="col-md-7 text-center">
                        <label class="font-weight-bold">Update Wajah (Opsional)</label>
                        
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
                                <path class="outline-path" d="M200,45 c-40,0 -70,25 -75,85 c-3,40 10,80 30,105 c15,18 30,30 45,30 s30,-12 45,-30 c20,-25 33,-65 30,-105 c-5,-60 -35,-85 -75,-85 Z" />
                            </svg>
                            <div id="scannerLine" class="scan-line"></div>
                            <div id="cameraFeedback">MENGAKTIFKAN KAMERA...</div>
                        </div>

                        <div class="mt-3">
                            <button type="button" id="btnCapture" class="btn btn-warning shadow-sm" disabled>
                                <i class="fas fa-sync mr-2"></i> Ganti Foto Wajah
                            </button>
                            <input type="file" name="foto" id="fotoFile" class="d-none">
                            
                            <div id="snapshotContainer" class="mt-3">
                                <p class="small mb-1 font-weight-bold">Foto Saat Ini / Baru:</p>
                                <img id="photoPreview" 
                                     src="{{ $siswa->foto ? asset('storage/'.$siswa->foto) : asset('img/default-user.png') }}" 
                                     class="img-thumbnail rounded-circle" width="120" height="120" 
                                     style="object-fit: cover; border: 3px solid #3b82f6;">
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
    const scannerLine = document.getElementById('scannerLine');

    // Fungsi pencahayaan (Reuse dari Create)
    function getBrightness(video) {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = 100; canvas.height = 100;
        ctx.drawImage(video, 0, 0, 100, 100);
        const data = ctx.getImageData(0,0,100,100).data;
        let brightness = 0;
        for(let i=0; i<data.length; i+=4) brightness += (data[i] + data[i+1] + data[i+2]) / 3;
        return brightness / (data.length / 4);
    }

    async function init() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: { width: 400, height: 300 } });
            video.srcObject = stream;
            await faceapi.nets.ssdMobilenetv1.loadFromUri('/models');
            validateStream();
        } catch (err) {
            feedback.innerHTML = "Kamera Tidak Aktif";
        }
    }

    async function validateStream() {
        setInterval(async () => {
            const detection = await faceapi.detectSingleFace(video);
            const brightness = getBrightness(video);
            
            if (!detection) {
                feedback.innerHTML = "Mencari Wajah...";
                scannerLine.style.display = "none";
                btnCapture.disabled = true;
            } else {
                const box = detection.box;
                scannerLine.style.display = "block";
                if (brightness < 50) {
                    feedback.innerHTML = "Terlalu Gelap!";
                    btnCapture.disabled = true;
                } else if (box.width < 140) {
                    feedback.innerHTML = "Kurang Dekat";
                    btnCapture.disabled = true;
                } else {
                    feedback.innerHTML = "Wajah Siap Diupdate";
                    btnCapture.disabled = false;
                }
            }
        }, 500);
    }

    btnCapture.addEventListener('click', () => {
        const canvas = document.createElement('canvas');
        canvas.width = 400; canvas.height = 300;
        const ctx = canvas.getContext('2d');
        ctx.translate(400, 0); ctx.scale(-1, 1);
        ctx.drawImage(video, 0, 0, 400, 300);

        const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
        document.getElementById('photoPreview').src = dataUrl;
        
        // Convert to file
        let arr = dataUrl.split(','), bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
        while(n--) u8arr[n] = bstr.charCodeAt(n);
        const file = new File([u8arr], 'face_update.jpg', {type:'image/jpeg'});
        
        const dt = new DataTransfer();
        dt.items.add(file);
        document.getElementById('fotoFile').files = dt.files;
        
        alert("Foto baru siap diperbarui!");
    });

    init();
</script>
@endsection