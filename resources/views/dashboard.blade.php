<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Student -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 border border-gray-100 flex items-center gap-4 relative">
                    <div class="absolute top-2 right-2">
                         <span class="text-[8px] bg-blue-100 text-blue-600 px-1.5 py-0.5 rounded-full font-bold uppercase tracking-tighter">Aktif: {{ $activeYear->tahun_ajaran ?? '-' }}</span>
                    </div>
                    <div class="bg-blue-50 p-3 rounded-xl text-blue-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase">Siswa</p>
                        <h3 class="text-2xl font-black text-gray-900">{{ $totalSiswa }}</h3>
                    </div>
                </div>

                <!-- Lulus -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 border border-gray-100 flex items-center gap-4">
                    <div class="bg-emerald-50 p-3 rounded-xl text-emerald-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase">Lulus</p>
                        <h3 class="text-2xl font-black text-gray-900">{{ $lulus }}</h3>
                    </div>
                </div>

                <!-- Tidak Lulus -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 border border-gray-100 flex items-center gap-4">
                    <div class="bg-rose-50 p-3 rounded-xl text-rose-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase">Tidak Lulus</p>
                        <h3 class="text-2xl font-black text-gray-900">{{ $tidakLulus }}</h3>
                    </div>
                </div>

                <!-- Dibuka -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 border border-gray-100 flex items-center gap-4">
                    <div class="bg-indigo-50 p-3 rounded-xl text-indigo-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase">Telah Dibuka</p>
                        <h3 class="text-2xl font-black text-gray-900">{{ $dibuka }}</h3>
                    </div>
                </div>
            </div>

            <!-- Access History Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                    <h3 class="font-bold text-lg text-gray-900">Riwayat Akses Terbaru</h3>
                    <span class="text-xs bg-gray-100 px-3 py-1 rounded-full text-gray-500 font-bold uppercase tracking-widest">Tahun Ajaran: {{ $activeYear->tahun_ajaran ?? '-' }}</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50/50 text-gray-500 uppercase text-[10px] font-bold tracking-widest">
                            <tr>
                                <th class="px-6 py-4">NISN</th>
                                <th class="px-6 py-4">Nama Siswa</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Waktu Dibuka</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse ($logs as $log)
                                <tr class="hover:bg-gray-50/50 transition duration-150">
                                    <td class="px-6 py-4 font-mono font-bold text-gray-400">{{ $log->student->nisn }}</td>
                                    <td class="px-6 py-4 font-bold text-gray-900">{{ $log->student->nama_lengkap }}</td>
                                    <td class="px-6 py-4">
                                        @if($log->student->status_lulus)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-emerald-100 text-emerald-800">LULUS</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-rose-100 text-rose-800">TIDAK LULUS</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-gray-500 font-medium">
                                        {{ $log->created_at->diffForHumans() }}
                                        <span class="text-[10px] opacity-50 block">{{ $log->created_at->translatedFormat('d F Y H:i') }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                        <svg class="w-12 h-12 mx-auto mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Belum ada riwayat akses untuk tahun ajaran ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
