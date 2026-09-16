<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F8FAFC]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>JARA — Manajemen List & Kolaborasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="h-full text-slate-800 antialiased font-sans">
    <div class="min-h-full flex flex-col">
        <!-- Top Navigation -->
        <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-md bg-blue-600 flex items-center justify-center text-white font-bold text-lg">
                            J
                        </div>
                        <div>
                            <span class="font-bold text-lg text-slate-900 tracking-tight">JARA</span>
                            <span class="text-xs text-slate-500 ml-2">Manajemen List</span>
                        </div>
                    </div>

                    <nav class="hidden md:flex items-center space-x-2">
                        <a href="{{ route('lists.index') }}" class="px-3 py-1.5 text-xs font-semibold rounded-md bg-blue-50 text-blue-700 border border-blue-200">
                            📁 Daftar List
                        </a>
                        <a href="{{ route('tasks.index') }}" class="px-3 py-1.5 text-xs font-medium rounded-md text-slate-600 hover:text-blue-600 hover:bg-slate-50 transition">
                            ✅ Semua Tugas
                        </a>
                    </nav>

                    <div class="flex items-center space-x-3">
                        <div class="text-right hidden sm:block">
                            <p class="text-xs font-semibold text-slate-800">{{ $user->name }}</p>
                            <p class="text-[10px] text-slate-500">{{ $user->email }}</p>
                        </div>
                        <div class="h-8 w-8 rounded-full bg-blue-100 text-blue-700 font-semibold text-xs flex items-center justify-center border border-blue-200">
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
                    <div class="rounded-md bg-emerald-50 border border-emerald-200 p-3 flex items-center space-x-2 text-emerald-800 text-xs shadow-xs">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <p class="font-medium">{{ session('success') }}</p>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="rounded-md bg-red-50 border border-red-200 p-3 text-red-800 text-xs shadow-xs">
                        <p class="font-bold mb-1">Terjadi kesalahan input:</p>
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Header Actions -->
                <div class="bg-white rounded-md border border-slate-300 shadow-xs p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-xl font-bold text-slate-900">Manajemen Daftar Tugas</h1>
                        <p class="text-xs text-slate-500 mt-0.5">Kelola daftar tugas pribadi Anda dan daftar tugas kolaborasi tim.</p>
                    </div>
                    <button 
                        type="button" 
                        onclick="openCreateListModal()"
                        class="inline-flex items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-xs font-semibold text-white shadow-xs hover:bg-blue-700 transition cursor-pointer shrink-0"
                    >
                        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Buat List Baru
                    </button>
                </div>

                <!-- Daftar List Milik Saya (Owner) -->
                <section class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Daftar List Milik Saya</h2>
                            <p class="text-xs text-slate-500">List di mana Anda berperan sebagai pemilik penuh (owner).</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                            {{ $ownedLists->count() }} List
                        </span>
                    </div>

                    @if ($ownedLists->isEmpty())
                        <div class="bg-white rounded-md border border-dashed border-slate-300 p-10 text-center">
                            <p class="text-xs font-semibold text-slate-700">Belum ada daftar tugas</p>
                            <p class="text-xs text-slate-400 mt-1">Klik tombol "Buat List Baru" di atas untuk membuat list pertama Anda.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                            @foreach ($ownedLists as $list)
                                @include('components.lists.card', ['list' => $list, 'isOwner' => true, 'availableUsers' => $availableUsers])
                            @endforeach
                        </div>
                    @endif
                </section>

                <!-- Daftar List Kolaborasi (Shared with Me) -->
                <section class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-base font-bold text-slate-900">List Kolaborasi</h2>
                            <p class="text-xs text-slate-500">List tugas di mana Anda diundang sebagai anggota (member).</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                            {{ $sharedLists->count() }} List
                        </span>
                    </div>

                    @if ($sharedLists->isEmpty())
                        <div class="bg-white rounded-md border border-dashed border-slate-300 p-8 text-center">
                            <p class="text-xs text-slate-400">Belum ada list yang dibagikan oleh rekan kerja kepada Anda.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                            @foreach ($sharedLists as $list)
                                @include('components.lists.card', ['list' => $list, 'isOwner' => false, 'availableUsers' => $availableUsers])
                            @endforeach
                        </div>
                    @endif
                </section>
            </div>
        </main>
    </div>

    <!-- Modals Developer 1 -->
    @include('components.lists.create-modal')
    @include('components.lists.member-modal', ['availableUsers' => $availableUsers])
    @include('components.lists.delete-dialog')
</body>
</html>
