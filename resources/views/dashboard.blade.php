@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-indigo-700 via-indigo-600 to-purple-700 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-indigo-100">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white/15 text-indigo-100 backdrop-blur-sm mb-3">
                    🚀 JARA Productivity Suite v1.0
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">Halo, {{ auth()->user()->name }}!</h1>
                <p class="text-indigo-100 text-sm mt-1 max-w-xl">
                    Pantau progres tugas pribadi dan kolaborasi tim Anda dalam satu dasbor terpadu.
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="inline-flex items-center px-3 py-1.5 rounded-xl bg-white/10 text-xs font-medium text-white border border-white/20">
                    Peran: <strong class="ml-1 uppercase">{{ auth()->user()->role }}</strong>
                </span>
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-white text-indigo-700 hover:bg-indigo-50 text-xs font-semibold rounded-xl shadow-sm transition">
                        Kelola Pengguna &rarr;
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Notice Banner for Mock/Dummy Data -->
    <div class="bg-amber-50 border border-amber-200/80 rounded-2xl p-4 text-amber-900 flex items-start space-x-3 text-sm">
        <svg class="w-5 h-5 text-amber-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div class="flex-1">
            <span class="font-semibold text-amber-950">Catatan Modul (Dummy Data Aktif):</span>
            <p class="text-xs text-amber-800 mt-0.5">
                Modul autentikasi dan RBAC sudah aktif. Komponen Todo List, Manajemen Tugas, dan Kolaborasi Tim di bawah ini menggunakan data simulasi statis (*placeholder*) sesuai spesifikasi SRS sembari menunggu migrasi tabel relasi tugas pada iterasi berikutnya.
            </p>
        </div>
    </div>

    <!-- Statistics Cards Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- Total -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Tugas</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $stats['total'] }}</h3>
            </div>
            <div class="w-11 h-11 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
        </div>

        <!-- Selesai -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Selesai</p>
                <h3 class="text-2xl font-bold text-emerald-700 mt-1">{{ $stats['completed'] }}</h3>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
        </div>

        <!-- Dalam Proses -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Dalam Proses</p>
                <h3 class="text-2xl font-bold text-blue-700 mt-1">{{ $stats['in_progress'] }}</h3>
            </div>
            <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- Tertunda -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-amber-600 uppercase tracking-wider">Tertunda</p>
                <h3 class="text-2xl font-bold text-amber-700 mt-1">{{ $stats['pending'] }}</h3>
            </div>
            <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Mock Todo List Section -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-slate-900 tracking-tight">Daftar Tugas Anda</h2>
                <p class="text-xs text-slate-500 mt-0.5">Mocking list tugas JARA Todo List</p>
            </div>
            <button type="button" onclick="alert('Fitur tambah tugas akan terhubung dengan database pada iterasi modul Tugas.')"
                class="inline-flex items-center px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm transition">
                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Buat Tugas Baru
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-200 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-6">Tugas</th>
                        <th class="py-3.5 px-6">Kategori</th>
                        <th class="py-3.5 px-6">Prioritas</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6">Tenggat Waktu</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/70 text-sm">
                    @foreach($todos as $item)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-4 px-6 font-medium text-slate-900">
                                <div class="flex items-center space-x-2.5">
                                    <input type="checkbox" {{ $item['status'] === 'Selesai' ? 'checked' : '' }}
                                        onclick="alert('Status tugas akan tersimpan secara otomatis setelah modul Tugas tersambung ke database.')"
                                        class="w-4 h-4 rounded text-indigo-600 border-slate-300 focus:ring-indigo-500 cursor-pointer">
                                    <span class="{{ $item['status'] === 'Selesai' ? 'line-through text-slate-400' : '' }}">
                                        {{ $item['title'] }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-600">
                                <span class="bg-slate-100 text-slate-700 px-2 py-1 rounded-md font-medium">
                                    {{ $item['category'] }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                @if($item['priority'] === 'Tinggi')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 ring-1 ring-rose-200">
                                        Tinggi
                                    </span>
                                @elseif($item['priority'] === 'Sedang')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 ring-1 ring-amber-200">
                                        Sedang
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                                        Rendah
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                @if($item['status'] === 'Selesai')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200">
                                        ● Selesai
                                    </span>
                                @elseif($item['status'] === 'Dalam Proses')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 ring-1 ring-blue-200">
                                        ● Dalam Proses
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 ring-1 ring-amber-200">
                                        ● Tertunda
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-500 font-mono">
                                {{ date('d M Y', strtotime($item['due_date'])) }}
                            </td>
                            <td class="py-4 px-6 text-right">
                                <button type="button" onclick="alert('Fitur aksi tugas akan dihubungkan pada iterasi modul manajemen tugas.')"
                                    class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                                    Detail
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
