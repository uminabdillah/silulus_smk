<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight tracking-tight uppercase">
                {{ __('Manajemen Mata Pelajaran') }}
            </h2>
            <button onclick="document.getElementById('addSubjectModal').classList.remove('hidden')" class="bg-blue-600 hover:bg-blue-700 text-white font-black py-2 px-6 rounded-lg shadow-lg hover:shadow-blue-500/30 transition-all active:scale-95 text-xs uppercase tracking-widest">
                Tambah Mapel
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-500 text-white rounded-xl shadow-lg animate-fade-in flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span class="font-bold">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-2xl border border-gray-100">
                <div class="p-0">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 uppercase tracking-widest">
                            <tr>
                                <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400">Kelompok</th>
                                <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400">Nama Mapel</th>
                                <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400">Program Keahlian</th>
                                <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400">Konsentrasi Keahlian</th>
                                <th class="px-6 py-4 text-center text-[10px] font-black text-gray-400">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($subjects as $subject)
                                <tr class="hover:bg-blue-50/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-[10px] leading-5 font-black rounded-full {{ $subject->kelompok == 'A' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                            KELOMPOK {{ $subject->kelompok }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900">{{ $subject->nama_mapel }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap uppercase tracking-tight">
                                        @if($subject->program_keahlian)
                                            <span class="text-gray-900 text-xs font-bold">{{ $subject->program_keahlian }}</span>
                                        @else
                                            <span class="text-gray-400 text-xs font-medium italic">Semua Program</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap uppercase tracking-tight">
                                        @if($subject->konsentrasi_keahlian)
                                            <span class="text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded text-[10px] font-black">{{ $subject->konsentrasi_keahlian }}</span>
                                        @else
                                            <span class="text-gray-400 text-[10px] font-medium italic">Umum</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex justify-center items-center gap-3">
                                            <button onclick="editSubject({{ $subject->id }}, '{{ $subject->nama_mapel }}', '{{ $subject->kelompok }}', '{{ $subject->program_keahlian }}', '{{ $subject->konsentrasi_keahlian }}')" class="text-blue-600 hover:text-blue-900 transition-transform active:scale-90">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                            </button>
                                            <form action="{{ route('subjects.destroy', $subject->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus mapel ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 transition-transform active:scale-90">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="text-gray-400 font-medium italic">Belum ada data mata pelajaran.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Program & Konsentrasi for JS -->
    <script>
        const programsData = @json($programs);
        
        function populateConcentrations(programSelectId, concentrationSelectId, selectedConcentration = '') {
            const programName = document.getElementById(programSelectId).value;
            const concSelect = document.getElementById(concentrationSelectId);
            
            concSelect.innerHTML = '<option value="">-- SEMUA KONSENTRASI --</option>';
            
            if (programName) {
                const program = programsData.find(p => p.nama_program === programName);
                if (program && program.concentrations) {
                    program.concentrations.forEach(c => {
                        const option = document.createElement('option');
                        option.value = c.nama_konsentrasi;
                        option.textContent = c.nama_konsentrasi;
                        if (c.nama_konsentrasi === selectedConcentration) option.selected = true;
                        concSelect.appendChild(option);
                    });
                }
            }
        }
    </script>

    <!-- Add Modal -->
    <div id="addSubjectModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden animate-slide-up">
            <div class="bg-blue-600 p-6">
                <h3 class="text-white font-black text-xl uppercase tracking-tighter">Tambah Mata Pelajaran</h3>
            </div>
            <form action="{{ route('subjects.store') }}" method="POST" class="p-8 space-y-6">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nama Mata Pelajaran</label>
                        <input type="text" name="nama_mapel" required class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 text-gray-900 font-bold focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Kelompok</label>
                        <select name="kelompok" required class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 text-gray-900 font-bold focus:ring-2 focus:ring-blue-500 transition-all">
                            <option value="A">KELOMPOK A</option>
                            <option value="B">KELOMPOK B</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Program / Jurusan</label>
                        <select name="program_keahlian" id="add_program" onchange="populateConcentrations('add_program', 'add_konsentrasi')" class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 text-gray-900 font-bold focus:ring-2 focus:ring-blue-500 transition-all uppercase text-xs">
                            <option value="">-- SEMUA PROGRAM --</option>
                            @foreach($programs as $prog)
                                <option value="{{ $prog->nama_program }}">{{ $prog->nama_program }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Konsentrasi (Opsional)</label>
                        <select name="konsentrasi_keahlian" id="add_konsentrasi" class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 text-gray-900 font-bold focus:ring-2 focus:ring-blue-500 transition-all uppercase text-xs">
                            <option value="">PILIH PROGRAM DULU</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-4 font-bold uppercase text-xs tracking-widest">
                    <button type="button" onclick="document.getElementById('addSubjectModal').classList.add('hidden')" class="px-6 py-3 text-gray-400 hover:text-gray-600">Batal</button>
                    <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-xl shadow-lg shadow-blue-500/20 active:scale-95 transition-all">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editSubjectModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden animate-slide-up">
            <div class="bg-emerald-600 p-6">
                <h3 class="text-white font-black text-xl uppercase tracking-tighter">Edit Mata Pelajaran</h3>
            </div>
            <form id="editForm" method="POST" class="p-8 space-y-6">
                @csrf @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nama Mata Pelajaran</label>
                        <input type="text" name="nama_mapel" id="edit_nama" required class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 text-gray-900 font-bold focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Kelompok</label>
                        <select name="kelompok" id="edit_kelompok" required class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 text-gray-900 font-bold focus:ring-2 focus:ring-blue-500 transition-all">
                            <option value="A">KELOMPOK A</option>
                            <option value="B">KELOMPOK B</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Program / Jurusan</label>
                        <select name="program_keahlian" id="edit_program" onchange="populateConcentrations('edit_program', 'edit_konsentrasi')" class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 text-gray-900 font-bold focus:ring-2 focus:ring-blue-500 transition-all uppercase text-xs">
                            <option value="">-- SEMUA PROGRAM --</option>
                            @foreach($programs as $prog)
                                <option value="{{ $prog->nama_program }}">{{ $prog->nama_program }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Konsentrasi (Opsional)</label>
                        <select name="konsentrasi_keahlian" id="edit_konsentrasi" class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 text-gray-900 font-bold focus:ring-2 focus:ring-blue-500 transition-all uppercase text-xs">
                            <option value="">PILIH PROGRAM DULU</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-4 font-bold uppercase text-xs tracking-widest">
                    <button type="button" onclick="document.getElementById('editSubjectModal').classList.add('hidden')" class="px-6 py-3 text-gray-400 hover:text-gray-600">Batal</button>
                    <button type="submit" class="bg-emerald-600 text-white px-8 py-3 rounded-xl shadow-lg shadow-emerald-500/20 active:scale-95 transition-all">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editSubject(id, name, kelompok, program, konsentrasi) {
            const form = document.getElementById('editForm');
            form.action = `/subjects/${id}`;
            document.getElementById('edit_nama').value = name;
            document.getElementById('edit_kelompok').value = kelompok;
            document.getElementById('edit_program').value = program || '';
            
            // Populasikan terlebih dahulu list konsentrasinya, lalu set nilainya
            populateConcentrations('edit_program', 'edit_konsentrasi', konsentrasi || '');
            
            document.getElementById('editSubjectModal').classList.remove('hidden');
        }
    </script>
</x-app-layout>
