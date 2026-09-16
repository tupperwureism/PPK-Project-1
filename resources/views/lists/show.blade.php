<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F8FAFC]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $todoList->name }} — JARA</title>
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
                            <span class="font-bold text-lg text-slate-900">JARA</span>
                            <span class="text-xs text-slate-500 ml-2">Workspace Detail</span>
                        </div>
                    </div>

                    <nav class="flex items-center space-x-3">
                        <a href="{{ route('lists.index') }}" class="px-3 py-1.5 text-xs font-semibold rounded-md bg-slate-100 text-slate-800 hover:bg-slate-200">
                            📁 Semua List
                        </a>
                        <a href="{{ route('tasks.index') }}" class="px-3 py-1.5 text-xs font-medium rounded-md text-slate-600 hover:text-blue-600 hover:bg-slate-50">
                            ✅ Semua Tugas
                        </a>
                    </nav>

                    <div class="flex items-center space-x-3 text-xs">
                        <span class="font-semibold text-slate-800">{{ $user->name }}</span>
                        <div class="h-7 w-7 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Container -->
        <main class="flex-1 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Notifications -->
                @if (session('success'))
                    <div class="rounded-md bg-emerald-50 border border-emerald-200 p-3 mb-6 flex items-center space-x-2 text-emerald-800 text-xs">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="rounded-md bg-red-50 border border-red-200 p-3 mb-6 text-red-800 text-xs">
                        <p class="font-semibold mb-1">Terdapat kendala:</p>
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- 1. Komponen Developer 1: List Header & Member Controls -->
                @include('components.lists.header', ['todoList' => $todoList, 'isOwner' => $isOwner])

                <!-- 2. Slot Modular Developer 3: Progress Tracking Bar -->
                <div id="dev3-progress-section" class="mb-6">
                    <div class="bg-white rounded-md border border-slate-300 p-4 shadow-xs">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Progres Penyelesaian Tugas</span>
                            @php
                                $totalTasks = $todoList->tasks->count();
                                $completedTasks = $todoList->tasks->where('is_completed', true)->count();
                                $percentage = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                            @endphp
                            <span class="text-xs font-bold text-blue-600">{{ $percentage }}% Selesai ({{ $completedTasks }}/{{ $totalTasks }})</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-md h-2.5 overflow-hidden border border-slate-200">
                            <div class="bg-blue-600 h-2.5 rounded-md transition-all duration-300" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- 3. Slot Modular Developer 2: Task Board / Lifecycle -->
                <div id="dev2-tasks-section">
                    <div class="bg-white rounded-md border border-slate-300 p-6 shadow-xs">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-200 mb-4">
                            <div>
                                <h2 class="text-base font-bold text-slate-900">Daftar Tugas</h2>
                                <p class="text-xs text-slate-500">Kelola dan selesaikan tugas-tugas dalam list ini.</p>
                            </div>
                            <a href="{{ route('tasks.index', ['list_id' => $todoList->id]) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-md transition-colors shadow-xs">
                                + Kelola Tugas
                            </a>
                        </div>

                        @if ($todoList->tasks->isEmpty())
                            <div class="text-center py-10 border border-dashed border-slate-200 rounded-md">
                                <p class="text-xs font-medium text-slate-500">Belum ada tugas pada list ini.</p>
                            </div>
                        @else
                            <div class="divide-y divide-slate-100">
                                @foreach ($todoList->tasks as $task)
                                    <div class="py-3 flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <span class="w-2 h-2 rounded-full {{ $task->is_completed ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                            <span class="text-sm font-medium text-slate-800 {{ $task->is_completed ? 'line-through text-slate-400' : '' }}">
                                                {{ $task->title }}
                                            </span>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="px-2 py-0.5 text-xs font-medium rounded-md {{ $task->is_completed ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-700' }}">
                                                {{ $task->is_completed ? 'Selesai' : 'In Progress' }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modals Developer 1 -->
    @include('components.lists.member-modal', ['availableUsers' => $availableUsers])
    @include('components.lists.delete-dialog')
</body>
</html>
