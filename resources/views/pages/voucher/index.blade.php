@extends('layouts.app')

@section('title', 'Voucher')

@section('content')
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Tukar Poin dengan Voucher</h1>
            <a href="{{ route('vouchers.myVouchers') }}" class="d-none d-sm-inline-block btn btn-sm btn-info shadow-sm">
                <i class="fas fa-check-circle fa-sm text-white-50"></i> Voucher Milikku
            </a>
        </div>

        <!-- Points Card -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-success font-weight-bold text-uppercase mb-1">Poin Saya Saat Ini</div>
                        <div class="h3 mb-0 text-gray-800">{{ $totalPoints }}</div>
                        <a href="{{ route('points.myPoints') }}" class="small text-success">
                            <i class="fas fa-arrow-right"></i> Lihat Detail Poin
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Voucher Katalog -->
        <div class="row">
            @forelse($vouchers as $voucher)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-body">
                            <h5 class="card-title text-primary">{{ $voucher->name }}</h5>
                            
                            @if($voucher->description)
                                <p class="card-text text-muted small">{{ $voucher->description }}</p>
                            @endif

                            <div class="mb-3">
                                <div class="text-primary font-weight-bold">
                                    <i class="fas fa-coins"></i> {{ $voucher->point_cost }} Poin
                                </div>
                                <small class="text-muted">Sisa: {{ $voucher->getAvailableCount() }} / {{ $voucher->quantity }}</small>
                            </div>

                            @if($voucher->valid_until)
                                <small class="text-warning d-block mb-3">
                                    <i class="fas fa-calendar-times"></i> Berlaku hingga {{ \Carbon\Carbon::parse($voucher->valid_until)->format('d M Y') }}
                                </small>
                            @endif

                            <button class="btn btn-primary btn-sm btn-block redeem-voucher" 
                                    data-voucher-id="{{ $voucher->id }}"
                                    data-voucher-name="{{ $voucher->name }}"
                                    data-voucher-cost="{{ $voucher->point_cost }}"
                                    @if($totalPoints < $voucher->point_cost) disabled @endif>
                                @if($totalPoints < $voucher->point_cost)
                                    <i class="fas fa-times"></i> Poin Kurang
                                @elseif($voucher->quantity <= $voucher->used)
                                    <i class="fas fa-times"></i> Habis
                                @else
                                    <i class="fas fa-exchange-alt"></i> Tukar Sekarang
                                @endif
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Belum ada voucher yang tersedia saat ini.
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Modal Konfirmasi -->
    <div class="modal fade" id="confirmRedeemModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Tukar Voucher</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menukar voucher berikut?</p>
                    <div class="alert alert-info">
                        <strong id="confirmVoucherName"></strong><br>
                        <span id="confirmVoucherCost"></span> Poin
                    </div>
                    <p class="text-muted small">Voucher akan langsung masuk ke akun Anda dan dapat digunakan kapan saja.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="confirmRedeemBtn">Tukar Sekarang</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedVoucherId = null;

        document.querySelectorAll('.redeem-voucher').forEach(button => {
            button.addEventListener('click', function() {
                if (this.disabled) return;

                selectedVoucherId = this.dataset.voucherId;
                document.getElementById('confirmVoucherName').textContent = this.dataset.voucherName;
                document.getElementById('confirmVoucherCost').textContent = this.dataset.voucherCost;

                $('#confirmRedeemModal').modal('show');
            });
        });

        document.getElementById('confirmRedeemBtn').addEventListener('click', function() {
            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';

            fetch('{{ route("vouchers.redeem") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    voucher_id: selectedVoucherId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message,
                    });
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-exchange-alt"></i> Tukar Sekarang';
                }
                $('#confirmRedeemModal').modal('hide');
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat memproses: ' + error,
                });
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-exchange-alt"></i> Tukar Sekarang';
            });
        });
    </script>

    <style>
        .card {
            border-radius: 8px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .btn-block {
            width: 100%;
        }
    </style>
@endsection
