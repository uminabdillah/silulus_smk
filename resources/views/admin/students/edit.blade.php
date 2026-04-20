<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Siswa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('students.update', $student->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="nisn" class="block text-sm font-medium text-gray-700 mb-1">NISN *</label>
                                <input type="text" name="nisn" id="nisn" value="{{ old('nisn', $student->nisn) }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('nisn') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="nis_lokal" class="block text-sm font-medium text-gray-700 mb-1">NIS Lokal</label>
                                <input type="text" name="nis_lokal" id="nis_lokal" value="{{ old('nis_lokal', $student->nis_lokal) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('nis_lokal') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="classroom_id" class="block text-sm font-medium text-gray-700 mb-1">Kelas (dari daftar)</label>
                                <select name="classroom_id" id="classroom_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">— Tidak Assign ke Kelas —</option>
                                    @foreach($classrooms as $room)
                                        <option value="{{ $room->id }}" {{ old('classroom_id', $student->classroom_id) == $room->id ? 'selected' : '' }}>
                                            {{ $room->nama_kelas }}
                                            @if($room->majorProgram) ({{ $room->majorProgram->nama_program }}) @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('classroom_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="kelas" class="block text-sm font-medium text-gray-700 mb-1">Label Kelas (teks bebas)</label>
                                <input type="text" name="kelas" id="kelas" value="{{ old('kelas', $student->kelas) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="misal: XII TKJ 1">
                                @error('kelas') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap *</label>
                                <input type="text" name="nama_lengkap" id="nama_lengkap" value="{{ old('nama_lengkap', $student->nama_lengkap) }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('nama_lengkap') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="tempat_lahir" class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir *</label>
                                <input type="text" name="tempat_lahir" id="tempat_lahir" value="{{ old('tempat_lahir', $student->tempat_lahir) }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('tempat_lahir') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir *</label>
                                <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir', $student->tanggal_lahir) }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('tanggal_lahir') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="program_keahlian" class="block text-sm font-medium text-gray-700 mb-1">Program Keahlian</label>
                                <input type="text" name="program_keahlian" id="program_keahlian" value="{{ old('program_keahlian', $student->program_keahlian) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('program_keahlian') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="konsentrasi_keahlian" class="block text-sm font-medium text-gray-700 mb-1">Konsentrasi Keahlian</label>
                                <input type="text" name="konsentrasi_keahlian" id="konsentrasi_keahlian" value="{{ old('konsentrasi_keahlian', $student->konsentrasi_keahlian) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('konsentrasi_keahlian') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- === JURUSAN SECTION === --}}
                            @if($programs->isNotEmpty())
                            <div class="md:col-span-2 bg-blue-50 border border-blue-200 rounded-xl p-4 space-y-4">
                                <p class="text-[10px] font-black text-blue-700 uppercase tracking-widest">Jurusan & Konsentrasi</p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Program / Jurusan</label>
                                        <select name="major_program_id" id="programSelect"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            onchange="loadConcentrations(this.value)">
                                            <option value="">-- Pilih Program / Jurusan --</option>
                                            @foreach($programs as $prog)
                                                <option value="{{ $prog->id }}" {{ old('major_program_id', $student->major_program_id) == $prog->id ? 'selected' : '' }}>
                                                    {{ $prog->nama_program }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Konsentrasi (Opsional)</label>
                                        <select name="major_concentration_id" id="concentrationSelect"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">-- Pilih Jurusan dulu --</option>
                                            @if($student->major_program_id)
                                                @foreach($programs->find($student->major_program_id)?->concentrations ?? [] as $conc)
                                                    <option value="{{ $conc->id }}" {{ old('major_concentration_id', $student->major_concentration_id) == $conc->id ? 'selected' : '' }}>
                                                        {{ $conc->nama_konsentrasi }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                            @endif


                            <div>
                                <label for="status_lulus" class="block text-sm font-medium text-gray-700 mb-1">Status Lulus *</label>
                                <select name="status_lulus" id="status_lulus" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="0" {{ old('status_lulus', $student->status_lulus) == '0' ? 'selected' : '' }}>Belum / Tidak Lulus</option>
                                    <option value="1" {{ old('status_lulus', $student->status_lulus) == '1' ? 'selected' : '' }}>Lulus</option>
                                </select>
                                @error('status_lulus') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Nilai Mata Pelajaran -->
                        <div class="mt-8 pt-8 border-t border-gray-200">
                            <h3 class="text-lg font-bold text-gray-800 mb-6 uppercase tracking-wider flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                Input Nilai Mata Pelajaran
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
                                @php
                                    $subjectsA = $subjects->where('kelompok', 'A');
                                    $subjectsB = $subjects->where('kelompok', 'B');
                                @endphp

                                <!-- Kelompok A -->
                                <div class="space-y-4">
                                    <div class="bg-blue-50 px-4 py-2 rounded-lg label-premium">
                                        <span class="text-[10px] font-black text-blue-700 uppercase tracking-widest">Kelompok A (Umum)</span>
                                    </div>
                                    @foreach($subjectsA as $subject)
                                        <div class="flex items-center justify-between gap-4 p-3 bg-gray-50 rounded-xl border border-transparent hover:border-blue-200 transition-all">
                                            <label class="text-sm font-bold text-gray-700">{{ $loop->iteration }}. {{ $subject->nama_mapel }}</label>
                                            <div class="w-24">
                                                <input type="number" name="grades[{{ $subject->id }}]" 
                                                       value="{{ old('grades.' . $subject->id, $grades[$subject->id] ?? '') }}"
                                                       step="1" min="0" max="100"
                                                       class="w-full rounded-lg border-gray-200 text-center font-black text-blue-600 focus:ring-blue-500 focus:border-blue-500">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Kelompok B -->
                                <div class="space-y-4">
                                    <div class="bg-purple-50 px-4 py-2 rounded-lg label-premium">
                                        <span class="text-[10px] font-black text-purple-700 uppercase tracking-widest">Kelompok B (Kejuruan)</span>
                                    </div>
                                    @foreach($subjectsB as $subject)
                                        <div class="flex items-center justify-between gap-4 p-3 bg-gray-50 rounded-xl border border-transparent hover:border-purple-200 transition-all">
                                            <label class="text-sm font-bold text-gray-700">{{ $loop->iteration }}. {{ $subject->nama_mapel }}</label>
                                            <div class="w-24">
                                                <input type="number" name="grades[{{ $subject->id }}]" 
                                                       value="{{ old('grades.' . $subject->id, $grades[$subject->id] ?? '') }}"
                                                       step="1" min="0" max="100"
                                                       class="w-full rounded-lg border-gray-200 text-center font-black text-purple-600 focus:ring-purple-500 focus:border-purple-500">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end">
                            <a href="{{ route('students.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Update Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@push('scripts')
<script>
function loadConcentrations(programId) {
    const select = document.getElementById('concentrationSelect');
    select.innerHTML = '<option value="">Memuat...</option>';

    if (!programId) {
        select.innerHTML = '<option value="">-- Pilih Program dulu --</option>';
        return;
    }

    fetch(`/majors/programs/${programId}/concentrations`)
        .then(res => res.json())
        .then(data => {
            select.innerHTML = '<option value="">-- Pilih Konsentrasi --</option>';
            data.forEach(c => {
                select.innerHTML += `<option value="${c.id}">${c.nama_konsentrasi}</option>`;
            });
        });
}

// Auto-fill kelas label when classroom is selected
const editClassroomData = @json($classrooms->map(fn($c) => ['id' => $c->id, 'nama_kelas' => $c->nama_kelas]));

document.getElementById('classroom_id')?.addEventListener('change', function () {
    const selected = editClassroomData.find(c => c.id == this.value);
    const kelasInput = document.getElementById('kelas');
    if (kelasInput && selected) kelasInput.value = selected.nama_kelas;
    else if (kelasInput && !this.value) kelasInput.value = '';
});
</script>
@endpush
