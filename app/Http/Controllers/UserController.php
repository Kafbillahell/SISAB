<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
{
    // Ambil filter role dari URL, default-nya 'admin'
    $role = $request->query('role', 'admin');

    // Validasi role agar aman
    if (!in_array($role, ['admin', 'guru', 'siswa'])) {
        $role = 'admin';
    }

    // PENTING: Batasi data per halaman (misal 20)
    $users = User::where('role', $role)
                ->latest()
                ->paginate(20)
                ->withQueryString(); 

    return view('admin.users.index', compact('users', 'role'));
}

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'nullable|min:6',
            'role'     => 'required|in:admin,guru,siswa',
        ], [
            'name.required'     => 'Nama lengkap wajib diisi.',
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid (contoh: user@gmail.com).',
            'email.unique'      => 'Email sudah terdaftar.',
            'password.min'      => 'Password minimal 6 karakter.',
            'role.required'     => 'Silakan pilih role.',
        ]);

        // Jika password kosong dan role guru, gunakan default 'gurusmk123'
        $passwordPlain = $request->password;
        if (empty($passwordPlain)) {
            if ($request->role === 'guru') {
                $passwordPlain = 'gurusmk123';
            } else {
                return back()->withErrors(['password' => 'Password tidak boleh kosong untuk role ini.'])->withInput();
            }
        }

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($passwordPlain),
            'role'     => $request->role,
        ]);

        return back()->with('success', 'User berhasil dibuat!');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|in:admin,guru,siswa',
            'password' => 'nullable|min:6',
        ], [
            'name.required' => 'Nama tidak boleh kosong.',
            'password.min'  => 'Password baru minimal 6 karakter.',
        ]);

        $user->name = $request->name;
        $user->role = $request->role;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return back()->with('success', 'User berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return back()->with('success', 'User berhasil dihapus!');
    }
}