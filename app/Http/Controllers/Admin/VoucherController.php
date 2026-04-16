<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    /**
     * Menampilkan daftar voucher
     */
    public function index()
    {
        $vouchers = Voucher::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.vouchers.index', compact('vouchers'));
    }

    /**
     * Menampilkan form tambah voucher
     */
    public function create()
    {
        return view('admin.vouchers.create');
    }

    /**
     * Menyimpan voucher baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'point_cost' => 'required|integer|min:1',
            'quantity' => 'required|integer|min:1',
            'usage_type' => 'required|in:anytime,after_lesson',
            'valid_minutes' => 'nullable|integer|min:1|required_if:usage_type,after_lesson',
            'valid_until' => 'nullable|date',
        ]);

        Voucher::create($validated);

        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher berhasil ditambahkan');
    }

    /**
     * Menampilkan form edit voucher
     */
    public function edit(Voucher $voucher)
    {
        return view('admin.vouchers.edit', compact('voucher'));
    }

    /**
     * Memperbarui voucher
     */
    public function update(Request $request, Voucher $voucher)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'point_cost' => 'required|integer|min:1',
            'quantity' => 'required|integer|min:1',
            'usage_type' => 'required|in:anytime,after_lesson',
            'valid_minutes' => 'nullable|integer|min:1|required_if:usage_type,after_lesson',
            'valid_until' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        $voucher->update($validated);

        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher berhasil diperbarui');
    }

    /**
     * Menghapus voucher
     */
    public function destroy(Voucher $voucher)
    {
        $voucher->delete();
        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher berhasil dihapus');
    }

    /**
     * Toggle status voucher (active/inactive)
     */
    public function toggleActive(Voucher $voucher)
    {
        $voucher->update(['is_active' => !$voucher->is_active]);
        
        return response()->json([
            'success' => true,
            'is_active' => $voucher->is_active,
            'message' => 'Status voucher berhasil diubah'
        ]);
    }
}
