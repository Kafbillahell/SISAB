<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->get();
        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
{
    $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|unique:users,email',
        'password' => 'required|min:6',
        'role'     => 'required|in:admin,guru,siswa',
    ], [
        'name.required'     => 'Nama lengkap wajib diisi.',
        'email.required'    => 'Email wajib diisi.',
        'email.email'       => 'Format email tidak valid (contoh: user@gmail.com).',
        'email.unique'      => 'Email sudah terdaftar.',
        'password.required' => 'Password tidak boleh kosong.',
        'password.min'      => 'Password minimal 6 karakter.',
        'role.required'     => 'Silakan pilih role.',
    ]);

    User::create([
        'name'     => $request->name,
        'email'    => $request->email,
        'password' => bcrypt($request->password),
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