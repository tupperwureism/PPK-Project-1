<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#F8FAFC]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manajemen Tugas — JARA</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        jara: {
                            primary: '#2563EB',
                            'primary-hover': '#1D4ED8',
                            secondary: '#475569',
                            bg: '#F8FAFC',
                            border: '#CBD5E1',
                            success: '#16A34A',
                            warning: '#D97706',
                            error: '#DC2626'
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-full font-sans text-slate-800 antialiased bg-[#F8FAFC]">
    <div class="min-h-full flex flex-col">
        <!-- Main Navigation Header -->
        <header class="bg-white border-b border-[#CBD5E1] sticky top-0 z-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-md bg-[#2563EB] flex items-center justify-center text-white font-bold text-lg">
                            J
                        </div>
                        <div>
                            <span class="font-bold text-lg text-slate-900 tracking-tight">JARA</span>
                            <span class="text-xs text-slate-500 font-medium ml-2 pl-2 border-l border-slate-300">Modul Tugas • Developer 2</span>
                        </div>
                    </div>

                    <nav class="flex items-center space-x-2">
                        <a href="{{ route('lists.index') }}" class="px-3 py-1.5 text-xs font-semibold rounded-md border border-[#CBD5E1] text-slate-600 hover:bg-slate-50 transition">
                            📁 Kembali ke Daftar List
                        </a>
                        <a href="{{ route('tasks.index') }}" class="px-3 py-1.5 text-xs font-semibold rounded-md bg-blue-50 border border-blue-200 text-[#2563EB]">
                            ✓ Task Board
                        </a>
                    </nav>
                </div>
            </div>
        </header>

        <!-- Main Workspace -->
        <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            
            <!-- Breadcrumb & Workspace Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-[#CBD5E1]">
                <div>
                    <div class="flex items-center space-x-2 text-xs text-[#475569] mb-1">
                        <span>Workspace</span>
                        <span>/</span>
                        @if ($currentList)
                            <a href="{{ route('lists.index') }}" class="hover:underline">Lists</a>
                            <span>/</span>
                            <span class="font-semibold text-slate-900">{{ $currentList->name }}</span>
                        @else
                            <span class="font-semibold text-slate-900">Semua Tugas</span>
                        @endif
                    </div>
                    <h1 class="text-2xl font-bold text-slate-900">
                        @if ($currentList)
                            List Board: {{ $currentList->name }}
                        @else
                            Task Board & Pengelolaan Tugas
                        @endif
                    </h1>
                </div>

                <!-- Progress Tracker Bar (REQ-06 Indicator) -->
                <div class="w-full sm:w-72 bg-white border border-[#CBD5E1] rounded-md p-3">
                    <div class="flex items-center justify-between text-xs font-medium text-[#475569] mb-1.5">
                        <span>Progres Penyelesaian</span>
                        <span class="font-bold text-slate-900">{{ $progressPercentage }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded h-2 overflow-hidden border border-slate-200">
                        <div class="bg-[#2563EB] h-full transition-all duration-300" style="width: {{ $progressPercentage }}%"></div>
                    </div>
                    <div class="mt-1 text-[11px] text-slate-500 text-right">
                        {{ $completedTasks }} dari {{ $totalTasks }} tugas selesai
                    </div>
                </div>
            </div>

            <!-- Feedback Notifications -->
            @if (session('success'))
                <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-[#16A34A] flex items-center justify-between" role="alert">
                    <div class="flex items-center space-x-2">
                        <span class="font-bold text-base">✓</span>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-[#DC2626]" role="alert">
                    <p class="font-semibold mb-1">Terdapat kesalahan input:</p>
                    <ul class="list-disc list-inside text-xs space-y-0.5 ml-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Workspace Layout: Two Columns (Form Creation & Task Board) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Left Column: Form Task Creation (SRS 4 / REQ-04) -->
                <section class="lg:col-span-4 bg-white border border-[#CBD5E1] rounded-md p-5 shadow-xs">
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-[#CBD5E1]">
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Tambah Tugas Baru</h2>
                            <p class="text-xs text-slate-500 mt-0.5">SRS 4: Tetapkan judul, grup, prioritas & deadline</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('tasks.store') }}" class="space-y-4">
                        @csrf
                        
                        <!-- List Relationship -->
                        @if ($currentList)
                            <input type="hidden" name="todo_list_id" value="{{ $currentList->id }}">
                            <div class="rounded-md bg-slate-50 border border-[#CBD5E1] px-3 py-2 text-xs flex items-center justify-between">
                                <span class="text-slate-600">List Tujuan:</span>
                                <span class="font-semibold text-[#2563EB]">{{ $currentList->name }}</span>
                            </div>
                        @else
                            <div>
                                <label for="todo_list_id" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                    Daftar List (Wadah)
                                </label>
                                <select 
                                    id="todo_list_id" 
                                    name="todo_list_id" 
                                    class="w-full text-xs rounded-md border border-[#CBD5E1] px-3 py-2 bg-white text-slate-800 focus:outline-none focus:ring-1 focus:ring-[#2563EB] focus:border-[#2563EB]"
                                >
                                    <option value="">-- Tugas Umum (Tanpa List) --</option>
                                    @foreach ($availableLists as $listOption)
                                        <option value="{{ $listOption->id }}" @selected(old('todo_list_id') == $listOption->id)>
                                            📁 {{ $listOption->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <!-- Task Title -->
                        <div>
                            <label for="title" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Nama Tugas <span class="text-[#DC2626]">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="title" 
                                name="title" 
                                value="{{ old('title') }}" 
                                required 
                                maxlength="255"
                                placeholder="Contoh: Implementasi query prepared statement" 
                                class="w-full text-xs rounded-md border border-[#CBD5E1] px-3 py-2 bg-white text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-[#2563EB] focus:border-[#2563EB]"
                            >
                        </div>

                        <!-- Task Group (Categorization) -->
                        <div>
                            <label for="group" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Kelompok / Group
                            </label>
                            <input 
                                type="text" 
                                id="group" 
                                name="group" 
                                list="group-suggestions" 
                                value="{{ old('group', 'General') }}" 
                                placeholder="Contoh: Backend, Frontend, UI, Database..." 
                                class="w-full text-xs rounded-md border border-[#CBD5E1] px-3 py-2 bg-white text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-[#2563EB] focus:border-[#2563EB]"
                            >
                            <datalist id="group-suggestions">
                                @foreach ($availableGroups as $grp)
                                    <option value="{{ $grp }}">
                                @endforeach
                            </datalist>
                            <p class="text-[11px] text-slate-500 mt-1">Grup digunakan untuk membagi tugas per divisi/modul.</p>
                        </div>

                        <!-- Priority & Due Date (Two Columns) -->
                        <div class="grid grid-cols-2 gap-3">
                            <!-- Priority Selector -->
                            <div>
                                <label for="priority" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                    Prioritas <span class="text-[#DC2626]">*</span>
                                </label>
                                <select 
                                    id="priority" 
                                    name="priority" 
                                    required 
                                    class="w-full text-xs rounded-md border border-[#CBD5E1] px-2.5 py-2 bg-white text-slate-800 focus:outline-none focus:ring-1 focus:ring-[#2563EB] focus:border-[#2563EB]"
                                >
                                    <option value="HIGH" @selected(old('priority') === 'HIGH')>HIGH (Tinggi)</option>
                                    <option value="MEDIUM" @selected(old('priority', 'MEDIUM') === 'MEDIUM')>MEDIUM (Sedang)</option>
                                    <option value="LOW" @selected(old('priority') === 'LOW')>LOW (Rendah)</option>
                                </select>
                            </div>

                            <!-- Due Date Picker -->
                            <div>
                                <label for="due_date" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                    Deadline <span class="text-[#DC2626]">*</span>
                                </label>
                                <input 
                                    type="date" 
                                    id="due_date" 
                                    name="due_date" 
                                    value="{{ old('due_date', date('Y-m-d')) }}" 
                                    required 
                                    class="w-full text-xs rounded-md border border-[#CBD5E1] px-2.5 py-2 bg-white text-slate-800 focus:outline-none focus:ring-1 focus:ring-[#2563EB] focus:border-[#2563EB]"
                                >
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button 
                            type="submit" 
                            class="w-full mt-2 rounded-md bg-[#2563EB] hover:bg-[#1D4ED8] text-white text-xs font-semibold py-2.5 px-4 transition cursor-pointer"
                        >
                            + Tambah Tugas ke Board
                        </button>
                    </form>
                </section>

                <!-- Right Column: Task Board & Grouping Sections (Bab 13) -->
                <section class="lg:col-span-8 space-y-4">
                    
                    <!-- Filter and Categorization Bar -->
                    <div class="bg-white border border-[#CBD5E1] rounded-md p-3 space-y-2.5">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center space-x-2">
                                <span class="text-xs font-semibold text-[#475569] uppercase tracking-wider">Filter Grup:</span>
                                <a 
                                    href="{{ route('tasks.index', array_filter(['list_id' => $currentList?->id, 'priority' => $selectedPriority, 'status' => $selectedStatus])) }}" 
                                    class="text-xs px-2.5 py-1 rounded-md border {{ empty($selectedGroup) ? 'bg-[#2563EB] text-white border-[#2563EB]' : 'bg-slate-50 text-slate-600 border-[#CBD5E1] hover:bg-slate-100' }}"
                                >
                                    Semua
                                </a>
                                @foreach ($availableGroups as $grp)
                                    <a 
                                        href="{{ route('tasks.index', array_filter(['list_id' => $currentList?->id, 'group' => $grp, 'priority' => $selectedPriority, 'status' => $selectedStatus])) }}" 
                                        class="text-xs px-2.5 py-1 rounded-md border {{ $selectedGroup === $grp ? 'bg-[#2563EB] text-white border-[#2563EB]' : 'bg-slate-50 text-slate-600 border-[#CBD5E1] hover:bg-slate-100' }}"
                                    >
                                        {{ $grp }}
                                    </a>
                                @endforeach
                            </div>

                            <!-- Filter Priority -->
                            <div class="flex items-center space-x-1.5 text-xs">
                                <span class="text-[#475569] font-medium">Prioritas:</span>
                                @foreach (['ALL' => 'Semua', 'HIGH' => 'High', 'MEDIUM' => 'Med', 'LOW' => 'Low'] as $priKey => $priLabel)
                                    <a 
                                        href="{{ route('tasks.index', array_filter(['list_id' => $currentList?->id, 'group' => $selectedGroup, 'priority' => $priKey === 'ALL' ? null : $priKey, 'status' => $selectedStatus])) }}" 
                                        class="px-2 py-0.5 rounded border text-[11px] {{ ($selectedPriority === $priKey || (empty($selectedPriority) && $priKey === 'ALL')) ? 'bg-slate-800 text-white border-slate-800' : 'bg-white text-slate-600 border-[#CBD5E1] hover:bg-slate-50' }}"
                                    >
                                        {{ $priLabel }}
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <!-- Filter Status Penyelesaian (SRS 5) -->
                        <div class="flex items-center space-x-2 pt-2 border-t border-slate-100 text-xs">
                            <span class="text-[#475569] font-semibold uppercase tracking-wider">Status Tugas:</span>
                            <a 
                                href="{{ route('tasks.index', array_filter(['list_id' => $currentList?->id, 'group' => $selectedGroup, 'priority' => $selectedPriority])) }}" 
                                class="px-2.5 py-0.5 rounded border text-[11px] {{ empty($selectedStatus) ? 'bg-slate-800 text-white border-slate-800 font-semibold' : 'bg-white text-slate-600 border-[#CBD5E1] hover:bg-slate-50' }}"
                            >
                                Semua ({{ $totalTasks }})
                            </a>
                            <a 
                                href="{{ route('tasks.index', array_filter(['list_id' => $currentList?->id, 'group' => $selectedGroup, 'priority' => $selectedPriority, 'status' => 'active'])) }}" 
                                class="px-2.5 py-0.5 rounded border text-[11px] {{ $selectedStatus === 'active' ? 'bg-slate-800 text-white border-slate-800 font-semibold' : 'bg-white text-slate-600 border-[#CBD5E1] hover:bg-slate-50' }}"
                            >
                                ⏳ Belum Selesai ({{ $totalTasks - $completedTasks }})
                            </a>
                            <a 
                                href="{{ route('tasks.index', array_filter(['list_id' => $currentList?->id, 'group' => $selectedGroup, 'priority' => $selectedPriority, 'status' => 'completed'])) }}" 
                                class="px-2.5 py-0.5 rounded border text-[11px] {{ $selectedStatus === 'completed' ? 'bg-[#16A34A] text-white border-[#16A34A] font-semibold' : 'bg-white text-slate-600 border-[#CBD5E1] hover:bg-slate-50' }}"
                            >
                                ✓ Selesai ({{ $completedTasks }})
                            </a>
                        </div>
                    </div>

                    <!-- Task Groups Display (TaskGroupSection) -->
                    @if ($tasks->isEmpty())
                        <div class="bg-white border border-[#CBD5E1] rounded-md p-12 text-center">
                            <div class="w-10 h-10 rounded-md bg-slate-100 border border-slate-200 text-slate-400 mx-auto flex items-center justify-center font-bold text-base mb-2">
                                📋
                            </div>
                            <h3 class="text-sm font-bold text-slate-900">Belum ada tugas pada kategori ini</h3>
                            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                                Gunakan form di sebelah kiri untuk menambahkan tugas baru dengan kelompok, prioritas, dan deadline yang jelas.
                            </p>
                        </div>
                    @else
                        @foreach ($groupedTasks as $groupName => $groupItems)
                            <div class="bg-white border border-[#CBD5E1] rounded-md overflow-hidden">
                                
                                <!-- Group Header Section -->
                                <div class="bg-slate-50 border-b border-[#CBD5E1] px-4 py-2.5 flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <span class="w-2.5 h-2.5 rounded-sm bg-[#2563EB]"></span>
                                        <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                                            Kategori: {{ $groupName ?: 'General' }}
                                        </h3>
                                    </div>
                                    <span class="text-xs text-[#475569] font-medium">
                                        {{ $groupItems->where('is_completed', true)->count() }}/{{ $groupItems->count() }} Selesai
                                    </span>
                                </div>

                                <!-- Tasks List under this group (TaskList) -->
                                <div class="divide-y divide-[#CBD5E1]">
                                    @foreach ($groupItems as $task)
                                        @php
                                            $isOverdue = ! $task->is_completed && $task->due_date->isPast() && ! $task->due_date->isToday();
                                            $priorityUpper = strtoupper($task->priority);
                                            $priorityUpper = match($priorityUpper) {
                                                'TINGGI' => 'HIGH',
                                                'SEDANG' => 'MEDIUM',
                                                'RENDAH' => 'LOW',
                                                default => $priorityUpper,
                                            };
                                        @endphp
                                        <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50/60 transition {{ $task->is_completed ? 'bg-slate-50/40' : '' }}">
                                            
                                            <!-- Left Item: Checkbox (REQ-05 Toggle) & Title -->
                                            <div class="flex items-start space-x-3 min-w-0">
                                                <form method="POST" action="{{ route('tasks.toggle', $task) }}" class="pt-0.5">
                                                    @csrf
                                                    @method('PATCH')
                                                    @if ($currentList)
                                                        <input type="hidden" name="list_id" value="{{ $currentList->id }}">
                                                    @endif
                                                    @if ($selectedGroup)
                                                        <input type="hidden" name="group" value="{{ $selectedGroup }}">
                                                    @endif
                                                    @if ($selectedStatus)
                                                        <input type="hidden" name="status" value="{{ $selectedStatus }}">
                                                    @endif
                                                    <button 
                                                        type="submit" 
                                                        title="{{ $task->is_completed ? 'Tandai belum selesai' : 'Tandai selesai' }}" 
                                                        class="w-5 h-5 rounded border flex items-center justify-center text-xs font-bold transition cursor-pointer {{ $task->is_completed ? 'bg-[#16A34A] border-[#16A34A] text-white' : 'bg-white border-slate-400 hover:border-[#2563EB]' }}"
                                                    >
                                                        @if ($task->is_completed)✓@endif
                                                    </button>
                                                </form>

                                                <div class="min-w-0">
                                                    <p class="text-sm font-medium {{ $task->is_completed ? 'text-slate-400 line-through' : 'text-slate-900' }}">
                                                        {{ $task->title }}
                                                    </p>
                                                    
                                                    <!-- Metadata Row: Priority Badge, Deadline, List name -->
                                                    <div class="flex flex-wrap items-center gap-2 mt-1.5 text-xs text-[#475569]">
                                                        <!-- Priority Badge (SRS Bab 10 & 13) -->
                                                        @if ($priorityUpper === 'HIGH')
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold border border-red-200 bg-red-50 text-[#DC2626]">
                                                                HIGH
                                                            </span>
                                                        @elseif ($priorityUpper === 'MEDIUM')
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold border border-amber-200 bg-amber-50 text-[#D97706]">
                                                                MEDIUM
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium border border-slate-200 bg-slate-100 text-[#475569]">
                                                                LOW
                                                            </span>
                                                        @endif

                                                        <!-- Due Date indicator -->
                                                        <span class="inline-flex items-center {{ $isOverdue ? 'text-[#DC2626] font-semibold' : '' }}">
                                                            <span class="mr-1">📅</span>
                                                            {{ $task->due_date->format('d M Y') }}
                                                            @if ($isOverdue)
                                                                <span class="ml-1 text-[10px] bg-red-100 text-[#DC2626] px-1.5 py-0.2 rounded font-bold">Terlambat</span>
                                                            @elseif ($task->due_date->isToday())
                                                                <span class="ml-1 text-[10px] bg-amber-100 text-[#D97706] px-1.5 py-0.2 rounded font-bold">Hari ini</span>
                                                            @endif
                                                        </span>

                                                        <!-- List relation tag (if on global view) -->
                                                        @if ($task->todoList && ! $currentList)
                                                            <a 
                                                                href="{{ route('lists.show', $task->todoList) }}" 
                                                                class="inline-flex items-center px-2 py-0.5 rounded border border-slate-200 bg-white text-slate-600 hover:text-[#2563EB] hover:border-[#2563EB]"
                                                            >
                                                                📁 {{ $task->todoList->name }}
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Right Action: Edit & Delete Buttons (Full CRUD) -->
                                            <div class="flex items-center space-x-1 sm:justify-end shrink-0 pl-8 sm:pl-0">
                                                <button 
                                                    type="button" 
                                                    onclick='openEditModal({
                                                        id: {{ $task->id }},
                                                        title: @json($task->title),
                                                        group: @json($task->group),
                                                        priority: @json($priorityUpper),
                                                        due_date: @json($task->due_date->format("Y-m-d")),
                                                        list_id: {{ $currentList ? $currentList->id : "null" }}
                                                    })'
                                                    class="text-xs text-slate-500 hover:text-[#2563EB] px-2 py-1 rounded hover:bg-slate-100 transition cursor-pointer"
                                                >
                                                    Edit
                                                </button>

                                                <form method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tugas ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    @if ($currentList)
                                                        <input type="hidden" name="list_id" value="{{ $currentList->id }}">
                                                    @endif
                                                    <button 
                                                        type="submit" 
                                                        class="text-xs text-slate-400 hover:text-[#DC2626] px-2 py-1 rounded hover:bg-red-50 transition cursor-pointer"
                                                    >
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>

                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @endif
                </section>
            </div>
        </main>
    </div>

    <!-- Edit Task Modal (Atomic Component - SRS Bab 10 & 13) -->
    <div id="edit-task-modal" class="fixed inset-0 z-50 hidden bg-slate-900/40 flex items-center justify-center p-4">
        <div class="bg-white border border-[#CBD5E1] rounded-md max-w-md w-full p-5 shadow-lg">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-[#CBD5E1]">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Edit Rincian Tugas</h3>
                    <p class="text-xs text-slate-500">Perbarui judul, kelompok, prioritas, atau tenggat waktu.</p>
                </div>
                <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-700 text-lg font-bold">&times;</button>
            </div>

            <form id="edit-task-form" method="POST" action="" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit-list-id" name="list_id" value="">

                <div>
                    <label for="edit-title" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                        Nama Tugas <span class="text-[#DC2626]">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="edit-title" 
                        name="title" 
                        required 
                        maxlength="255" 
                        class="w-full text-xs rounded-md border border-[#CBD5E1] px-3 py-2 bg-white text-slate-800 focus:outline-none focus:ring-1 focus:ring-[#2563EB] focus:border-[#2563EB]"
                    >
                </div>

                <div>
                    <label for="edit-group" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                        Kelompok / Group
                    </label>
                    <input 
                        type="text" 
                        id="edit-group" 
                        name="group" 
                        list="edit-group-suggestions" 
                        class="w-full text-xs rounded-md border border-[#CBD5E1] px-3 py-2 bg-white text-slate-800 focus:outline-none focus:ring-1 focus:ring-[#2563EB] focus:border-[#2563EB]"
                    >
                    <datalist id="edit-group-suggestions">
                        @foreach ($availableGroups as $grp)
                            <option value="{{ $grp }}">
                        @endforeach
                    </datalist>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="edit-priority" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Prioritas <span class="text-[#DC2626]">*</span>
                        </label>
                        <select 
                            id="edit-priority" 
                            name="priority" 
                            required 
                            class="w-full text-xs rounded-md border border-[#CBD5E1] px-2.5 py-2 bg-white text-slate-800 focus:outline-none focus:ring-1 focus:ring-[#2563EB] focus:border-[#2563EB]"
                        >
                            <option value="HIGH">HIGH (Tinggi)</option>
                            <option value="MEDIUM">MEDIUM (Sedang)</option>
                            <option value="LOW">LOW (Rendah)</option>
                        </select>
                    </div>

                    <div>
                        <label for="edit-due-date" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Deadline <span class="text-[#DC2626]">*</span>
                        </label>
                        <input 
                            type="date" 
                            id="edit-due-date" 
                            name="due_date" 
                            required 
                            class="w-full text-xs rounded-md border border-[#CBD5E1] px-2.5 py-2 bg-white text-slate-800 focus:outline-none focus:ring-1 focus:ring-[#2563EB] focus:border-[#2563EB]"
                        >
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-2 pt-2 border-t border-slate-100">
                    <button 
                        type="button" 
                        onclick="closeEditModal()" 
                        class="px-3 py-1.5 rounded-md border border-[#CBD5E1] text-xs font-medium text-slate-600 hover:bg-slate-50 transition"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit" 
                        class="px-4 py-1.5 rounded-md bg-[#2563EB] hover:bg-[#1D4ED8] text-white text-xs font-semibold transition cursor-pointer"
                    >
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(task) {
            const modal = document.getElementById('edit-task-modal');
            const form = document.getElementById('edit-task-form');
            form.action = '/tasks/' + task.id;
            document.getElementById('edit-title').value = task.title;
            document.getElementById('edit-group').value = task.group || 'General';
            document.getElementById('edit-priority').value = task.priority || 'MEDIUM';
            document.getElementById('edit-due-date').value = task.due_date;
            document.getElementById('edit-list-id').value = task.list_id || '';
            modal.classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('edit-task-modal').classList.add('hidden');
        }

        // Close modal on Escape key press
        window.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeEditModal();
            }
        });
    </script>
</body>
</html>
