@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Breadcrumb / Back Button -->
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-slate-800 transition">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Manajemen Pengguna
        </a>
    </div>

    <!-- Card Form -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200">
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Tambah Pengguna Baru</h1>
            <p class="text-xs text-slate-500 mt-1">Daftarkan akun admin atau pengguna biasa baru ke sistem JARA.</p>
        </div>

        <form action="{{ route('admin.users.store') }}" method="POST" class="p-6 space-y-5">
            @csrf

            <!-- Name Input -->
            <div>
                <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                    Nama Lengkap <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('name') ? 'border-rose-400 focus:ring-rose-100' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-100' }} text-slate-900 text-sm focus:outline-none focus:ring-4 transition placeholder-slate-400"
                    placeholder="Contoh: Reynaldi Hutagaol">
                @error('name')
                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email Input -->
            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                    Alamat Email <span class="text-rose-500">*</span>
                </label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                    class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('email') ? 'border-rose-400 focus:ring-rose-100' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-100' }} text-slate-900 text-sm focus:outline-none focus:ring-4 transition placeholder-slate-400"
                    placeholder="nama@jara.test">
                @error('email')
                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Role Selection -->
            <div>
                <label for="role" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                    Peran Pengguna (RBAC) <span class="text-rose-500">*</span>
                </label>
                <select name="role" id="role" required
                    class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('role') ? 'border-rose-400 focus:ring-rose-100' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-100' }} text-slate-900 text-sm focus:outline-none focus:ring-4 transition bg-white">
                    <option value="user" {{ old('role', 'user') === 'user' ? 'selected' : '' }}>User (Pengguna Biasa / Anggota Tim)</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin (Administrator Sistem Penuh)</option>
                </select>
                @error('role')
                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
                <p class="mt-1.5 text-xs text-slate-500">Admin memiliki akses penuh ke manajemen pengguna dan seluruh data sistem.</p>
            </div>

            <!-- Password Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <!-- Password -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Kata Sandi <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" name="password" id="password" required
                        class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('password') ? 'border-rose-400 focus:ring-rose-100' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-100' }} text-slate-900 text-sm focus:outline-none focus:ring-4 transition placeholder-slate-400"
                        placeholder="Minimal 8 karakter">
                    @error('password')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Confirmation -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Konfirmasi Kata Sandi <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-indigo-100 text-slate-900 text-sm focus:outline-none focus:ring-4 transition placeholder-slate-400"
                        placeholder="Ulangi kata sandi">
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 flex items-center justify-end space-x-3 border-t border-slate-100">
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2.5 border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-medium rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                    Simpan Pengguna
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
