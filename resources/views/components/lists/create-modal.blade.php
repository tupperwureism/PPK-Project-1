<!-- CreateListModal -->
<div id="createListModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-md border border-slate-300 shadow-xl max-w-md w-full p-6 animate-in fade-in duration-200">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200">
            <h3 class="text-base font-bold text-slate-900">Buat Daftar Tugas Baru</h3>
            <button type="button" onclick="closeCreateListModal()" class="text-slate-400 hover:text-slate-600 cursor-pointer p-1">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form action="{{ route('lists.store') }}" method="POST" class="mt-4 space-y-4">
            @csrf
            <div>
                <label for="create_title" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                    Judul List <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="title" 
                    id="create_title" 
                    required 
                    placeholder="Contoh: Pengembangan Sprint 1, Tugas PPK..."
                    class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600"
                >
            </div>

            <div>
                <label for="create_description" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                    Deskripsi (Opsional)
                </label>
                <textarea 
                    name="description" 
                    id="create_description" 
                    rows="3" 
                    placeholder="Contoh: Fokus pada core feature JARA..."
                    class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600"
                ></textarea>
            </div>

            <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-200">
                <button 
                    type="button" 
                    onclick="closeCreateListModal()"
                    class="px-4 py-2 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-md transition-colors cursor-pointer"
                >
                    Batal
                </button>
                <button 
                    type="submit" 
                    class="px-4 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-md transition-colors shadow-xs cursor-pointer"
                >
                    Simpan List
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateListModal() {
        document.getElementById('createListModal').classList.remove('hidden');
        document.getElementById('create_title').focus();
    }
    function closeCreateListModal() {
        document.getElementById('createListModal').classList.add('hidden');
    }
</script>
