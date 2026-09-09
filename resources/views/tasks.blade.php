<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Tasks | {{ config('app.name', 'JARA') }}</title>
        <script src="https://cdn.tailwindcss.com"></script>
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="min-h-screen bg-[#f4f1ea] text-[#25231f] antialiased">
        <main class="mx-auto max-w-6xl px-5 py-8 sm:px-8 lg:py-14">
            <header class="mb-10 flex flex-col gap-5 border-b border-[#d8d0c2] pb-8 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="mb-3 text-xs font-semibold uppercase tracking-[0.28em] text-[#b45439]">JARA / workspace</p>
                    <h1 class="font-serif text-4xl font-semibold tracking-tight sm:text-5xl">
                        My tasks @if ($currentList) <span class="text-2xl sm:text-3xl font-normal text-[#716b61]">/ {{ $currentList->name }}</span> @endif
                    </h1>
                    <p class="mt-3 max-w-xl text-sm leading-6 text-[#716b61]">
                        @if ($currentList)
                            Menampilkan tugas yang terhubung dengan list <strong>{{ $currentList->name }}</strong>.
                        @else
                            Rencanakan pekerjaan hari ini, lalu lihat progresnya bergerak.
                        @endif
                    </p>
                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        <a href="{{ route('lists.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg bg-[#e7dfd2] text-[#595246] hover:bg-[#dbd0bf] transition">
                            ← Buka Daftar List (Projects)
                        </a>
                        @if ($currentList)
                            <a href="{{ route('tasks.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg bg-[#fffdf8] border border-[#bdb4a6] text-[#716b61] hover:text-[#25231f] hover:border-[#b45439] transition">
                                Lihat Semua Tugas
                            </a>
                        @endif
                    </div>
                </div>
                <div class="text-left sm:text-right">
                    <p class="text-4xl font-semibold text-[#b45439]">{{ $progressPercentage }}%</p>
                    <p class="text-sm text-[#716b61]">{{ $completedTasks }} dari {{ $totalTasks }} tugas selesai</p>
                </div>
            </header>

            @if (session('success'))
                <div class="mb-6 border-l-4 border-[#4b8068] bg-[#e4eee6] px-4 py-3 text-sm text-[#315642]" role="status">{{ session('success') }}</div>
            @endif

            <section class="mb-10 grid gap-8 lg:grid-cols-[minmax(0,1fr)_22rem] lg:items-start">
                <div>
                    <div class="mb-3 flex items-center justify-between text-xs font-semibold uppercase tracking-[0.18em] text-[#716b61]">
                        <span>Progress</span>
                        <span>{{ $completedTasks }}/{{ $totalTasks }}</span>
                    </div>
                    <div class="h-3 overflow-hidden rounded-full bg-[#ded8cc]" role="progressbar" aria-valuenow="{{ $progressPercentage }}" aria-valuemin="0" aria-valuemax="100" aria-label="Progress tugas">
                        <div class="h-full rounded-full bg-[#b45439] transition-all" style="width: {{ $progressPercentage }}%"></div>
                    </div>
                </div>

                <form method="POST" action="{{ route('tasks.store') }}" class="border border-[#d8d0c2] bg-[#fffdf8] p-5 shadow-[0_12px_30px_rgba(64,51,35,0.06)]">
                    @csrf
                    <h2 class="mb-5 font-serif text-2xl font-semibold">Tambah tugas</h2>

                    @if ($currentList)
                        <input type="hidden" name="todo_list_id" value="{{ $currentList->id }}">
                        <div class="mb-4 rounded-lg bg-[#e7dfd2]/60 border border-[#d8d0c2] px-3 py-2 text-xs text-[#595246] flex items-center justify-between">
                            <span>List: <strong>{{ $currentList->name }}</strong></span>
                            <span class="text-[#b45439] font-semibold">Tersambung</span>
                        </div>
                    @else
                        <label class="mb-4 block text-sm font-medium" for="todo_list_id">Kategori List (Opsional)
                            <select id="todo_list_id" name="todo_list_id" class="mt-2 w-full border border-[#c9c0b2] bg-[#fffdf8] px-3 py-2 text-sm outline-none focus:border-[#b45439] focus:ring-0">
                                <option value="">-- Tanpa Kategori / Umum --</option>
                                @foreach ($availableLists as $listOption)
                                    <option value="{{ $listOption->id }}" @selected(old('todo_list_id') == $listOption->id)>
                                        {{ $listOption->name }}
                                    </option>
                                @endforeach
                            </select>
                        </label>
                        @error('todo_list_id') <p class="-mt-2 mb-3 text-xs text-[#a53b2b]">{{ $message }}</p> @enderror
                    @endif

                    <label class="mb-4 block text-sm font-medium" for="title">Nama tugas
                        <input id="title" name="title" type="text" value="{{ old('title') }}" required maxlength="255" class="mt-2 w-full border-0 border-b border-[#bdb4a6] bg-transparent px-0 py-2 text-sm outline-none focus:border-[#b45439] focus:ring-0" placeholder="Contoh: Review proposal">
                    </label>
                    @error('title') <p class="-mt-2 mb-3 text-xs text-[#a53b2b]">{{ $message }}</p> @enderror
                    <div class="mb-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2">
                        <label class="block text-sm font-medium" for="priority">Prioritas
                            <select id="priority" name="priority" required class="mt-2 w-full border border-[#c9c0b2] bg-[#fffdf8] px-3 py-2 text-sm outline-none focus:border-[#b45439] focus:ring-0">
                                <option value="rendah" @selected(old('priority') === 'rendah')>Rendah</option>
                                <option value="sedang" @selected(old('priority', 'sedang') === 'sedang')>Sedang</option>
                                <option value="tinggi" @selected(old('priority') === 'tinggi')>Tinggi</option>
                            </select>
                        </label>
                        <label class="block text-sm font-medium" for="due_date">Deadline
                            <input id="due_date" name="due_date" type="date" value="{{ old('due_date') }}" required class="mt-2 w-full border border-[#c9c0b2] bg-[#fffdf8] px-3 py-2 text-sm outline-none focus:border-[#b45439] focus:ring-0">
                        </label>
                    </div>
                    @error('priority') <p class="mb-3 text-xs text-[#a53b2b]">{{ $message }}</p> @enderror
                    @error('due_date') <p class="mb-3 text-xs text-[#a53b2b]">{{ $message }}</p> @enderror
                    <button type="submit" class="w-full bg-[#25231f] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#b45439]">Tambah ke list</button>
                </form>
            </section>

            <section>
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="font-serif text-2xl font-semibold">Daftar pekerjaan</h2>
                    <span class="text-sm text-[#716b61]">{{ $totalTasks }} total</span>
                </div>
                <div class="divide-y divide-[#e2dbcf] border-y border-[#d8d0c2] bg-[#fffdf8]">
                    @forelse ($tasks as $task)
                        <article class="flex flex-col gap-4 px-4 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                            <div class="flex min-w-0 items-start gap-4">
                                <form method="POST" action="{{ route('tasks.toggle', $task) }}" class="pt-1">
                                    @csrf
                                    @method('PATCH')
                                    @if ($currentList)
                                        <input type="hidden" name="list_id" value="{{ $currentList->id }}">
                                    @endif
                                    <button type="submit" class="flex h-5 w-5 items-center justify-center rounded-full border-2 {{ $task->is_completed ? 'border-[#4b8068] bg-[#4b8068] text-white' : 'border-[#aaa092] bg-transparent' }}" aria-label="{{ $task->is_completed ? 'Tandai belum selesai' : 'Tandai selesai' }}">
                                        @if ($task->is_completed)<span class="text-xs">&#10003;</span>@endif
                                    </button>
                                </form>
                                <div class="min-w-0">
                                    <h3 class="truncate text-base font-medium {{ $task->is_completed ? 'text-[#938c81] line-through' : '' }}">{{ $task->title }}</h3>
                                    <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-[#716b61]">
                                        <span class="rounded-full px-2 py-1 font-medium {{ ['tinggi' => 'bg-[#f7ddd5] text-[#a53b2b]', 'sedang' => 'bg-[#f4e8c6] text-[#806319]', 'rendah' => 'bg-[#e4eee6] text-[#315642]'][$task->priority] }}">{{ ucfirst($task->priority) }}</span>
                                        <span>Deadline {{ $task->due_date->format('d M Y') }}</span>
                                        @if ($task->todoList)
                                            <a href="{{ route('tasks.index', ['list_id' => $task->todoList->id]) }}" class="inline-flex items-center gap-1 rounded-full bg-[#e8e4db] px-2.5 py-0.5 text-[11px] font-medium text-[#595246] hover:bg-[#ded8cb] transition" title="Buka list {{ $task->todoList->name }}">
                                                📁 {{ $task->todoList->name }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('tasks.destroy', $task) }}">
                                @csrf
                                @method('DELETE')
                                @if ($currentList)
                                    <input type="hidden" name="list_id" value="{{ $currentList->id }}">
                                @endif
                                <button type="submit" class="text-sm font-medium text-[#938c81] transition hover:text-[#a53b2b]">Hapus</button>
                            </form>
                        </article>
                    @empty
                        <div class="px-6 py-16 text-center">
                            <p class="font-serif text-2xl font-semibold">Belum ada tugas</p>
                            <p class="mt-2 text-sm text-[#716b61]">Tambahkan pekerjaan pertama untuk mulai memantau progres.</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </main>
    </body>
</html>
