@extends('layouts.app')

@section('title', 'Atur Lokasi Absensi')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pengaturan Lokasi GPS</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Map Pemilih Lokasi</h6>
                    <small class="text-muted">Klik pada peta atau geser marker untuk menentukan titik pusat absensi</small>
                </div>
                <!-- Card Body -->
                <div class="card-body p-0">
                    <div id="map" style="height: 500px; width: 100%;"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Konfigurasi Koordinat</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('settings.lokasi.update') }}" method="POST">
                        @csrf
                        @php
                            $coords = explode(',', $lokasi);
                            $lat = $coords[0] ?? '';
                            $lng = $coords[1] ?? '';
                        @endphp
                        
                        <div class="form-group">
                            <label class="font-weight-bold small d-flex justify-content-between">
                                <span>Latitude</span>
                                <a href="#" id="btn-get-location" class="text-primary small font-weight-bold text-decoration-none">
                                    <i class="fas fa-location-arrow mr-1"></i> Gunakan Lokasi Saat Ini
                                </a>
                            </label>
                            <input type="text" name="lat" id="lat" class="form-control" value="{{ $lat }}" readonly required>
                        </div>
                        
                        <div class="form-group">
                            <label class="font-weight-bold small">Longitude</label>
                            <input type="text" name="lng" id="lng" class="form-control" value="{{ $lng }}" readonly required>
                        </div>
                        
                        <div class="form-group">
                            <label class="font-weight-bold small">Radius Absensi (Meter)</label>
                            <div class="input-group">
                                <input type="number" name="radius" id="radius_input" class="form-control" value="{{ $radius }}" required>
                                <div class="input-group-append">
                                    <span class="input-group-text">Meter</span>
                                </div>
                            </div>
                            <small class="text-muted">Jarak maksimal siswa dari titik pusat untuk bisa absen</small>
                        </div>

                        <hr>
                        
                        <button type="submit" class="btn btn-primary btn-block py-2">
                            <i class="fas fa-save mr-1"></i> Simpan Pengaturan
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="card shadow mb-4 border-left-info">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Panduan</div>
                            <div class="small mb-0 text-gray-800">
                                <ul>
                                    <li>Pastikan titik berada tepat di tengah area sekolah.</li>
                                    <li>Radius ideal adalah 50-100 meter.</li>
                                    <li>Gunakan perangkat GPS jika ingin akurasi maksimal.</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-info-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Leaflet.js Assets -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const initialLat = {{ $lat ?: -6.9147 }};
        const initialLng = {{ $lng ?: 107.6098 }};
        const initialRadius = {{ $radius ?: 100 }};

        // Initialize Map
        const map = L.map('map').setView([initialLat, initialLng], 17);

        // Add Tile Layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Add Marker
        let marker = L.marker([initialLat, initialLng], {
            draggable: true
        }).addTo(map);

        // Add Radius Circle
        let circle = L.circle([initialLat, initialLng], {
            color: 'red',
            fillColor: '#f03',
            fillOpacity: 0.2,
            radius: initialRadius
        }).addTo(map);

        // Add Search Control (Geocoder) dengan Autocomplete
        const geocoder = L.Control.Geocoder.nominatim();
        const control = L.Control.geocoder({
            geocoder: geocoder,
            defaultMarkGeocode: false,
            placeholder: 'Masukkan nama sekolah atau alamat...',
            errorMessage: 'Lokasi tidak ditemukan.',
            showUniqueResult: true,
            suggestMinLength: 3, // Mulai mencari setelah 3 karakter
            suggestTimeout: 250   // Delay pencarian (ms) agar tidak terlalu berat
        })
        .on('markgeocode', function(e) {
            const bbox = e.geocode.bbox;
            const center = e.geocode.center;
            
            // Pindahkan marker, circle dan map
            marker.setLatLng(center);
            circle.setLatLng(center);
            map.setView(center, 18); // Zoom lebih dekat saat ditemukan
            
            // Update input teks
            updateInputs(center.lat, center.lng);
        })
        .addTo(map);

        // Langsung fokus ke input search saat map load (Opsional)
        // control.expand(); 

        // Event: Marker dragged
        marker.on('dragend', function(e) {
            const pos = marker.getLatLng();
            updateInputs(pos.lat, pos.lng);
            circle.setLatLng(pos);
        });

        // Event: Map clicked
        map.on('click', function(e) {
            const pos = e.latlng;
            marker.setLatLng(pos);
            circle.setLatLng(pos);
            updateInputs(pos.lat, pos.lng);
        });

        // Event: Radius input changed
        document.getElementById('radius_input').addEventListener('input', function(e) {
            const val = e.target.value;
            circle.setRadius(val);
        });

        // Event: Button Gunakan Lokasi Saat Ini
        document.getElementById('btn-get-location').addEventListener('click', function(e) {
            e.preventDefault();
            const originalHtml = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Mencari...';

            navigator.geolocation.getCurrentPosition((position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                const center = [lat, lng];

                marker.setLatLng(center);
                circle.setLatLng(center);
                map.setView(center, 18);
                updateInputs(lat, lng);

                this.innerHTML = originalHtml;
            }, (err) => {
                alert("Gagal mendapatkan lokasi: " + err.message);
                this.innerHTML = originalHtml;
            }, { enableHighAccuracy: true });
        });

        function updateInputs(lat, lng) {
            document.getElementById('lat').value = lat.toFixed(8);
            document.getElementById('lng').value = lng.toFixed(8);
        }
    });
</script>
@endsection
