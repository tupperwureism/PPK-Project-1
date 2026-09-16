<!-- DeleteListConfirmationDialog -->
<div id="deleteListDialog" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-md border border-slate-300 shadow-xl max-w-sm w-full p-6 animate-in fade-in duration-200">
        <div class="flex items-center space-x-3 text-red-600 mb-3">
            <div class="w-10 h-10 rounded-md bg-red-50 border border-red-200 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-900">Hapus Daftar Tugas Atomik</h3>
                <p class="text-xs text-slate-500">Aksi ini tidak dapat dibatalkan.</p>
            </div>
        </div>

        <p class="text-xs text-slate-600 leading-relaxed">
            Apakah Anda yakin ingin menghapus list <strong id="deleteListTitleName" class="text-slate-900"></strong>? 
            Seluruh data tugas dan relasi anggota di dalamnya akan dihapus secara permanen via transaksi database atomik (cascade rollback).
        </p>

        <form id="deleteListForm" method="POST" class="mt-5 flex items-center justify-end space-x-2">
            @csrf
            @method('DELETE')
            <button 
                type="button" 
                onclick="closeDeleteDialog()"
                class="px-3 py-1.5 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-md transition-colors cursor-pointer"
            >
                Batal
            </button>
            <button 
                type="submit" 
                class="px-3 py-1.5 text-xs font-semibold text-white bg-red-600 hover:bg-red-700 rounded-md transition-colors shadow-xs cursor-pointer"
            >
                Hapus List Permanen
            </button>
        </form>
    </div>
</div>

<script>
    function openDeleteDialog(listId, listName, deleteUrl) {
        document.getElementById('deleteListTitleName').textContent = listName;
        document.getElementById('deleteListForm').action = deleteUrl;
        document.getElementById('deleteListDialog').classList.remove('hidden');
    }
    function closeDeleteDialog() {
        document.getElementById('deleteListDialog').classList.add('hidden');
    }
</script>
