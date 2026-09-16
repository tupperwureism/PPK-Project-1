@props([
    'totalTasks' => 0,
    'completedTasks' => 0,
    'progressPercentage' => 0,
])

<div class="bg-white p-4 rounded-md border border-[#CBD5E1] shadow-xs space-y-2">
    <div class="flex items-center justify-between text-xs font-semibold text-[#475569] uppercase tracking-wider">
        <span class="flex items-center gap-1.5">
            <svg class="w-4 h-4 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            Progres Penyelesaian Tugas
        </span>
        <div class="flex items-center gap-2">
            <span class="text-slate-900 font-bold font-mono text-sm">{{ $progressPercentage }}%</span>
            @if($totalTasks > 0 && $completedTasks === $totalTasks)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    Selesai
                </span>
            @elseif($totalTasks > 0)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                    Dalam Progres
                </span>
            @else
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-50 text-slate-500 border border-slate-200">
                    Belum Ada Tugas
                </span>
            @endif
        </div>
    </div>

    <!-- Progress Bar Line -->
    <div class="h-2.5 w-full bg-slate-100 rounded-md overflow-hidden border border-[#CBD5E1]" role="progressbar" aria-valuenow="{{ $progressPercentage }}" aria-valuemin="0" aria-valuemax="100" aria-label="Progress penyelesaian tugas">
        <div 
            class="h-full bg-[#2563EB] transition-all duration-300 rounded-xs" 
            style="width: {{ $progressPercentage }}%"
        ></div>
    </div>

    <div class="flex justify-between items-center text-xs text-slate-500 pt-0.5">
        <span>Rasio Tugas: <strong class="text-slate-800 font-semibold">{{ $completedTasks }}</strong> dari <strong class="text-slate-800 font-semibold">{{ $totalTasks }}</strong> tugas telah diselesaikan</span>
        @if($totalTasks > $completedTasks)
            <span class="text-slate-500 font-medium">Tersisa: {{ $totalTasks - $completedTasks }} tugas</span>
        @endif
    </div>
</div>
