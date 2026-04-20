<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('classrooms.index') }}"
                class="p-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 transition-all active:scale-90">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h2 class="font-black text-2xl text-gray-800 tracking-tight uppercase">Input Nilai — {{ $classroom->nama_kelas }}</h2>
                <p class="text-xs text-gray-400 mt-0.5">
                    {{ $classroom->majorProgram?->nama_program ?? '' }}
                    @if($classroom->majorConcentration)
                        <span class="text-gray-300 mx-1">›</span>{{ $classroom->majorConcentration->nama_konsentrasi }}
                    @endif
                    &nbsp;·&nbsp; {{ $classroom->students->count() }} siswa
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-500 text-white rounded-xl shadow flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span class="font-bold text-sm">{{ session('success') }}</span>
                </div>
            @endif

            @if($classroom->students->isEmpty())
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-16 text-center">
                    <div class="text-5xl mb-4">👥</div>
                    <h3 class="text-gray-600 font-bold text-lg mb-2">Belum Ada Siswa di Kelas Ini</h3>
                    <p class="text-gray-400 text-sm mb-4">Tambahkan siswa dan assign ke kelas <b>{{ $classroom->nama_kelas }}</b> dari menu <a href="{{ route('students.index') }}" class="text-blue-600 underline">Data Siswa</a>.</p>
                </div>
            @elseif($subjects->isEmpty())
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-16 text-center">
                    <div class="text-5xl mb-4">📚</div>
                    <h3 class="text-gray-600 font-bold text-lg mb-2">Belum Ada Mata Pelajaran</h3>
                    <p class="text-gray-400 text-sm">Tambahkan mata pelajaran di menu <a href="{{ route('subjects.index') }}" class="text-blue-600 underline">Data Mapel</a> terlebih dahulu.</p>
                </div>
            @else
            <form action="{{ route('classrooms.grades.save', $classroom->id) }}" method="POST" id="gradeForm">
                @csrf
                {{-- Sticky top bar --}}
                <div class="sticky top-0 z-30 flex flex-wrap items-center justify-between gap-4 bg-white/90 backdrop-blur border-b border-gray-200 px-4 py-3 -mx-4 mb-4 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
                    <div class="text-xs text-gray-500 font-bold">
                        <span class="text-blue-600">{{ $subjects->count() }}</span> mapel &nbsp;·&nbsp;
                        <span class="text-emerald-600">{{ $classroom->students->count() }}</span> siswa
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <a href="{{ route('classrooms.grades.export', $classroom->id) }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 bg-blue-50 border border-blue-200 py-2.5 px-4 rounded-xl hover:bg-blue-100 transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Download Format
                        </a>
                        <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')" class="text-xs font-bold text-emerald-600 hover:text-emerald-800 bg-emerald-50 border border-emerald-200 py-2.5 px-4 rounded-xl hover:bg-emerald-100 transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            Upload Nilai
                        </button>
                        <div class="h-6 border-l border-gray-300 mx-1"></div>
                        <button type="button" onclick="clearAll()"
                            class="text-xs font-bold text-gray-400 hover:text-red-500 transition-colors px-3 py-2 rounded-lg hover:bg-red-50">
                            Kosongkan Form
                        </button>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-black py-2.5 px-8 rounded-xl text-xs uppercase tracking-widest shadow-lg active:scale-95 transition-all">
                            💾 Simpan Perubahan Form
                        </button>
                    </div>
                </div>

                {{-- SPREADSHEET GRID --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-auto">
                    <table class="w-full text-xs border-collapse" id="gradeTable">
                        <thead>
                            {{-- Subject group headers --}}
                            @php
                                $subjectsA = $subjects->where('kelompok', 'A');
                                $subjectsB = $subjects->where('kelompok', 'B');
                                $subjectsOther = $subjects->whereNotIn('kelompok', ['A', 'B']);
                            @endphp
                            <tr class="border-b border-gray-200">
                                <th rowspan="2" class="sticky left-0 z-20 bg-gray-800 text-white px-4 py-3 text-left text-[10px] font-black uppercase tracking-widest min-w-[220px] border-r border-gray-600">
                                    Nama Siswa
                                </th>
                                @if($subjectsA->count())
                                <th colspan="{{ $subjectsA->count() }}" class="bg-blue-600 text-white px-3 py-2 text-center text-[10px] font-black uppercase tracking-widest border-x border-blue-700">
                                    Kelompok A — Umum
                                </th>
                                @endif
                                @if($subjectsB->count())
                                <th colspan="{{ $subjectsB->count() }}" class="bg-purple-700 text-white px-3 py-2 text-center text-[10px] font-black uppercase tracking-widest border-x border-purple-800">
                                    Kelompok B — Kejuruan
                                </th>
                                @endif
                                @if($subjectsOther->count())
                                <th colspan="{{ $subjectsOther->count() }}" class="bg-gray-600 text-white px-3 py-2 text-center text-[10px] font-black uppercase tracking-widest border-x border-gray-700">
                                    Lainnya
                                </th>
                                @endif
                                <th rowspan="2" class="bg-emerald-700 text-white px-4 py-3 text-center text-[10px] font-black uppercase tracking-widest min-w-[80px] border-l border-emerald-800">
                                    Rata²
                                </th>
                            </tr>
                            {{-- Subject name headers --}}
                            <tr class="border-b-2 border-gray-300">
                                @foreach($subjects as $subject)
                                <th class="px-2 py-3 text-center font-black text-gray-600 border-x border-gray-200 min-w-[72px] max-w-[90px]
                                    {{ $subject->kelompok === 'A' ? 'bg-blue-50' : ($subject->kelompok === 'B' ? 'bg-purple-50' : 'bg-gray-50') }}">
                                    <div class="text-[9px] leading-tight" title="{{ $subject->nama_mapel }}">
                                        {{ Str::limit($subject->nama_mapel, 14) }}
                                    </div>
                                </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($classroom->students as $idx => $student)
                            <tr class="hover:bg-yellow-50/60 transition-colors {{ $idx % 2 === 0 ? 'bg-white' : 'bg-gray-50/50' }}" data-student="{{ $student->id }}">
                                {{-- Frozen student name column --}}
                                <td class="sticky left-0 z-10 bg-white border-r border-gray-200 px-4 py-2 {{ $idx % 2 !== 0 ? 'bg-gray-50' : '' }}">
                                    <div class="font-bold text-gray-800 text-xs">{{ $student->nama_lengkap }}</div>
                                    <div class="text-gray-400 text-[10px]">{{ $student->nisn }}</div>
                                </td>
                                {{-- Grade inputs --}}
                                @foreach($subjects as $subject)
                                <td class="border-x border-gray-100 p-1 text-center
                                    {{ $subject->kelompok === 'A' ? 'bg-blue-50/30' : ($subject->kelompok === 'B' ? 'bg-purple-50/30' : '') }}">
                                    <input
                                        type="number"
                                        name="grades[{{ $student->id }}][{{ $subject->id }}]"
                                        value="{{ $allGrades[$student->id][$subject->id] ?? '' }}"
                                        min="0" max="100" step="1"
                                        placeholder="—"
                                        data-row="{{ $idx }}"
                                        data-col="{{ $loop->index }}"
                                        class="grade-input w-full text-center text-xs font-black rounded-lg border-0 bg-transparent focus:bg-white focus:ring-2 focus:ring-blue-400 focus:outline-none py-1.5 px-1 hover:bg-white/80 transition-all
                                            {{ ($allGrades[$student->id][$subject->id] ?? null) !== null ? 'text-blue-700' : 'text-gray-300' }}"
                                        onchange="updateRowAverage({{ $idx }})"
                                        onkeydown="handleArrowKeys(event)"
                                    >
                                </td>
                                @endforeach
                                {{-- Row average --}}
                                <td class="border-l border-gray-200 px-2 py-2 text-center bg-emerald-50/50">
                                    <span id="avg-{{ $idx }}" class="font-black text-emerald-700 text-xs">—</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Bottom save button --}}
                <div class="mt-6 flex justify-end">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-black py-3 px-10 rounded-xl text-sm uppercase tracking-widest shadow-xl active:scale-95 transition-all">
                        💾 Simpan Semua Nilai
                    </button>
                </div>
            </form>
            @endif
        </div>
    </div>

    <!-- Import Modal -->
    <div id="importModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden animate-slide-up">
            <div class="bg-emerald-600 p-6">
                <h3 class="text-white font-black text-xl uppercase tracking-tighter">Upload Nilai Excel</h3>
            </div>
            <form action="{{ route('classrooms.grades.import', $classroom->id) }}" method="POST" enctype="multipart/form-data" class="p-8 pb-6">
                @csrf
                <div class="space-y-4">
                    <p class="text-xs text-gray-500 mb-6 leading-relaxed">
                        Silakan unduh format Excel terlebih dahulu, isi nilai-nilainya, lalu upload kembali file tersebut di sini. Format kolom mapel menyesuaikan dengan pengaturan mapel terbaru.
                    </p>
                    <div class="relative border-2 border-dashed border-gray-300 bg-gray-50 rounded-2xl p-6 text-center hover:bg-emerald-50 hover:border-emerald-300 transition-colors cursor-pointer" onclick="document.getElementById('file_upload').click()">
                        <div class="text-4xl mb-2">📁</div>
                        <div class="font-bold text-gray-700 text-sm mb-1" id="file_name_display">Klik untuk pilih file Excel</div>
                        <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">(.XLSX, .XLS)</div>
                        <input type="file" name="grade_file" id="file_upload" accept=".xlsx,.xls,.csv" required class="hidden" onchange="document.getElementById('file_name_display').innerText = this.files[0] ? this.files[0].name : 'Klik untuk pilih file Excel'">
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-8 font-bold uppercase text-xs tracking-widest">
                    <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="px-6 py-3 text-gray-400 hover:text-gray-600">Batal</button>
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-3 rounded-xl shadow-lg shadow-emerald-500/20 active:scale-95 transition-all">
                        Upload Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    // Calculate per-row average on load and on change
    document.addEventListener('DOMContentLoaded', function () {
        const rows = document.querySelectorAll('[data-student]');
        rows.forEach((row, idx) => updateRowAverage(idx));
    });

    function updateRowAverage(rowIdx) {
        const row = document.querySelector(`tr[data-student]:nth-child(${rowIdx + 1})`);
        if (!row) return;
        const inputs = row.querySelectorAll('.grade-input');
        let total = 0, count = 0;
        inputs.forEach(inp => {
            const v = parseFloat(inp.value);
            if (!isNaN(v)) { total += v; count++; }
            inp.classList.toggle('text-blue-700', inp.value !== '');
            inp.classList.toggle('text-gray-300', inp.value === '');
        });
        const avg = document.getElementById('avg-' + rowIdx);
        if (avg) avg.textContent = count > 0 ? (total / count).toFixed(1) : '—';
    }

    function clearAll() {
        if (!confirm('Yakin ingin mengosongkan semua nilai pada tampilan ini? (Data di database belum berubah sampai Anda simpan.)')) return;
        document.querySelectorAll('.grade-input').forEach(inp => { inp.value = ''; inp.classList.add('text-gray-300'); inp.classList.remove('text-blue-700'); });
        document.querySelectorAll('[id^="avg-"]').forEach(el => el.textContent = '—');
    }

    // Arrow key navigation between cells
    function handleArrowKeys(e) {
        if (!['ArrowUp','ArrowDown','ArrowRight','ArrowLeft','Enter','Tab'].includes(e.key)) return;

        const inputs = Array.from(document.querySelectorAll('.grade-input'));
        const idx = inputs.indexOf(e.target);
        if (idx < 0) return;

        // Figure out column count from first row
        const colCount = document.querySelectorAll('tr[data-student]:first-child .grade-input').length;

        let next = null;
        if (e.key === 'ArrowDown' || e.key === 'Enter') next = inputs[idx + colCount];
        else if (e.key === 'ArrowUp') next = inputs[idx - colCount];
        else if (e.key === 'ArrowRight') next = inputs[idx + 1];
        else if (e.key === 'ArrowLeft') next = inputs[idx - 1];
        else return;

        if (next) { e.preventDefault(); next.focus(); next.select(); }
    }
    </script>
    @endpush
</x-app-layout>
