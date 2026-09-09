<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>JARA — Manajemen List & Kolaborasi</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full text-slate-800 antialiased font-sans">
    <div class="min-h-full flex flex-col">
        <!-- Top Navigation -->
        <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center text-white font-bold text-xl shadow-md shadow-indigo-100">
                            J
                        </div>
                        <div>
                            <span class="font-bold text-xl text-slate-900 tracking-tight">JARA</span>
                            <span class="text-xs text-indigo-600 font-semibold uppercase tracking-wider block sm:inline sm:ml-1 sm:border-l sm:border-slate-300 sm:pl-2">Modul 2 • List & Kolaborasi</span>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-semibold text-slate-800">{{ $user->name }}</p>
                            <p class="text-xs text-slate-500">{{ $user->email }}</p>
                        </div>
                        <div class="h-9 w-9 rounded-full bg-indigo-100 text-indigo-700 font-semibold flex items-center justify-center border border-indigo-200">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
                <!-- Notifications -->
                @if (session('success'))
                    <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 flex items-center space-x-3 text-emerald-800 shadow-xs">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <p class="text-sm font-medium">{{ session('success') }}</p>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="rounded-xl bg-rose-50 border border-rose-200 p-4 text-rose-800 shadow-xs">
                        <div class="flex items-center space-x-2 font-medium text-sm mb-1">
                            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                            </svg>
                            <span>Terjadi kesalahan pada data yang Anda kirim:</span>
                        </div>
                        <ul class="list-disc list-inside text-xs space-y-1 ml-6 text-rose-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- FR-05: Buat List Baru -->
                <section class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6">
                    <div class="max-w-2xl">
                        <h2 class="text-lg font-bold text-slate-900">Buat Daftar Tugas Baru</h2>
                        <p class="text-sm text-slate-500 mt-0.5">Kelompokkan tugas ke dalam kategori tertentu seperti Tugas Kuliah atau Projek Tim.</p>
                        
                        <form action="{{ route('lists.store') }}" method="POST" class="mt-4 flex flex-col sm:flex-row gap-3">
                            @csrf
                            <div class="relative flex-1">
                                <input 
                                    type="text" 
                                    name="name" 
                                    id="name" 
                                    required 
                                    placeholder="Contoh: Tugas Kuliah, Projek Tim, Skripsi..." 
                                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                >
                            </div>
                            <button 
                                type="submit" 
                                class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors cursor-pointer"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                Tambah List
                            </button>
                        </form>
                    </div>
                </section>

                <!-- FR-06: Daftar List Milik Pengguna -->
                <section class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-slate-900">Daftar List Milik Saya</h2>
                            <p class="text-sm text-slate-500">List tugas di mana Anda berperan sebagai pemilik penuh (owner).</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                            {{ $ownedLists->count() }} List
                        </span>
                    </div>

                    @if ($ownedLists->isEmpty())
                        <div class="bg-white rounded-2xl border border-dashed border-slate-300 p-12 text-center">
                            <div class="w-12 h-12 rounded-full bg-slate-100 mx-auto flex items-center justify-center text-slate-400 mb-3">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm0 5.25h.007v.008H3.75V12Zm0 5.25h.007v.008H3.75v-.008Z" />
                                </svg>
                            </div>
                            <h3 class="text-sm font-semibold text-slate-800">Belum ada list tugas</h3>
                            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Silakan buat daftar tugas pertama Anda menggunakan form di atas untuk mulai mengatur pekerjaan.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                            @foreach ($ownedLists as $list)
                                <div class="bg-white rounded-2xl border border-slate-200 shadow-xs hover:shadow-md transition-shadow p-5 flex flex-col justify-between">
                                    <div>
                                        <!-- Header Card List -->
                                        <div class="flex items-start justify-between gap-2">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center space-x-2">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                                        👑 Pemilik
                                                    </span>
                                                    <span class="text-xs text-slate-400">
                                                        {{ $list->created_at->translatedFormat('d M Y') }}
                                                    </span>
                                                </div>
                                                <h3 class="text-lg font-bold text-slate-900 mt-2 truncate" title="{{ $list->name }}">
                                                    {{ $list->name }}
                                                </h3>
                                            </div>

                                            <!-- FR-07: Hapus List Milik Sendiri -->
                                            <form action="{{ route('lists.destroy', $list) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus list &quot;{{ $list->name }}&quot;? Seluruh data tugas di dalamnya akan ikut terhapus secara permanen.');">
                                                @csrf
                                                @method('DELETE')
                                                <button 
                                                    type="submit" 
                                                    class="text-slate-400 hover:text-rose-600 p-1.5 rounded-lg hover:bg-rose-50 transition-colors cursor-pointer" 
                                                    title="Hapus List Tugas"
                                                >
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>

                                        <!-- FR-08: Anggota Kolaborasi -->
                                        <div class="mt-4 pt-4 border-t border-slate-100">
                                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Anggota Kolaborasi</p>
                                            
                                            <div class="flex flex-wrap items-center gap-1.5 mb-3">
                                                @forelse ($list->members as $member)
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                                        {{ $member->name }}
                                                    </span>
                                                @empty
                                                    <span class="text-xs text-slate-400 italic">Belum ada anggota lain ditambahkan</span>
                                                @endforelse
                                            </div>

                                            <!-- Form Tambah Anggota (FR-08) -->
                                            @php
                                                $nonMembers = $availableUsers->filter(fn($u) => ! $list->members->contains('id', $u->id));
                                            @endphp

                                            @if ($nonMembers->isNotEmpty())
                                                <form action="{{ route('lists.add-member', $list) }}" method="POST" class="flex gap-2">
                                                    @csrf
                                                    <select 
                                                        name="user_id" 
                                                        required 
                                                        class="text-xs rounded-lg border border-slate-300 py-1.5 px-2.5 bg-white text-slate-700 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 flex-1"
                                                    >
                                                        <option value="" disabled selected>+ Pilih teman untuk kolaborasi...</option>
                                                        @foreach ($nonMembers as $availableUser)
                                                            <option value="{{ $availableUser->id }}">
                                                                {{ $availableUser->name }} ({{ $availableUser->email }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <button 
                                                        type="submit" 
                                                        class="text-xs font-medium bg-slate-900 text-white px-3 py-1.5 rounded-lg hover:bg-slate-800 transition-colors shrink-0 cursor-pointer"
                                                    >
                                                        Tambah
                                                    </button>
                                                </form>
                                            @else
                                                <p class="text-xs text-slate-400">Seluruh pengguna sudah terdaftar di list ini.</p>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Placeholder modul tugas Programmer 3 -->
                                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                                        <span class="inline-flex items-center text-slate-400">
                                            <svg class="w-4 h-4 mr-1 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                                            </svg>
                                            Modul Tugas (Programmer 3)
                                        </span>
                                        <span class="text-indigo-600 font-medium hover:underline cursor-pointer">
                                            Buka Tugas &rarr;
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>

                <!-- List Kolaborasi (Shared with Me) -->
                <section class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-slate-900">List Dibagikan ke Saya</h2>
                            <p class="text-sm text-slate-500">List tugas di mana Anda diundang sebagai anggota kolaborasi oleh pengguna lain.</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-violet-50 text-violet-700 border border-violet-200">
                            {{ $sharedLists->count() }} List
                        </span>
                    </div>

                    @if ($sharedLists->isEmpty())
                        <div class="bg-white rounded-2xl border border-dashed border-slate-300 p-8 text-center">
                            <p class="text-xs text-slate-400 italic">Belum ada list tugas yang dibagikan kepada Anda oleh teman lain.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                            @foreach ($sharedLists as $list)
                                <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5 flex flex-col justify-between">
                                    <div>
                                        <div class="flex items-center space-x-2">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-violet-50 text-violet-700 border border-violet-200">
                                                🤝 Kolaborator
                                            </span>
                                            <span class="text-xs text-slate-400">
                                                Pemilik: <strong class="text-slate-600">{{ $list->user->name }}</strong>
                                            </span>
                                        </div>
                                        <h3 class="text-lg font-bold text-slate-900 mt-2 truncate" title="{{ $list->name }}">
                                            {{ $list->name }}
                                        </h3>

                                        <div class="mt-4 pt-3 border-t border-slate-100">
                                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Anggota Tim</p>
                                            <div class="flex flex-wrap items-center gap-1.5">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                                    👑 {{ $list->user->name }}
                                                </span>
                                                @foreach ($list->members as $member)
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                                        {{ $member->name }} {{ $member->id === $user->id ? '(Saya)' : '' }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                                        <span class="text-slate-400">Akses: Anggota Tim</span>
                                        <span class="text-indigo-600 font-medium hover:underline cursor-pointer">
                                            Buka Tugas &rarr;
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-slate-200 py-6 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-xs text-slate-400">
                &copy; {{ date('Y') }} JARA (To-Do List Pribadi & Tim) — Praktikum PPK Pertemuan 2
            </div>
        </footer>
    </div>
</body>
</html>
