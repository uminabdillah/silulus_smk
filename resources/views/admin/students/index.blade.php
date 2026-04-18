<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Siswa') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ 
        selectedIds: [], 
        allSelected: false,
        toggleAll() {
            if (this.allSelected) {
                this.selectedIds = [];
            } else {
                this.selectedIds = Array.from(document.querySelectorAll('.student-checkbox')).map(cb => cb.value);
            }
            this.allSelected = !this.allSelected;
        },
        submitBulk(route) {
            if (confirm('Apakah Anda yakin ingin melakukan aksi massal ini?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = route;
                
                const csrfToken = document.querySelector('meta[name=&quot;csrf-token&quot;]').getAttribute('content');
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;
                form.appendChild(csrfInput);

                this.selectedIds.forEach(id => {
                    const idInput = document.createElement('input');
                    idInput.type = 'hidden';
                    idInput.name = 'ids[]';
                    idInput.value = id;
                    form.appendChild(idInput);
                });

                document.body.appendChild(form);
                form.submit();
            }
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @php $isReadOnly = $selectedYear && !$selectedYear->is_active; @endphp

            <!-- Bulk Action Bar -->
            <div x-show="selectedIds.length > 0 && !{{ $isReadOnly ? 'true' : 'false' }}" class="mb-4">
                <div class="flex items-center gap-4">
                    <span class="font-bold whitespace-nowrap"><span x-text="selectedIds.length"></span> Siswa Terpilih</span>
                    <div class="h-6 w-px bg-blue-400"></div>
                    <div class="flex gap-2">
                        <button @click="submitBulk('{{ route('students.bulk_release') }}')" class="bg-white text-blue-600 px-3 py-1 rounded text-xs font-bold hover:bg-blue-50 transition flex items-center gap-1">
                            🔓 Release
                        </button>
                        <button @click="submitBulk('{{ route('students.bulk_hold') }}')" class="bg-blue-500 text-white border border-blue-400 px-3 py-1 rounded text-xs font-bold hover:bg-blue-400 transition flex items-center gap-1">
                            🔒 Hold
                        </button>
                        
                        <button @click="submitBulk('{{ route('students.bulk_delete') }}')" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs font-bold transition flex items-center gap-1">
                            🗑️ Hapus Permanen
                        </button>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 leading-none">Data Siswa</h3>
                            <p class="text-sm text-gray-500 mt-2">
                                Menampilkan <span class="font-bold text-blue-600">{{ $students->total() }}</span> siswa
                                @if($selectedYear) untuk tahun ajaran <span class="font-bold underline">{{ $selectedYear->tahun_ajaran }}</span>@endif
                            </p>
                        </div>
                        
                        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                            <!-- Per Page Selector -->
                            <form action="{{ route('students.index') }}" method="GET" class="flex items-center">
                                <input type="hidden" name="academic_year_id" value="{{ $selectedYearId }}">
                                <select name="per_page" onchange="this.form.submit()" class="rounded-xl border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500 pr-10 bg-gray-50/30">
                                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 baris</option>
                                    <option value="25" {{ request('per_page', 25) == 25 ? 'selected' : '' }}>25 baris</option>
                                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 baris</option>
                                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 baris</option>
                                </select>
                            </form>

                            <!-- Year Filter -->
                            <form action="{{ route('students.index') }}" method="GET" class="flex items-center">
                                <input type="hidden" name="per_page" value="{{ request('per_page', 25) }}">
                                <select name="academic_year_id" onchange="this.form.submit()" class="rounded-xl border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500 pr-10 bg-gray-50/30 font-bold text-blue-600">
                                    <option value="">Semua Tahun</option>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}" {{ $selectedYearId == $year->id ? 'selected' : '' }}>
                                            {{ $year->tahun_ajaran }} {{ $year->is_active ? '(Aktif)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>

                            @if(!$isReadOnly && $activeYear)
                                <button
                                    x-data=""
                                    x-on:click.prevent="$dispatch('open-modal', 'import-modal')"
                                    class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-2.5 px-5 rounded-xl text-sm flex items-center gap-2 shadow-sm shadow-emerald-100 transition active:scale-95"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                      <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                    </svg>
                                    Import
                                </button>
                                <a href="{{ route('students.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-5 rounded-xl text-sm flex items-center gap-2 shadow-sm shadow-blue-100 transition active:scale-95">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    Tambah Siswa
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="overflow-x-auto border border-gray-50 rounded-2xl overflow-hidden shadow-sm bg-gray-50/20">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead>
                                <tr class="bg-gray-50/80">
                                    <th class="px-6 py-4 text-center w-10">
                                        <input type="checkbox" @click="toggleAll()" :checked="allSelected" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 transition cursor-pointer">
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">No</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">NISN</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Nama</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Keahlian</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Status</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-widest">Akses</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-widest">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-50">
                                @forelse ($students as $key => $student)
                                    <tr class="group hover:bg-blue-50/30 transition-colors" :class="selectedIds.includes('{{ $student->id }}') ? 'bg-blue-50 shadow-inner' : ''">
                                        <td class="px-6 py-4 text-center">
                                            <input type="checkbox" value="{{ $student->id }}" x-model="selectedIds" class="student-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500 transition cursor-pointer">
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-400 border-none">{{ $students->firstItem() + $key }}</td>
                                        <td class="px-6 py-4 text-sm font-bold text-gray-500 border-none">{{ $student->nisn }}</td>
                                        <td class="px-6 py-4 text-sm font-bold text-gray-900 border-none">{{ $student->nama_lengkap }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500 border-none">
                                            <div class="font-bold text-gray-700">{{ $student->program_keahlian ?? '-' }}</div>
                                            <div class="text-xs text-gray-400">{{ $student->konsentrasi_keahlian ?? '-' }}</div>
                                        </td>
                                        <td class="px-6 py-4 border-none">
                                            @if($student->status_lulus)
                                                <span class="inline-flex items-center px-2.5 py-1 rounded text-[10px] font-black bg-emerald-100 text-emerald-800">
                                                    LULUS
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-1 rounded text-[10px] font-black bg-rose-100 text-rose-800">
                                                    TIDAK LULUS
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center border-none">
                                            @if(!$isReadOnly)
                                            <form action="{{ route('students.toggle_release', $student->id) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="inline-flex items-center px-3 py-1 rounded text-[10px] font-black tracking-wider uppercase transition-all duration-200 border-2 {{ $student->is_released ? 'border-emerald-100 bg-emerald-50 text-emerald-600 hover:bg-emerald-100' : 'border-rose-100 bg-rose-50 text-rose-600 hover:bg-rose-100' }}">
                                                    {{ $student->is_released ? 'RELEASED' : 'HOLD' }}
                                                </button>
                                            </form>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 rounded text-[10px] font-black tracking-wider uppercase border-2 {{ $student->is_released ? 'border-emerald-50 text-emerald-400' : 'border-rose-50 text-rose-400' }}">
                                                    {{ $student->is_released ? 'RELEASED' : 'HOLD' }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center whitespace-nowrap border-none">
                                            <div class="flex items-center justify-center gap-2">
                                                @if($isReadOnly)
                                                <!-- Cetak -->
                                                <a href="{{ route('students.download_skl', $student->id) }}" 
                                                   class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-xl transition-all border border-transparent hover:border-emerald-100 shadow-sm bg-gray-50/50 hover:shadow-emerald-100 active:scale-90"
                                                   title="Cetak SKL">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.821l.827-1.423a3.75 3.75 0 116.142 0l.827 1.423M16.5 9.75V10.5m-9 0v-.75m9 3v.75m-9 0v-.75m9 3v.75m-9 0v-.75M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                                    </svg>
                                                </a>
                                                @endif
                                                @if(!$isReadOnly)
                                                    <!-- Edit -->
                                                    <a href="{{ route('students.edit', $student->id) }}" 
                                                       class="p-2 text-blue-600 hover:bg-blue-50 rounded-xl transition-all border border-transparent hover:border-blue-100 shadow-sm bg-gray-50/50 hover:shadow-blue-100 active:scale-90"
                                                       title="Edit Data">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                        </svg>
                                                    </a>
                                                    
                                                    <!-- Hapus -->
                                                    <form action="{{ route('students.destroy', $student->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="p-2 text-rose-600 hover:bg-rose-50 rounded-xl transition-all border border-transparent hover:border-rose-100 shadow-sm bg-gray-50/50 hover:shadow-rose-100 active:scale-90"
                                                                title="Hapus Data">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="py-12 px-4 text-center text-gray-400 bg-gray-50/30">
                                            <div class="flex flex-col items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-gray-200">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                                </svg>
                                                <p class="font-medium">Belum ada data siswa ditemukan.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $students->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-modal name="import-modal" :show="$errors->has('file')" focusable>
        <form action="{{ route('students.import') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Import Data Siswa dari Excel') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('Unggah file Excel (.xlsx, .xls) atau CSV yang berisi data siswa.') }}
            </p>
            
            <div class="mt-4">
                <a href="{{ route('students.template') }}" class="text-sm text-blue-600 hover:text-blue-900 underline flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    Download Template Excel
                </a>
            </div>

            <div class="mt-6">
                <x-input-label for="file" value="{{ __('File Excel') }}" class="sr-only" />
                <input type="file" name="file" id="file" accept=".xlsx, .xls, .csv" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 border border-gray-300 rounded cursor-pointer mt-1 focus:ring-indigo-500 focus:border-indigo-500">
                <x-input-error :messages="$errors->get('file')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Batal') }}
                </x-secondary-button>

                <button type="submit" class="ms-3 inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Import Data') }}
                </button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
