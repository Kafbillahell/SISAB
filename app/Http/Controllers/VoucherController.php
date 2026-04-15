<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Voucher;
use App\Models\StudentVoucher;
use App\Models\PointRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VoucherController extends Controller
{
    /**
     * Menampilkan katalog voucher yang bisa ditukar
     */
    public function index()
    {
        $siswa = Siswa::where('user_id', Auth::id())->first();

        if (!$siswa) {
            return redirect()->route('dashboard')->with('error', 'Data siswa tidak ditemukan');
        }

        $totalPoints = $siswa->getTotalPoints();
        $vouchers = Voucher::where('is_active', true)
            ->where('quantity', '>', DB::raw('used'))
            ->orderBy('point_cost', 'asc')
            ->get();

        return view('pages.voucher.index', [
            'siswa' => $siswa,
            'totalPoints' => $totalPoints,
            'vouchers' => $vouchers,
        ]);
    }

    /**
     * Menukar poin dengan voucher
     */
    public function redeem(Request $request)
    {
        $siswa = Siswa::where('user_id', Auth::id())->first();

        if (!$siswa) {
            return response()->json(['success' => false, 'message' => 'Data siswa tidak ditemukan']);
        }

        $voucher = Voucher::findOrFail($request->voucher_id);
        $totalPoints = $siswa->getTotalPoints();

        // Validasi
        if ($totalPoints < $voucher->point_cost) {
            return response()->json([
                'success' => false,
                'message' => 'Poin Anda tidak cukup. Anda butuh ' . ($voucher->point_cost - $totalPoints) . ' poin lagi'
            ]);
        }

        if ($voucher->quantity <= $voucher->used) {
            return response()->json(['success' => false, 'message' => 'Voucher sudah habis']);
        }

        if (!$voucher->is_active) {
            return response()->json(['success' => false, 'message' => 'Voucher tidak aktif']);
        }

        if ($voucher->valid_until && now() > $voucher->valid_until) {
            return response()->json(['success' => false, 'message' => 'Voucher sudah kedaluwarsa']);
        }

        // Buat student voucher
        DB::beginTransaction();
        try {
            $studentVoucher = StudentVoucher::create([
                'siswa_id' => $siswa->id,
                'voucher_id' => $voucher->id,
                'redeemed_at' => now(),
            ]);

            // Update used count pada voucher
            $voucher->increment('used');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Voucher berhasil ditukar! Voucher akan otomatis digunakan saat Anda terlambat'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Menampilkan voucher yang dimiliki siswa
     */
    public function myVouchers()
    {
        $siswa = Siswa::where('user_id', Auth::id())->first();

        if (!$siswa) {
            return redirect()->route('dashboard')->with('error', 'Data siswa tidak ditemukan');
        }

        $totalPoints = $siswa->getTotalPoints();
        $vouchers = $siswa->studentVouchers()
            ->with('voucher')
            ->where('is_used', false)
            ->orderBy('redeemed_at', 'desc')
            ->paginate(10);

        $usedVouchers = $siswa->studentVouchers()
            ->with('voucher')
            ->where('is_used', true)
            ->orderBy('used_at', 'desc')
            ->paginate(10);

        return view('pages.voucher.my-vouchers', [
            'siswa' => $siswa,
            'totalPoints' => $totalPoints,
            'vouchers' => $vouchers,
            'usedVouchers' => $usedVouchers,
        ]);
    }
}
