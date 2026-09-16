@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Manajemen Akun Pengguna</h1>
            <p class="text-sm text-slate-600 mt-1">Kelola data seluruh akun pengguna, hak akses peran (Role-Based Access Control), dan pendaftaran user baru.</p>
        </div>
        <div>
            <button 
                type="button" 
                onclick="openAddUserModal()" 
                class="inline-flex items-center px-4 py-2 bg-[#2563EB] hover:bg-blue-700 active:bg-blue-800 text-white text-sm font-semibold rounded-md shadow-xs transition-colors cursor-pointer"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Pengguna
            </button>
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
        <div class="p-4 rounded-md bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-md bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-md bg-rose-50 border border-rose-200 text-rose-800 text-sm">
            <div class="font-semibold mb-1">Gagal memproses formulir:</div>
            <ul class="list-disc list-inside text-xs space-y-1 text-rose-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Search & Filter Card -->
    <div class="bg-white p-4 rounded-md border border-[#CBD5E1] shadow-xs">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search ?? '' }}" 
                    placeholder="Cari berdasarkan nama atau email pengguna..."
                    class="w-full pl-9 pr-3 py-2 text-sm rounded-md border border-[#CBD5E1] bg-white text-slate-900 focus:outline-none focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] transition-colors"
                >
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-[#475569] hover:bg-slate-700 text-white text-sm font-medium rounded-md transition-colors cursor-pointer">
                    Cari
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-md border border-[#CBD5E1] transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- AdminUserTable Card -->
    <div class="bg-white rounded-md border border-[#CBD5E1] shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-100 border-b border-[#CBD5E1] text-xs font-semibold text-[#475569] uppercase tracking-wider">
                        <th class="py-3 px-5">ID</th>
                        <th class="py-3 px-5">Nama Pengguna</th>
                        <th class="py-3 px-5">Email</th>
                        <th class="py-3 px-5">Peran (Role)</th>
                        <th class="py-3 px-5">Terdaftar Sejak</th>
                        <th class="py-3 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-sm">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3.5 px-5 font-mono text-xs text-slate-500">
                                #{{ $user->id }}
                            </td>
                            <td class="py-3.5 px-5 font-medium text-slate-900">
                                <div class="flex items-center space-x-2">
                                    <span>{{ $user->name }}</span>
                                    @if($user->id === auth()->id())
                                        <span class="text-[11px] font-semibold text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200">
                                            Akun Anda
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5 px-5 text-slate-600">
                                {{ $user->email }}
                            </td>
                            <td class="py-3.5 px-5">
                                @if($user->role === 'admin' || $user->is_admin)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-800 border border-slate-300">
                                        Admin
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                        User
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5 text-slate-500 text-xs">
                                {{ $user->created_at ? $user->created_at->translatedFormat('d M Y, H:i') : '-' }}
                            </td>
                            <td class="py-3.5 px-5 text-right">
                                @if($user->id === auth()->id())
                                    <span class="text-xs text-slate-400 italic">
                                        Sedang Aktif
                                    </span>
                                @else
                                    <button 
                                        type="button" 
                                        onclick="openDeleteModal({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                        class="inline-flex items-center px-2.5 py-1.5 border border-red-300 text-red-700 hover:bg-red-50 rounded-md text-xs font-medium transition-colors cursor-pointer"
                                        title="Hapus Pengguna"
                                    >
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Hapus
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-500 text-sm">
                                Tidak ada data pengguna yang sesuai dengan kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="px-5 py-3.5 bg-slate-50 border-t border-[#CBD5E1]">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

<!-- ================= AddUserModal ================= -->
<div id="addUserModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-md border border-[#CBD5E1] shadow-xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <div class="px-5 py-4 border-b border-[#CBD5E1] flex items-center justify-between bg-slate-50">
            <div>
                <h3 class="text-base font-bold text-slate-900">Tambah Akun Pengguna Baru</h3>
                <p class="text-xs text-slate-500 mt-0.5">Daftarkan akun pengguna atau admin baru ke dalam sistem.</p>
            </div>
            <button type="button" onclick="closeAddUserModal()" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form action="{{ route('admin.users.store') }}" method="POST" class="p-5 space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-xs font-semibold text-[#475569] uppercase mb-1">Nama / Username</label>
                <input 
                    type="text" 
                    name="name" 
                    id="name" 
                    required 
                    placeholder="Contoh: budi_dev atau Budi Santoso"
                    value="{{ old('name') }}"
                    class="w-full px-3 py-2 text-sm rounded-md border border-[#CBD5E1] bg-white text-slate-900 focus:outline-none focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                >
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold text-[#475569] uppercase mb-1">Alamat Email</label>
                <input 
                    type="email" 
                    name="email" 
                    id="email" 
                    required 
                    placeholder="budi@jara.app"
                    value="{{ old('email') }}"
                    class="w-full px-3 py-2 text-sm rounded-md border border-[#CBD5E1] bg-white text-slate-900 focus:outline-none focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                >
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="password" class="block text-xs font-semibold text-[#475569] uppercase mb-1">Password</label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        required 
                        minlength="8"
                        placeholder="Min. 8 karakter"
                        class="w-full px-3 py-2 text-sm rounded-md border border-[#CBD5E1] bg-white text-slate-900 focus:outline-none focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                    >
                </div>
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-[#475569] uppercase mb-1">Konfirmasi Password</label>
                    <input 
                        type="password" 
                        name="password_confirmation" 
                        id="password_confirmation" 
                        required 
                        minlength="8"
                        placeholder="Ulangi password"
                        class="w-full px-3 py-2 text-sm rounded-md border border-[#CBD5E1] bg-white text-slate-900 focus:outline-none focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                    >
                </div>
            </div>

            <div>
                <label for="role" class="block text-xs font-semibold text-[#475569] uppercase mb-1">Peran (Role)</label>
                <select 
                    name="role" 
                    id="role" 
                    required 
                    class="w-full px-3 py-2 text-sm rounded-md border border-[#CBD5E1] bg-white text-slate-900 focus:outline-none focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                >
                    <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>User (Pengguna Biasa / Owner / Member)</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin (Pengelola Sistem)</option>
                </select>
            </div>

            <div class="pt-3 border-t border-[#CBD5E1] flex justify-end gap-2">
                <button 
                    type="button" 
                    onclick="closeAddUserModal()" 
                    class="px-4 py-2 border border-[#CBD5E1] text-slate-700 hover:bg-slate-100 rounded-md text-sm font-medium transition-colors cursor-pointer"
                >
                    Batal
                </button>
                <button 
                    type="submit" 
                    class="px-4 py-2 bg-[#2563EB] hover:bg-blue-700 text-white rounded-md text-sm font-semibold transition-colors cursor-pointer"
                >
                    Simpan Pengguna
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= DeleteUserConfirmModal ================= -->
<div id="deleteUserModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-md border border-[#CBD5E1] shadow-xl w-full max-w-md overflow-hidden">
        <div class="p-5">
            <div class="flex items-start space-x-3">
                <div class="w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center shrink-0 border border-red-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Konfirmasi Hapus Akun</h3>
                    <p class="text-sm text-slate-600 mt-1">
                        Apakah Anda yakin ingin menghapus akun pengguna <strong id="deleteUserName" class="text-slate-900 font-semibold"></strong>?
                    </p>
                    <p class="text-xs text-red-600 mt-2">
                        Perhatian: Seluruh to-do list dan data terkait akun ini akan ikut terhapus secara permanen.
                    </p>
                </div>
            </div>
        </div>

        <form id="deleteUserForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="px-5 py-3.5 bg-slate-50 border-t border-[#CBD5E1] flex justify-end gap-2">
                <button 
                    type="button" 
                    onclick="closeDeleteModal()" 
                    class="px-4 py-2 border border-[#CBD5E1] text-slate-700 hover:bg-slate-100 rounded-md text-sm font-medium transition-colors cursor-pointer"
                >
                    Batal
                </button>
                <button 
                    type="submit" 
                    class="px-4 py-2 bg-[#DC2626] hover:bg-red-700 text-white rounded-md text-sm font-semibold transition-colors cursor-pointer"
                >
                    Hapus Pengguna
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddUserModal() {
        document.getElementById('addUserModal').classList.remove('hidden');
    }

    function closeAddUserModal() {
        document.getElementById('addUserModal').classList.add('hidden');
    }

    function openDeleteModal(userId, userName) {
        document.getElementById('deleteUserName').textContent = userName;
        document.getElementById('deleteUserForm').action = "{{ url('/admin/users') }}/" + userId;
        document.getElementById('deleteUserModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteUserModal').classList.add('hidden');
    }

    // Menutup modal jika pengguna menekan tombol ESC
    window.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeAddUserModal();
            closeDeleteModal();
        }
    });

    // Otomatis buka modal jika ada error validasi saat submit form tambah user
    @if($errors->any())
        openAddUserModal();
    @endif
</script>
@endsection
