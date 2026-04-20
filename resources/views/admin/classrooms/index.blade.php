<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-black text-2xl text-gray-800 tracking-tight uppercase">Manajemen Kelas</h2>
                <p class="text-xs text-gray-400 mt-1">
                    Tahun Ajaran Aktif:
                    <span class="font-bold text-blue-600">{{ $activeYear?->tahun_ajaran ?? '— Belum ada —' }}</span>
                </p>
            </div>
            @if($activeYear)
            <button onclick="document.getElementById('addClassModal').classList.remove('hidden')"
                class="bg-blue-600 hover:bg-blue-700 text-white font-black py-2 px-6 rounded-xl text-xs uppercase tracking-widest shadow-lg active:scale-95 transition-all">
                + Tambah Kelas
            </button>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="p-4 bg-emerald-500 text-white rounded-xl shadow flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span class="font-bold text-sm">{{ session('success') }}</span>
                </div>
            @endif

            @if(!$activeYear)
                <div class="p-6 bg-amber-50 border border-amber-200 rounded-2xl text-center">
                    <p class="text-amber-700 font-bold text-sm">⚠️ Belum ada tahun ajaran aktif. Aktifkan dahulu di menu <a href="{{ route('settings.index') }}" class="underline">Pengaturan</a>.</p>
                </div>
            @else
                {{-- CLASS LIST TABLE --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="text-left px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">#</th>
                                <th class="text-left px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Nama Kelas</th>
                                <th class="text-left px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Program / Konsentrasi</th>
                                <th class="text-center px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Jumlah Siswa</th>
                                <th class="text-right px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($classrooms as $i => $kelas)
                            <tr class="hover:bg-blue-50/50 transition-colors group">
                                <td class="px-6 py-4 text-gray-400 font-bold text-xs">{{ $i + 1 }}</td>
                                <td class="px-6 py-4">
                                    <span class="font-black text-gray-800 text-base">{{ $kelas->nama_kelas }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($kelas->majorProgram)
                                        <span class="inline-flex items-center gap-1">
                                            <span class="font-bold text-blue-700">{{ $kelas->majorProgram->nama_program }}</span>
                                            @if($kelas->majorConcentration)
                                                <span class="text-gray-400">›</span>
                                                <span class="text-purple-600 font-semibold">{{ $kelas->majorConcentration->nama_konsentrasi }}</span>
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-gray-300 text-xs italic">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-sm font-black
                                        {{ $kelas->students->count() > 0 ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-400' }}">
                                        {{ $kelas->students->count() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        {{-- Input Nilai Massal --}}
                                        <a href="{{ route('classrooms.grades.index', $kelas->id) }}"
                                            class="inline-flex items-center gap-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-black text-[10px] uppercase tracking-wider px-3 py-2 rounded-lg active:scale-95 transition-all border border-emerald-200">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Input Nilai
                                        </a>
                                        {{-- Edit --}}
                                        <button onclick="editClass({{ $kelas->id }}, '{{ $kelas->nama_kelas }}', {{ $kelas->major_program_id ?? 'null' }}, {{ $kelas->major_concentration_id ?? 'null' }})"
                                            class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 active:scale-90 transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                        </button>
                                        {{-- Delete --}}
                                        <form method="POST" action="{{ route('classrooms.destroy', $kelas->id) }}" onsubmit="return confirm('Hapus kelas {{ $kelas->nama_kelas }}? Siswa tidak akan ikut terhapus.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 active:scale-90 transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-16 text-center">
                                    <div class="text-4xl mb-3">🏫</div>
                                    <p class="text-gray-400 font-bold">Belum ada kelas untuk tahun ajaran ini.</p>
                                    <p class="text-gray-300 text-xs mt-1">Klik tombol "+ Tambah Kelas" untuk mulai.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif

        </div>
    </div>

    {{-- ADD CLASS MODAL --}}
    <div id="addClassModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden">
            <div class="bg-blue-600 p-6">
                <h3 class="text-white font-black text-xl uppercase tracking-tighter">Tambah Kelas Baru</h3>
                <p class="text-blue-200 text-xs mt-1">Kelas akan masuk ke tahun ajaran: <b>{{ $activeYear?->tahun_ajaran }}</b></p>
            </div>
            <form action="{{ route('classrooms.store') }}" method="POST" class="p-8 space-y-5">
                @csrf
                <input type="hidden" name="academic_year_id" value="{{ $activeYear?->id }}">

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nama Kelas *</label>
                    <input type="text" name="nama_kelas" required placeholder="contoh: XII TKJ 1"
                        class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 font-bold focus:ring-2 focus:ring-blue-500">
                </div>

                @if($programs->isNotEmpty())
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Program Keahlian (opsional)</label>
                    <select name="major_program_id" id="addProgramSelect"
                        class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 font-bold focus:ring-2 focus:ring-blue-500"
                        onchange="loadAddConcentrations(this.value)">
                        <option value="">— Semua / Tidak Spesifik —</option>
                        @foreach($programs as $prog)
                            <option value="{{ $prog->id }}">{{ $prog->nama_program }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="addConcWrapper" class="hidden">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Konsentrasi (opsional)</label>
                    <select name="major_concentration_id" id="addConcSelect"
                        class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 font-bold focus:ring-2 focus:ring-blue-500">
                        <option value="">— Semua Konsentrasi —</option>
                    </select>
                </div>
                @endif

                <div class="flex justify-end gap-3 pt-2 font-bold uppercase text-xs tracking-widest">
                    <button type="button" onclick="document.getElementById('addClassModal').classList.add('hidden')" class="px-6 py-3 text-gray-400 hover:text-gray-600">Batal</button>
                    <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-xl active:scale-95 transition-all">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- EDIT CLASS MODAL --}}
    <div id="editClassModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden">
            <div class="bg-emerald-600 p-6">
                <h3 class="text-white font-black text-xl uppercase tracking-tighter">Edit Kelas</h3>
            </div>
            <form id="editClassForm" method="POST" class="p-8 space-y-5">
                @csrf @method('PUT')

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nama Kelas *</label>
                    <input type="text" name="nama_kelas" id="editClassName" required
                        class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 font-bold focus:ring-2 focus:ring-emerald-500">
                </div>

                @if($programs->isNotEmpty())
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Program Keahlian</label>
                    <select name="major_program_id" id="editProgramSelect"
                        class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 font-bold focus:ring-2 focus:ring-emerald-500"
                        onchange="loadEditConcentrations(this.value)">
                        <option value="">— Semua / Tidak Spesifik —</option>
                        @foreach($programs as $prog)
                            <option value="{{ $prog->id }}">{{ $prog->nama_program }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Konsentrasi</label>
                    <select name="major_concentration_id" id="editConcSelect"
                        class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 font-bold focus:ring-2 focus:ring-emerald-500">
                        <option value="">— Semua —</option>
                    </select>
                </div>
                @endif

                <div class="flex justify-end gap-3 pt-2 font-bold uppercase text-xs tracking-widest">
                    <button type="button" onclick="document.getElementById('editClassModal').classList.add('hidden')" class="px-6 py-3 text-gray-400 hover:text-gray-600">Batal</button>
                    <button type="submit" class="bg-emerald-600 text-white px-8 py-3 rounded-xl active:scale-95 transition-all">Update</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    // Pre-load all concentrations data from server
    const allPrograms = @json($programs);

    function loadAddConcentrations(programId) {
        const wrapper = document.getElementById('addConcWrapper');
        const select  = document.getElementById('addConcSelect');
        if (!programId) { wrapper.classList.add('hidden'); return; }
        const prog = allPrograms.find(p => p.id == programId);
        const concs = prog?.concentrations ?? [];
        if (concs.length === 0) { wrapper.classList.add('hidden'); return; }
        wrapper.classList.remove('hidden');
        select.innerHTML = '<option value="">— Semua Konsentrasi —</option>';
        concs.forEach(c => select.innerHTML += `<option value="${c.id}">${c.nama_konsentrasi}</option>`);
    }

    function loadEditConcentrations(programId) {
        const select = document.getElementById('editConcSelect');
        if (!programId) { select.innerHTML = '<option value="">— Semua —</option>'; return; }
        const prog = allPrograms.find(p => p.id == programId);
        const concs = prog?.concentrations ?? [];
        select.innerHTML = '<option value="">— Semua —</option>';
        concs.forEach(c => select.innerHTML += `<option value="${c.id}">${c.nama_konsentrasi}</option>`);
    }

    function editClass(id, nama, programId, concId) {
        document.getElementById('editClassName').value = nama;
        document.getElementById('editClassForm').action = `/classrooms/${id}`;
        const progSel = document.getElementById('editProgramSelect');
        if (progSel) {
            progSel.value = programId ?? '';
            loadEditConcentrations(programId);
            setTimeout(() => {
                const concSel = document.getElementById('editConcSelect');
                if (concSel) concSel.value = concId ?? '';
            }, 50);
        }
        document.getElementById('editClassModal').classList.remove('hidden');
    }
    </script>
    @endpush
</x-app-layout>
