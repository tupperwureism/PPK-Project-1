@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Navigation Back & Quick Actions -->
    <div class="flex items-center justify-between">
        <a href="{{ route('lists.index') }}" class="inline-flex items-center text-sm font-medium text-slate-600 hover:text-[#2563EB] transition-colors">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Daftar List
        </a>

        @if($isOwner)
            <form action="{{ route('lists.destroy', $todoList) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus list ini? Seluruh data tugas di dalamnya akan ikut terhapus permanen.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-red-300 text-red-700 hover:bg-red-50 rounded-md text-xs font-medium transition-colors cursor-pointer">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Hapus List
                </button>
            </form>
        @endif
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

    <!-- ================= List Board Header (Developer 3) ================= -->
    <div class="bg-white p-6 rounded-md border border-[#CBD5E1] shadow-xs space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    @if($isOwner)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                            👑 Pemilik List
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-50 text-blue-800 border border-blue-200">
                            🤝 Anggota Kolaborasi
                        </span>
                    @endif
                    <span class="text-xs text-slate-400 font-mono">
                        Dibuat oleh: <strong class="text-slate-700 font-medium">{{ $todoList->user->name }}</strong> • {{ $todoList->created_at->translatedFormat('d M Y') }}
                    </span>
                </div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $todoList->name }}</h1>
            </div>

            <!-- Anggota Tim Compact -->
            <div class="flex items-center gap-2 text-xs text-slate-600">
                <span class="font-semibold uppercase text-slate-500">Kolaborator:</span>
                <div class="flex items-center -space-x-1.5 overflow-hidden">
                    <div class="w-7 h-7 rounded-full bg-amber-100 text-amber-800 flex items-center justify-center font-bold border-2 border-white text-[10px]" title="Pemilik: {{ $todoList->user->name }}">
                        {{ substr($todoList->user->name, 0, 1) }}
                    </div>
                    @foreach($todoList->members as $member)
                        <div class="w-7 h-7 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center font-semibold border-2 border-white text-[10px]" title="{{ $member->name }}">
                            {{ substr($member->name, 0, 1) }}
                        </div>
                    @endforeach
                </div>
                <span class="text-slate-500 text-xs font-medium">({{ $todoList->members->count() + 1 }} orang)</span>
            </div>
        </div>

        <!-- REQ-06: ProgressBarTracker Component -->
        <x-progress-bar-tracker 
            :totalTasks="$totalTasks" 
            :completedTasks="$completedTasks" 
            :progressPercentage="$progressPercentage" 
        />
    </div>

    <!-- Main Workspace Layout: Task Board & Sidebar -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Task List Area (2 Cols) -->
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white p-5 rounded-md border border-[#CBD5E1] shadow-xs">
                <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-200">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Daftar Tugas dalam List Ini</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Kelola dan tandai tugas yang sudah terselesaikan.</p>
                    </div>
                    <span class="text-xs font-semibold px-2 py-1 rounded bg-slate-100 text-slate-700 border border-slate-200">
                        {{ $totalTasks }} Tugas
                    </span>
                </div>

                <!-- Form Tambah Tugas Baru -->
                <form action="{{ route('tasks.store') }}" method="POST" class="mb-6 p-4 bg-slate-50 rounded-md border border-[#CBD5E1] space-y-3">
                    @csrf
                    <input type="hidden" name="todo_list_id" value="{{ $todoList->id }}">
                    <div class="text-xs font-semibold text-[#475569] uppercase">Tambah Tugas Baru</div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                        <div class="sm:col-span-6">
                            <input 
                                type="text" 
                                name="title" 
                                required 
                                placeholder="Judul tugas yang harus dikerjakan..." 
                                class="w-full px-3 py-2 text-sm rounded-md border border-[#CBD5E1] bg-white text-slate-900 focus:outline-none focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                            >
                        </div>
                        <div class="sm:col-span-3">
                            <select 
                                name="priority" 
                                required 
                                class="w-full px-2.5 py-2 text-sm rounded-md border border-[#CBD5E1] bg-white text-slate-900 focus:outline-none focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                            >
                                <option value="sedang">Prioritas: Sedang</option>
                                <option value="tinggi">Prioritas: Tinggi</option>
                                <option value="rendah">Prioritas: Rendah</option>
                            </select>
                        </div>
                        <div class="sm:col-span-3">
                            <input 
                                type="date" 
                                name="due_date" 
                                required 
                                value="{{ date('Y-m-d') }}"
                                class="w-full px-2.5 py-2 text-sm rounded-md border border-[#CBD5E1] bg-white text-slate-900 focus:outline-none focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                            >
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button 
                            type="submit" 
                            class="px-4 py-1.5 bg-[#2563EB] hover:bg-blue-700 text-white rounded-md text-xs font-semibold transition-colors cursor-pointer"
                        >
                            + Simpan Tugas
                        </button>
                    </div>
                </form>

                <!-- Daftar Tugas -->
                @if($todoList->tasks->isEmpty())
                    <div class="py-10 text-center text-slate-400">
                        <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        <p class="text-sm font-medium text-slate-700">Belum ada tugas di dalam list ini.</p>
                        <p class="text-xs text-slate-500 mt-0.5">Gunakan formulir di atas untuk menambahkan tugas pertama.</p>
                    </div>
                @else
                    <div class="divide-y divide-slate-200">
                        @foreach($todoList->tasks as $task)
                            <div class="py-3.5 flex items-start justify-between gap-3 hover:bg-slate-50/75 px-2 rounded-md transition-colors">
                                <div class="flex items-start gap-3 flex-1 min-w-0">
                                    <!-- Toggle Complete Form -->
                                    <form action="{{ route('tasks.toggle', $task) }}" method="POST" class="mt-0.5">
                                        @csrf
                                        @method('PATCH')
                                        <button 
                                            type="submit" 
                                            class="w-5 h-5 rounded border {{ $task->is_completed ? 'bg-emerald-600 border-emerald-600 text-white' : 'border-slate-400 hover:border-[#2563EB] bg-white' }} flex items-center justify-center transition-colors cursor-pointer"
                                            title="{{ $task->is_completed ? 'Tandai belum selesai' : 'Tandai selesai' }}"
                                        >
                                            @if($task->is_completed)
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                                </svg>
                                            @endif
                                        </button>
                                    </form>

                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-medium {{ $task->is_completed ? 'line-through text-slate-400' : 'text-slate-900' }} truncate">
                                            {{ $task->title }}
                                        </div>
                                        <div class="flex items-center gap-2 mt-1 text-xs text-slate-500">
                                            <span>Tenggat: <strong class="text-slate-700">{{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d M Y') : '-' }}</strong></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <!-- Priority Badge -->
                                    @if(strtolower($task->priority) === 'tinggi')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                            Tinggi
                                        </span>
                                    @elseif(strtolower($task->priority) === 'sedang')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            Sedang
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                            Rendah
                                        </span>
                                    @endif

                                    <!-- Delete Task -->
                                    <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Hapus tugas ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button 
                                            type="submit" 
                                            class="text-slate-400 hover:text-red-600 p-1 rounded hover:bg-red-50 transition-colors cursor-pointer"
                                            title="Hapus tugas"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar: Collaborator & Members (1 Col) -->
        <div class="space-y-4">
            <div class="bg-white p-5 rounded-md border border-[#CBD5E1] shadow-xs space-y-4">
                <div class="pb-2 border-b border-slate-200">
                    <h3 class="text-sm font-bold text-slate-900">Manajemen Anggota Kolaborasi</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Daftar pengguna yang memiliki akses ke list ini.</p>
                </div>

                <div class="space-y-2.5">
                    <!-- Owner Row -->
                    <div class="flex items-center justify-between p-2 rounded bg-amber-50/60 border border-amber-200 text-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-amber-200 text-amber-900 flex items-center justify-center font-bold text-[10px]">
                                👑
                            </span>
                            <div>
                                <div class="font-semibold text-slate-900">{{ $todoList->user->name }}</div>
                                <div class="text-slate-500 text-[10px]">{{ $todoList->user->email }}</div>
                            </div>
                        </div>
                        <span class="font-semibold text-amber-800 text-[10px] uppercase">Pemilik</span>
                    </div>

                    <!-- Members Rows -->
                    @forelse($todoList->members as $member)
                        <div class="flex items-center justify-between p-2 rounded bg-slate-50 border border-[#CBD5E1] text-xs">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center font-semibold text-[10px]">
                                    {{ substr($member->name, 0, 1) }}
                                </span>
                                <div>
                                    <div class="font-semibold text-slate-900">{{ $member->name }}</div>
                                    <div class="text-slate-500 text-[10px]">{{ $member->email }}</div>
                                </div>
                            </div>
                            <span class="text-slate-600 text-[10px] font-medium">Anggota</span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic py-1">Belum ada anggota kolaborasi lain yang ditambahkan.</p>
                    @endforelse
                </div>

                <!-- Form Tambah Kolaborator (Hanya Pemilik) -->
                @if($isOwner)
                    <div class="pt-3 border-t border-slate-200">
                        <div class="text-xs font-semibold text-[#475569] uppercase mb-2">Tambah Teman ke List</div>
                        @if($availableUsers->isNotEmpty())
                            <form action="{{ route('lists.add-member', $todoList) }}" method="POST" class="space-y-2">
                                @csrf
                                <select 
                                    name="user_id" 
                                    required 
                                    class="w-full text-xs rounded-md border border-[#CBD5E1] py-2 px-2.5 bg-white text-slate-900 focus:outline-none focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                                >
                                    <option value="" disabled selected>Pilih pengguna untuk diundang...</option>
                                    @foreach($availableUsers as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                                    @endforeach
                                </select>
                                <button 
                                    type="submit" 
                                    class="w-full py-1.5 px-3 bg-[#475569] hover:bg-slate-700 text-white rounded-md text-xs font-semibold transition-colors cursor-pointer"
                                >
                                    + Tambah ke List
                                </button>
                            </form>
                        @else
                            <p class="text-xs text-slate-400">Seluruh pengguna sudah terdaftar di dalam list ini.</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
