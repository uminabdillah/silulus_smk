<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Siswa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('students.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="nisn" class="block text-sm font-medium text-gray-700 mb-1">NISN *</label>
                                <input type="text" name="nisn" id="nisn" value="{{ old('nisn') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('nisn') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="nis_lokal" class="block text-sm font-medium text-gray-700 mb-1">NIS Lokal</label>
                                <input type="text" name="nis_lokal" id="nis_lokal" value="{{ old('nis_lokal') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('nis_lokal') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="classroom_id" class="block text-sm font-medium text-gray-700 mb-1">Kelas (dari daftar)</label>
                                <select name="classroom_id" id="classroom_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">— Pilih Kelas —</option>
                                    @foreach($classrooms as $room)
                                        <option value="{{ $room->id }}" {{ old('classroom_id') == $room->id ? 'selected' : '' }}>
                                            {{ $room->nama_kelas }}
                                            @if($room->majorProgram) ({{ $room->majorProgram->nama_program }}) @endif
                                        </option>
                                    @endforeach
                                </select>
                                @if($classrooms->isEmpty())
                                    <p class="text-xs text-amber-600 mt-1">⚠ Belum ada kelas. <a href="{{ route('classrooms.index') }}" class="underline font-bold">Tambah kelas dulu</a>.</p>
                                @endif
                                @error('classroom_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="kelas" class="block text-sm font-medium text-gray-700 mb-1">Label Kelas (teks bebas)</label>
                                <input type="text" name="kelas" id="kelas" value="{{ old('kelas') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="misal: XII TKJ 1">
                                @error('kelas') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap *</label>
                                <input type="text" name="nama_lengkap" id="nama_lengkap" value="{{ old('nama_lengkap') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('nama_lengkap') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="tempat_lahir" class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir *</label>
                                <input type="text" name="tempat_lahir" id="tempat_lahir" value="{{ old('tempat_lahir') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('tempat_lahir') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir *</label>
                                <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('tanggal_lahir') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- === JURUSAN SECTION === --}}
                            @if($programs->isNotEmpty())
                            <div class="md:col-span-2 bg-blue-50 border border-blue-200 rounded-xl p-4 space-y-4">
                                <p class="text-[10px] font-black text-blue-700 uppercase tracking-widest">Jurusan & Konsentrasi</p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Program / Jurusan</label>
                                        <select name="major_program_id" id="programSelect_create"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            onchange="loadConcentrationsCreate(this.value)">
                                            <option value="">-- Pilih Program / Jurusan --</option>
                                            @foreach($programs as $prog)
                                                <option value="{{ $prog->id }}" {{ old('major_program_id') == $prog->id ? 'selected' : '' }}>
                                                    {{ $prog->nama_program }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Konsentrasi (Opsional)</label>
                                        <select name="major_concentration_id" id="concentrationSelect_create"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">-- Pilih Jurusan dulu --</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <div>
                                <label for="status_lulus" class="block text-sm font-medium text-gray-700 mb-1">Status Lulus *</label>
                                <select name="status_lulus" id="status_lulus" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="0" {{ old('status_lulus') == '0' ? 'selected' : '' }}>Belum / Tidak Lulus</option>
                                    <option value="1" {{ old('status_lulus') == '1' ? 'selected' : '' }}>Lulus</option>
                                </select>
                                @error('status_lulus') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-end">
                            <a href="{{ route('students.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Simpan Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    // Auto-fill kelas label when classroom is selected
    const classroomData = @json($classrooms->map(fn($c) => ['id' => $c->id, 'nama_kelas' => $c->nama_kelas]));

    document.getElementById('classroom_id')?.addEventListener('change', function () {
        const selected = classroomData.find(c => c.id == this.value);
        const kelasInput = document.getElementById('kelas');
        if (kelasInput && selected) kelasInput.value = selected.nama_kelas;
        else if (kelasInput) kelasInput.value = '';
    });

    function loadConcentrationsCreate(programId) {
        const select = document.getElementById('concentrationSelect_create');
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
    </script>
    @endpush
</x-app-layout>
