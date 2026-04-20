<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-black text-2xl text-gray-800 tracking-tight uppercase">Manajemen Jurusan</h2>
                <p class="text-xs text-gray-500 mt-1">Program Keahlian & Konsentrasi — untuk SMK/MAK</p>
            </div>
            <button onclick="document.getElementById('addProgramModal').classList.remove('hidden')"
                class="bg-blue-600 hover:bg-blue-700 text-white font-black py-2 px-6 rounded-lg text-xs uppercase tracking-widest shadow-lg active:scale-95 transition-all">
                + Program Baru
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-green-500 text-white rounded-xl shadow-lg flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span class="font-bold text-sm">{{ session('success') }}</span>
                </div>
            @endif

            @forelse($programs as $program)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <!-- Program Header -->
                <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-8 bg-white/40 rounded-full"></div>
                        <span class="text-white font-black text-lg uppercase tracking-tight">{{ $program->nama_program }}</span>
                        <span class="px-2 py-1 bg-white/20 text-white text-[10px] font-black rounded-lg uppercase tracking-widest">
                            {{ $program->concentrations->count() }} Konsentrasi
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="editProgram({{ $program->id }}, '{{ $program->nama_program }}')"
                            class="text-white/70 hover:text-white transition p-1 active:scale-90">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                        </button>
                        <form action="{{ route('majors.programs.destroy', $program->id) }}" method="POST" onsubmit="return confirm('Hapus program ini beserta semua konsentrasinya?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-white/70 hover:text-red-300 transition p-1 active:scale-90">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Concentrations List -->
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 mb-4">
                        @foreach($program->concentrations as $conc)
                        <div class="flex items-center justify-between bg-gray-50 hover:bg-blue-50 border border-gray-200 hover:border-blue-300 rounded-xl px-4 py-3 transition-all group">
                            <span class="text-sm font-bold text-gray-800">{{ $conc->nama_konsentrasi }}</span>
                            <form action="{{ route('majors.concentrations.destroy', $conc->id) }}" method="POST" onsubmit="return confirm('Hapus konsentrasi ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-gray-300 group-hover:text-red-500 transition active:scale-90 ml-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </form>
                        </div>
                        @endforeach

                        <!-- Add Concentration inline -->
                        <button onclick="showAddConc({{ $program->id }})"
                            class="flex items-center justify-center gap-2 border-2 border-dashed border-gray-300 hover:border-blue-400 text-gray-400 hover:text-blue-500 rounded-xl px-4 py-3 transition-all font-bold text-xs uppercase tracking-widest">
                            + Tambah Konsentrasi
                        </button>
                    </div>

                    <!-- Inline Add Concentration Form (hidden by default) -->
                    <form id="addConc-{{ $program->id }}" action="{{ route('majors.concentrations.store') }}" method="POST"
                        class="hidden flex gap-3 items-center mt-2">
                        @csrf
                        <input type="hidden" name="major_program_id" value="{{ $program->id }}">
                        <input type="text" name="nama_konsentrasi" placeholder="Nama Konsentrasi..."
                            class="flex-1 rounded-xl border-gray-200 bg-gray-50 text-sm font-bold focus:ring-blue-500 focus:border-blue-500" required>
                        <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-xl text-xs font-black uppercase tracking-widest active:scale-95">Simpan</button>
                        <button type="button" onclick="hideAddConc({{ $program->id }})" class="text-gray-400 hover:text-gray-600 text-xs font-bold px-3 py-2">Batal</button>
                    </form>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-16 text-center">
                <div class="text-5xl mb-4">🎓</div>
                <h3 class="text-gray-600 font-bold text-lg mb-2">Belum Ada Program Keahlian</h3>
                <p class="text-gray-400 text-sm mb-6">Tambahkan Program Keahlian terlebih dahulu, kemudian isi Konsentrasi di dalamnya.</p>
                <button onclick="document.getElementById('addProgramModal').classList.remove('hidden')"
                    class="bg-blue-600 text-white font-black py-3 px-8 rounded-xl text-xs uppercase tracking-widest shadow-lg active:scale-95 transition-all">
                    + Tambah Program Pertama
                </button>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Add Program Modal -->
    <div id="addProgramModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="bg-blue-600 p-6">
                <h3 class="text-white font-black text-xl uppercase tracking-tighter">Tambah Program Keahlian</h3>
                <p class="text-blue-200 text-xs mt-1">Contoh: Teknologi Informasi, Kesehatan, Bisnis Manajemen</p>
            </div>
            <form action="{{ route('majors.programs.store') }}" method="POST" class="p-8 space-y-4">
                @csrf
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nama Program Keahlian</label>
                    <input type="text" name="nama_program" required placeholder="contoh: Teknologi Informasi"
                        class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 text-gray-900 font-bold focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex justify-end gap-3 pt-2 font-bold uppercase text-xs tracking-widest">
                    <button type="button" onclick="document.getElementById('addProgramModal').classList.add('hidden')" class="px-6 py-3 text-gray-400 hover:text-gray-600">Batal</button>
                    <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-xl active:scale-95 transition-all">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Program Modal -->
    <div id="editProgramModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="bg-emerald-600 p-6">
                <h3 class="text-white font-black text-xl uppercase tracking-tighter">Edit Program Keahlian</h3>
            </div>
            <form id="editProgramForm" method="POST" class="p-8 space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nama Program Keahlian</label>
                    <input type="text" name="nama_program" id="editProgramName" required
                        class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 text-gray-900 font-bold focus:ring-2 focus:ring-emerald-500">
                </div>
                <div class="flex justify-end gap-3 pt-2 font-bold uppercase text-xs tracking-widest">
                    <button type="button" onclick="document.getElementById('editProgramModal').classList.add('hidden')" class="px-6 py-3 text-gray-400 hover:text-gray-600">Batal</button>
                    <button type="submit" class="bg-emerald-600 text-white px-8 py-3 rounded-xl active:scale-95 transition-all">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showAddConc(id) {
            document.getElementById('addConc-' + id).classList.remove('hidden');
            document.getElementById('addConc-' + id).classList.add('flex');
        }
        function hideAddConc(id) {
            document.getElementById('addConc-' + id).classList.add('hidden');
            document.getElementById('addConc-' + id).classList.remove('flex');
        }
        function editProgram(id, name) {
            document.getElementById('editProgramName').value = name;
            document.getElementById('editProgramForm').action = '/majors/programs/' + id;
            document.getElementById('editProgramModal').classList.remove('hidden');
        }
    </script>
</x-app-layout>
