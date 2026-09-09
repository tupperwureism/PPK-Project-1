@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Kelola Pengguna (Programmer 1)</h1>
            <p class="text-sm text-slate-500 mt-1">Modul manajemen user: form tambah pengguna baru dan tabel daftar seluruh pengguna terdaftar.</p>
        </div>
        <div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200">
                ● Branch: feat/user-admin
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        <!-- Form Tambah User Baru -->
        <div class="lg:col-span-1 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden sticky top-24">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <h2 class="text-base font-bold text-slate-900">Tambah User Baru</h2>
                <p class="text-xs text-slate-500 mt-0.5">Input data untuk membuat akun baru.</p>
            </div>

            <form action="{{ route('users.store') }}" method="POST" class="p-6 space-y-4">
                @csrf

                <!-- Nama -->
                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nama Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="w-full px-3.5 py-2 text-sm rounded-xl border {{ $errors->has('name') ? 'border-rose-400 focus:ring-rose-100' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-100' }} focus:outline-none focus:ring-4 transition"
                        placeholder="Contoh: Budi Prakoso">
                    @error('name')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Alamat Email <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                        class="w-full px-3.5 py-2 text-sm rounded-xl border {{ $errors->has('email') ? 'border-rose-400 focus:ring-rose-100' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-100' }} focus:outline-none focus:ring-4 transition"
                        placeholder="nama@email.com">
                    @error('email')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kata Sandi <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" name="password" id="password" required
                        class="w-full px-3.5 py-2 text-sm rounded-xl border {{ $errors->has('password') ? 'border-rose-400 focus:ring-rose-100' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-100' }} focus:outline-none focus:ring-4 transition"
                        placeholder="Minimal 8 karakter">
                    @error('password')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status is_admin -->
                <div class="pt-2">
                    <label class="flex items-center space-x-2.5 cursor-pointer">
                        <input type="checkbox" name="is_admin" value="1" {{ old('is_admin') ? 'checked' : '' }}
                            class="w-4 h-4 rounded text-indigo-600 border-slate-300 focus:ring-indigo-500">
                        <span class="text-xs font-medium text-slate-700">Tetapkan sebagai Admin (is_admin)</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                        Simpan User
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabel Daftar User -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Daftar Pengguna Terdaftar</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Total pengguna: {{ count($users) }} orang</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/75 border-b border-slate-200 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                            <th class="py-3 px-5">ID</th>
                            <th class="py-3 px-5">Nama</th>
                            <th class="py-3 px-5">Email</th>
                            <th class="py-3 px-5">Status Admin</th>
                            <th class="py-3 px-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200/70 text-sm">
                        @forelse($users as $user)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3.5 px-5 font-mono text-xs text-slate-500">
                                    #{{ $user->id }}
                                </td>
                                <td class="py-3.5 px-5 font-semibold text-slate-900">
                                    {{ $user->name }}
                                    @if(auth()->check() && $user->id === auth()->id())
                                        <span class="ml-1 text-[10px] text-emerald-600 font-medium bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">Anda</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-5 text-slate-600 text-xs">
                                    {{ $user->email }}
                                </td>
                                <td class="py-3.5 px-5">
                                    @if($user->is_admin || $user->role === 'admin')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                            Admin (Ya)
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                                            User Biasa
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-5 text-right">
                                    @if(auth()->check() && $user->id === auth()->id())
                                        <span class="text-xs text-slate-400 italic">Aktif</span>
                                    @else
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user {{ $user->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1 text-xs font-medium text-rose-600 border border-rose-200 hover:bg-rose-50 rounded-lg transition">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-slate-400 text-sm">
                                    Belum ada pengguna terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
