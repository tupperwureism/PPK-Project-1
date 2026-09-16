@props(['todoList', 'isOwner'])

<div class="bg-white rounded-md border border-slate-300 shadow-xs p-6 mb-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 mb-2">
                <a href="{{ route('lists.index') }}" class="inline-flex items-center text-xs font-semibold text-blue-600 hover:text-blue-800">
                    &larr; Kembali ke Daftar List
                </a>
                <span class="text-slate-300">•</span>
                @if ($isOwner)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                        👑 Pemilik List
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                        🤝 Kolaborator
                    </span>
                @endif
                <span class="text-xs text-slate-500">
                    Dibuat: {{ $todoList->created_at ? $todoList->created_at->translatedFormat('d M Y') : '-' }}
                </span>
            </div>
            
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">
                {{ $todoList->name }}
            </h1>
            
            @if ($todoList->description)
                <p class="text-sm text-slate-600 mt-1 max-w-3xl">
                    {{ $todoList->description }}
                </p>
            @endif
        </div>

        <div class="flex items-center space-x-2 shrink-0">
            @if ($isOwner)
                <button 
                    type="button" 
                    onclick="openMemberModal('{{ $todoList->id }}', '{{ addslashes($todoList->name) }}', '{{ route('lists.add-member', $todoList) }}')"
                    class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 rounded-md transition-colors shadow-xs cursor-pointer"
                >
                    👥 Kelola Anggota ({{ $todoList->members->count() }})
                </button>
                <button 
                    type="button" 
                    onclick="openDeleteDialog('{{ $todoList->id }}', '{{ addslashes($todoList->name) }}', '{{ route('lists.destroy', $todoList) }}')"
                    class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 rounded-md transition-colors shadow-xs cursor-pointer"
                >
                    🗑️ Hapus List
                </button>
            @else
                <span class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-slate-600 bg-slate-50 border border-slate-200 rounded-md">
                    👥 {{ $todoList->members->count() }} Anggota Kolaborasi
                </span>
            @endif
        </div>
    </div>
</div>
