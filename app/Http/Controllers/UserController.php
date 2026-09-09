<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Menampilkan daftar semua user yang terdaftar di aplikasi.
     */
    public function index(): View
    {
        $users = User::all();

        return view('users', compact('users'));
    }

    /**
     * Validasi input dan simpan user baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'is_admin' => ['nullable', 'boolean'],
        ]);

        $isAdmin = $request->boolean('is_admin');

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'], // Otomatis di-hash oleh cast 'hashed' pada model
            'is_admin' => $isAdmin,
            'role' => $isAdmin ? 'admin' : 'user',
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User '.$validated['name'].' berhasil ditambahkan!');
    }

    /**
     * Menghapus user berdasarkan ID.
     */
    public function destroy(int|string $id): RedirectResponse
    {
        // Proteksi agar user yang sedang aktif login tidak menghapus akunnya sendiri
        if (Auth::check() && (int) $id === (int) Auth::id()) {
            return back()->with('error', 'Aksi ditolak: Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif login.');
        }

        User::destroy($id);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus!');
    }
}
