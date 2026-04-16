@extends('layouts.app')

@section('title', isset($voucher) ? 'Edit Voucher' : 'Tambah Voucher')

@section('content')
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ isset($voucher) ? 'Edit Voucher' : 'Tambah Voucher Baru' }}</h1>
            <a href="{{ route('admin.vouchers.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm"></i> Kembali
            </a>
        </div>

        <!-- Form Card -->
        <div class="card shadow">
            <div class="card-body p-5">
                <form action="{{ isset($voucher) ? route('admin.vouchers.update', $voucher) : route('admin.vouchers.store') }}" method="POST">
                    @csrf
                    @if(isset($voucher))
                        @method('PUT')
                    @endif

                    <!-- Nama Voucher -->
                    <div class="mb-4">
                        <label for="name" class="form-label fw-bold">
                            <i class="fas fa-tag"></i> Nama Voucher <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                            id="name" name="name" 
                            value="{{ old('name', $voucher->name ?? '') }}" 
                            placeholder="Contoh: Voucher Kehadiran 100%"
                            required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Deskripsi -->
                    <div class="mb-4">
                        <label for="description" class="form-label fw-bold">
                            <i class="fas fa-file-alt"></i> Deskripsi <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                            id="description" name="description" rows="3"
                            placeholder="Jelaskan apa gunanya voucher ini dan benefit yang didapat"
                            required>{{ old('description', $voucher->description ?? '') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Contoh: Voucher ini dapat digunakan untuk tidak dikurangi poin ketika Anda terlambat</small>
                    </div>

                    <!-- Point Cost -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="point_cost" class="form-label fw-bold">
                                <i class="fas fa-coins"></i> Biaya Point <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control @error('point_cost') is-invalid @enderror" 
                                id="point_cost" name="point_cost" min="1"
                                value="{{ old('point_cost', $voucher->point_cost ?? 15) }}"
                                required>
                            @error('point_cost')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Berapa point yang dibutuhkan untuk menukar voucher ini?</small>
                        </div>

                        <!-- Quantity -->
                        <div class="col-md-6">
                            <label for="quantity" class="form-label fw-bold">
                                <i class="fas fa-boxes"></i> Jumlah Ketersediaan <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                id="quantity" name="quantity" min="1"
                                value="{{ old('quantity', $voucher->quantity ?? 10) }}"
                                required>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Berapa banyak voucher yang bisa ditukar?</small>
                        </div>
                    </div>

                    <!-- Usage Type -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="fas fa-clock"></i> Kapan Voucher Bisa Digunakan? <span class="text-danger">*</span>
                        </label>
                        <div class="card border-light">
                            <div class="card-body">
                                <!-- Option 1: Anytime -->
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="usage_type" id="usage_anytime" 
                                        value="anytime" 
                                        {{ old('usage_type', $voucher->usage_type ?? 'anytime') === 'anytime' ? 'checked' : '' }}
                                        onchange="toggleValidMinutes()">
                                    <label class="form-check-label" for="usage_anytime">
                                        <strong><i class="fas fa-infinity"></i> Setiap Saat (Anytime)</strong>
                                        <br>
                                        <small class="text-muted">Voucher bisa digunakan kapan saja saat siswa terlambat</small>
                                    </label>
                                </div>

                                <!-- Option 2: After Lesson -->
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="usage_type" id="usage_after_lesson" 
                                        value="after_lesson"
                                        {{ old('usage_type', $voucher->usage_type ?? '') === 'after_lesson' ? 'checked' : '' }}
                                        onchange="toggleValidMinutes()">
                                    <label class="form-check-label" for="usage_after_lesson">
                                        <strong><i class="fas fa-hourglass-end"></i> Terbatas Waktu (Setelah Jam Pelajaran)</strong>
                                        <br>
                                        <small class="text-muted">Voucher hanya bisa digunakan dalam beberapa menit setelah jam pelajaran selesai</small>
                                    </label>
                                    
                                    <!-- Valid Minutes Input -->
                                    <div class="mt-2 ms-4" id="validMinutesDiv" 
                                        style="display: {{ old('usage_type', $voucher->usage_type ?? '') === 'after_lesson' ? 'block' : 'none' }}">
                                        <label for="valid_minutes" class="form-label">
                                            Berapa menit setelah jam pelajaran? <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="number" class="form-control @error('valid_minutes') is-invalid @enderror" 
                                                id="valid_minutes" name="valid_minutes" min="1"
                                                value="{{ old('valid_minutes', $voucher->valid_minutes ?? 10) }}"
                                                placeholder="Contoh: 10">
                                            <span class="input-group-text">Menit</span>
                                        </div>
                                        @error('valid_minutes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Valid Until -->
                    @if(isset($voucher))
                        <div class="mb-4">
                            <label for="valid_until" class="form-label fw-bold">
                                <i class="fas fa-calendar-check"></i> Tanggal Kadaluarsa
                            </label>
                            <input type="date" class="form-control @error('valid_until') is-invalid @enderror" 
                                id="valid_until" name="valid_until"
                                value="{{ old('valid_until', $voucher->valid_until ? $voucher->valid_until->format('Y-m-d') : '') }}">
                            @error('valid_until')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Kosongkan jika voucher tidak ada batas waktu</small>
                        </div>

                        <!-- Is Active -->
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                    value="1" {{ $voucher->is_active ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    <strong>Aktifkan Voucher</strong>
                                    <br>
                                    <small class="text-muted">Unchecked = Voucher tidak bisa ditukar siswa</small>
                                </label>
                            </div>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ isset($voucher) ? 'Simpan Perubahan' : 'Buat Voucher' }}
                        </button>
                        <a href="{{ route('admin.vouchers.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleValidMinutes() {
            const validMinutesDiv = document.getElementById('validMinutesDiv');
            const usageType = document.querySelector('input[name="usage_type"]:checked').value;
            
            if(usageType === 'after_lesson') {
                validMinutesDiv.style.display = 'block';
                document.getElementById('valid_minutes').required = true;
            } else {
                validMinutesDiv.style.display = 'none';
                document.getElementById('valid_minutes').required = false;
            }
        }
    </script>
@endsection
