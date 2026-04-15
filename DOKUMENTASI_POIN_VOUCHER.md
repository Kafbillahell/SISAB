# Dokumentasi Sistem Poin dan Voucher Siswa

## 📋 Ringkasan Fitur

Sistem poin dan voucher memungkinkan siswa mengumpulkan poin dari kehadiran mereka dan menukarnya dengan voucher untuk menghindari pengurangan poin saat terlambat.

## 🎯 Cara Kerja Sistem

### 1. **Poin Otomatis** (Dihitung saat presensi dibuat)
- **Tepat Waktu**: +10 Poin (jika waktu scan ≤ jam mulai jadwal)
- **Terlambat**: -5 Poin (jika waktu scan > jam mulai jadwal)

### 2. **Sistem Voucher**
- Siswa bisa menukar **15 poin** untuk mendapatkan **1 voucher**
- Voucher otomatis digunakan saat siswa terlambat
- Voucher akan membatalkan pengurangan poin (-5 menjadi 0)
- Siswa bisa melihat stok voucher dan riwayat penggunaan

## 📂 File-File yang Dibuat

### Database (Migrations)
```
database/migrations/2026_04_15_000000_create_point_rules_table.php
database/migrations/2026_04_15_000001_add_points_to_presensis_table.php
database/migrations/2026_04_15_000002_create_vouchers_table.php
database/migrations/2026_04_15_000003_create_student_vouchers_table.php
```

### Models
```
app/Models/PointRule.php       - Model untuk aturan poin
app/Models/Voucher.php          - Model untuk voucher
app/Models/StudentVoucher.php    - Model untuk voucher milik siswa
```

### Controllers
```
app/Http/Controllers/PointController.php    - Halaman poin saya & kalkulasi poin
app/Http/Controllers/VoucherController.php  - Tukar & kelola voucher
```

### Views
```
resources/views/pages/points/my-points.blade.php              - Halaman poin saya
resources/views/pages/voucher/index.blade.php                 - Katalog voucher untuk ditukar
resources/views/pages/voucher/my-vouchers.blade.php           - Voucher milikku
```

### Routes
```
GET  /my-points              - Halaman poin saya
GET  /vouchers              - Katalog voucher
POST /vouchers/redeem       - Tukar poin dengan voucher
GET  /my-vouchers           - Voucher saya
```

## 💾 Database Tables

### point_rules
```sql
- id: int
- points_late: int (default: -5)
- points_on_time: int (default: 10)
- timestamps
```

### vouchers
```sql
- id: int
- name: string
- description: text
- point_cost: int (berapa poin untuk ditukar)
- quantity: int (total voucher tersedia)
- used: int (jumlah yang sudah ditukar)
- valid_until: timestamp (tanggal kedaluwarsa)
- is_active: boolean (aktif atau tidak)
- timestamps
```

### student_vouchers
```sql
- id: int
- siswa_id: bigint (FK ke siswas)
- voucher_id: bigint (FK ke vouchers)
- redeemed_at: timestamp (kapan ditukar)
- used_at: timestamp (kapan digunakan)
- is_used: boolean
- timestamps
```

### presensis (Updated)
```sql
- ... (existing columns)
- points: int (poin yang diperoleh/dikurangi)
- used_voucher: boolean (apakah memakai voucher)
- student_voucher_id: bigint FK (voucher yang digunakan)
```

## 🔧 Konfigurasi Aturan Poin

Untuk mengubah aturan poin (saat ini: +10 tepat waktu, -5 terlambat), edit tabel `point_rules`:

```sql
UPDATE point_rules SET points_late = -3, points_on_time = 15;
```

Atau melalui Laravel Console:
```php
PointRule::first()->update(['points_late' => -3, 'points_on_time' => 15]);
```

## 📊 Cara Menggunakan (Untuk Siswa)

### 1. Lihat Poin Saya
- Klik Menu → "Poin Saya" (atau `/my-points`)
- Lihat total poin dan riwayat presensi dengan poin masing-masing

### 2. Tukar Voucher
- Klik Menu → "Tukar Voucher" (atau `/vouchers`)
- Pilih voucher yang ingin ditukar
- Klik "Tukar Sekarang"
- Voucher akan langsung masuk ke akun

### 3. Lihat Voucher Saya
- Klik Menu → "Voucher Saya" (atau `/my-vouchers`)
- Lihat voucher aktif dan riwayat penggunaan

## 🏆 Fitur Otomatis

1. **Kalkulasi Poin Otomatis**: Saat siswa melakukan presensi, sistem otomatis menghitung poin berdasarkan jam mulai jadwal
2. **Penggunaan Voucher Otomatis**: Jika siswa terlambat dan punya voucher, sistem otomatis menggunakan voucher untuk membatalkan pengurangan poin
3. **Update Poin Real-time**: Poin langsung terupdate saat presensi dicatat

## 🔐 Validasi Sistem Voucher

- Cek poin cukup (≥ point_cost voucher)
- Cek voucher tersedia (tidak habis)
- Cek voucher masih aktif
- Cek voucher belum kedaluwarsa
- Cek tidak ada duplikasi

## 📝 Hubungan Model

```
Siswa (1) → (Many) StudentVoucher
StudentVoucher (Many) → (1) Voucher
StudentVoucher (Many) → (1) Siswa
Siswa (1) → (Many) Presensi
Presensi → StudentVoucher (optional)
```

## 🐛 Troubleshooting

### Poin tidak ter-update
- Pastikan kolom `points` ada di tabel `presensis`
- Jalankan: `php artisan migrate`

### Voucher tidak muncul
- Pastikan voucher `is_active = true`
- Pastikan `quantity > used` (masih ada stok)
- Pastikan tidak kedaluwarsa

### Error saat tukar voucher
- Cek error messages di browser console
- Lihat log di `storage/logs/laravel.log`

## 🎨 Tampilan Menu Sidebar

Tambahkan menu di sidebar untuk akses cepat:
```blade
<li class="nav-item">
    <a class="nav-link" href="{{ route('points.myPoints') }}">
        <i class="fas fa-trophy"></i> Poin Saya
    </a>
</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route('vouchers.index') }}">
        <i class="fas fa-gift"></i> Tukar Voucher
    </a>
</li>
```

## ✅ Testing Checklist

- [ ] Siswa bisa melihat poin di halaman "My Points"
- [ ] Poin ter-update otomatis saat presensi
- [ ] Siswa bisa lihat katalog voucher
- [ ] Siswa bisa tukar poin dengan voucher
- [ ] Validasi poin cukup berfungsi
- [ ] Voucher otomatis digunakan saat terlambat
- [ ] Riwayat penggunaan voucher tercatat
-  [ ] Poin tidak berkurang saat memakai voucher

## 🚀 Next Steps (Optional)

1. **Admin Panel**: Tambah halaman admin untuk:
   - Buat/Edit/Delete voucher
   - Lihat statistik poin siswa
   - Edit aturan poin (points_late, points_on_time)

2. **Reward System**: Tambah hadiah untuk poin minimum tertentu

3. **Export Reports**: Export laporan poin siswa ke Excel/PDF

4. **Notification**: Kirim notifikasi saat poin mencapai target atau voucher digunakan

---

**Dibuat pada**: 15 April 2026
**Status**: ✅ Siap Digunakan
