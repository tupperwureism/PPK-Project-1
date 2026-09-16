@props(['list', 'isOwner' => true, 'availableUsers' => collect()])

<div class="bg-white rounded-md border border-slate-300 shadow-xs hover:border-blue-500 transition-colors p-5 flex flex-col justify-between" id="list-card-{{ $list->id }}">
    <div>
        <!-- Header Card -->
        <div class="flex items-start justify-between gap-3">
            <div class="flex-1 min-w-0">
                <div class="flex items-center space-x-2 mb-1.5">
                    @if ($isOwner)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                            👑 Pemilik
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                            🤝 Kolaborator
                        </span>
                    @endif
                    <span class="text-xs text-slate-500">
                        {{ $list->created_at ? $list->created_at->translatedFormat('d M Y') : '-' }}
                    </span>
                </div>
                <h3 class="text-base font-bold text-slate-900 truncate" title="{{ $list->name }}">
                    {{ $list->name }}
                </h3>
                @if ($list->description)
                    <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $list->description }}</p>
                @endif
            </div>

            @if ($isOwner)
                <!-- Tombol Hapus List (Trigger Delete Dialog) -->
                <button 
                    type="button"
                    onclick="openDeleteDialog('{{ $list->id }}', '{{ addslashes($list->name) }}', '{{ route('lists.destroy', $list) }}')"
                    class="text-slate-400 hover:text-red-600 p-1.5 rounded-md hover:bg-red-50 transition-colors cursor-pointer"
                    title="Hapus List Tugas"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                    </svg>
                </button>
            @endif
        </div>

        <!-- Bagian Anggota Kolaborasi -->
        <div class="mt-4 pt-3 border-t border-slate-200">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Anggota ({{ $list->members->count() }})</span>
                @if ($isOwner)
                    <button 
                        type="button" 
                        onclick="openMemberModal('{{ $list->id }}', '{{ addslashes($list->name) }}', '{{ route('lists.add-member', $list) }}')"
                        class="text-xs font-semibold text-blue-600 hover:text-blue-800 cursor-pointer"
                    >
                        + Kelola
                    </button>
                @endif
            </div>

            <div class="flex flex-wrap gap-1.5">
                @forelse ($list->members as $member)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200" title="{{ $member->email }}">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                        {{ $member->name }}
                    </span>
                @empty
                    <span class="text-xs text-slate-400 italic">Belum ada kolaborator</span>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Footer Card -->
    <div class="mt-4 pt-3 border-t border-slate-200 flex items-center justify-between text-xs">
        <span class="text-slate-500 font-medium">
            📋 {{ $list->tasks ? $list->tasks->count() : 0 }} Tugas
        </span>
        <a href="{{ route('lists.show', $list) }}" class="inline-flex items-center font-semibold text-blue-600 hover:text-blue-800 hover:underline">
            Buka Workspace &rarr;
        </a>
    </div>
</div>
