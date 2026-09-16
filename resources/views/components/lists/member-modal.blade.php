<!-- ListMemberManagerModal -->
<div id="memberManagerModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-md border border-slate-300 shadow-xl max-w-md w-full p-6 animate-in fade-in duration-200">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Kelola Anggota Kolaborasi</h3>
                <p class="text-xs text-slate-500" id="memberModalListTitle"></p>
            </div>
            <button type="button" onclick="closeMemberModal()" class="text-slate-400 hover:text-slate-600 cursor-pointer p-1">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Form Tambah Kolaborator -->
        <form id="memberManagerForm" method="POST" class="mt-4 space-y-3">
            @csrf
            <div>
                <label for="member_user_id" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                    Pilih Pengguna Sistem
                </label>
                <select 
                    name="user_id" 
                    id="member_user_id"
                    class="w-full rounded-md border border-slate-300 px-3 py-2 text-xs text-slate-800 bg-white focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600"
                >
                    <option value="" selected>-- Pilih dari daftar pengguna --</option>
                    @if(isset($availableUsers))
                        @foreach ($availableUsers as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="relative flex py-1 items-center">
                <div class="flex-grow border-t border-slate-200"></div>
                <span class="flex-shrink mx-2 text-slate-400 text-xs uppercase">atau via email</span>
                <div class="flex-grow border-t border-slate-200"></div>
            </div>

            <div>
                <label for="member_email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                    Email Pengguna
                </label>
                <input 
                    type="email" 
                    name="email" 
                    id="member_email" 
                    placeholder="nama@jara.app"
                    class="w-full rounded-md border border-slate-300 px-3 py-2 text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600"
                >
            </div>

            <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-200">
                <button 
                    type="button" 
                    onclick="closeMemberModal()"
                    class="px-3 py-1.5 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-md transition-colors cursor-pointer"
                >
                    Tutup
                </button>
                <button 
                    type="submit" 
                    class="px-3 py-1.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-md transition-colors shadow-xs cursor-pointer"
                >
                    Undang / Tambah Anggota
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openMemberModal(listId, listTitle, addMemberUrl) {
        document.getElementById('memberModalListTitle').textContent = listTitle;
        document.getElementById('memberManagerForm').action = addMemberUrl;
        document.getElementById('memberManagerModal').classList.remove('hidden');
    }
    function closeMemberModal() {
        document.getElementById('memberManagerModal').classList.add('hidden');
    }
</script>
