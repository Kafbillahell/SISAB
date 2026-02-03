<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;

class GuruController extends Controller
{
    public function index()
    {
        $gurus = Guru::with('user')->get();
        return view('guru.index', compact('gurus'));
    }

    public function create()
    {
        $users = User::where('role', 'guru')
                    ->whereDoesntHave('guru')
                    ->get();
                    
        return view('guru.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'nip' => 'nullable|unique:gurus,nip',
            'nama_guru' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
        ]);

        Guru::create($request->all());
        return redirect()->route('guru.index')->with('success', 'Data Guru berhasil ditambahkan.');
    }

    public function edit(Guru $guru)
    {
        $users = User::all();
        return view('guru.edit', compact('guru', 'users'));
    }

    public function update(Request $request, Guru $guru)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'nip' => 'nullable|unique:gurus,nip,' . $guru->id,
            'nama_guru' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
        ]);

        $guru->update($request->all());
        return redirect()->route('guru.index')->with('success', 'Data Guru berhasil diperbarui.');
    }

    public function destroy(Guru $guru)
    {
        $guru->delete();
        return redirect()->route('guru.index')->with('success', 'Data Guru berhasil dihapus.');
    }
}