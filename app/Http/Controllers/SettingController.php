<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    // Menampilkan halaman pengaturan umum sistem (seperti titik koordinat GPS sekolah dan radius)
    public function index()
    {
        $lokasi = Setting::getValue('lokasi_kantor', '-6.9202,107.6186'); // Default Bandung if empty
        $radius = Setting::getValue('radius_absen', '100');
        
        return view('settings.index', compact('lokasi', 'radius'));
    }

    // Memproses form pembaruan pengaturan sistem dan menyimpannya di basis data
    public function update(Request $request)
    {
        $request->validate([
            'lat' => 'required',
            'lng' => 'required',
            'radius' => 'required|numeric'
        ]);

        Setting::updateOrCreate(
            ['key' => 'lokasi_kantor'],
            ['value' => $request->lat . ',' . $request->lng]
        );

        Setting::updateOrCreate(
            ['key' => 'radius_absen'],
            ['value' => $request->radius]
        );

        return redirect()->back()->with('success', 'Pengaturan lokasi berhasil diperbarui.');
    }
}
