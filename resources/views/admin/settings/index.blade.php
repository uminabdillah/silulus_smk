<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pengaturan Kelulusan & Tahun Ajaran') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
                <!-- Form Section -->
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Tambah Pengaturan Baru') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ __('Setiap tahun, Anda perlu mengatur tanggal dan konfigurasi SKL terbaru.') }}
                            </p>
                        </header>

                        <form method="post" action="{{ route('settings.store') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
                            @csrf

                            <div>
                                <x-input-label for="tahun_ajaran" :value="__('Tahun Ajaran')" />
                                <x-text-input id="tahun_ajaran" name="tahun_ajaran" type="text" class="mt-1 block w-full" placeholder="Misal: 2025/2026" required />
                                <x-input-error class="mt-2" :messages="$errors->get('tahun_ajaran')" />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="tanggal_pleno" :value="__('Tanggal Pleno')" />
                                    <x-text-input id="tanggal_pleno" name="tanggal_pleno" type="date" class="mt-1 block w-full" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('tanggal_pleno')" />
                                </div>

                                <div>
                                    <x-input-label for="tanggal_kelulusan" :value="__('Tanggal Kelulusan')" />
                                    <x-text-input id="tanggal_kelulusan" name="tanggal_kelulusan" type="date" class="mt-1 block w-full" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('tanggal_kelulusan')" />
                                </div>
                            </div>

                            <div>
                                <x-input-label for="tempat" :value="__('Tempat Tanda Tangan')" />
                                <x-text-input id="tempat" name="tempat" type="text" class="mt-1 block w-full" placeholder="Misal: Brebes" required />
                                <x-input-error class="mt-2" :messages="$errors->get('tempat')" />
                            </div>

                            <div>
                                <x-input-label for="nomor_skl_template" :value="__('Nomor Surat SKL (opsional)')" />
                                <x-text-input id="nomor_skl_template" name="nomor_skl_template" type="text" class="mt-1 block w-full" placeholder="Misal: 421.5/{nomor_urut}/SKL-SMK/VI/2026" />
                                <p class="mt-1 text-xs text-blue-600">Gunakan tag <b>{nomor_urut}</b> untuk penomoran otomatis.</p>
                                <x-input-error class="mt-2" :messages="$errors->get('nomor_skl_template')" />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-gray-100">
                                <div>
                                    <x-input-label for="announcement_start" :value="__('Waktu Buka')" />
                                    <x-text-input id="announcement_start" name="announcement_start" type="datetime-local" class="mt-1 block w-full" />
                                    <x-input-error class="mt-2" :messages="$errors->get('announcement_start')" />
                                </div>
                                <div>
                                    <x-input-label for="announcement_end" :value="__('Waktu Tutup')" />
                                    <x-text-input id="announcement_end" name="announcement_end" type="datetime-local" class="mt-1 block w-full" />
                                    <x-input-error class="mt-2" :messages="$errors->get('announcement_end')" />
                                </div>
                            </div>

                            <div>
                                <x-input-label for="hold_message" :value="__('Pesan Penangguhan (Hold)')" />
                                <textarea id="hold_message" name="hold_message" rows="2" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Misal: Mohon maaf, status kelulusan Anda ditangguhkan..."></textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('hold_message')" />
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>{{ __('Simpan Pengaturan') }}</x-primary-button>
                            </div>
                        </form>
                    </section>
                </div>

                <!-- Table Section -->
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg overflow-hidden">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Daftar Pengaturan Kelulusan') }}
                            </h2>
                        </header>

                        <div class="overflow-x-auto mt-6">
                            <table class="min-w-full bg-white border border-gray-200 text-xs border-collapse rounded-lg overflow-hidden">
                                <thead>
                                    <tr class="bg-gray-50 border-b border-gray-200">
                                        <th class="py-3 px-3 text-left font-bold text-gray-600 uppercase tracking-wider">Tahun Ajaran</th>
                                        <th class="py-3 px-3 text-center font-bold text-gray-600 uppercase tracking-wider">Status</th>
                                        <th class="py-3 px-3 text-center font-bold text-gray-600 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($academicYears as $year)
                                        <tr class="border-b hover:bg-blue-50/30 transition-colors">
                                            <td class="py-3 px-3 font-medium text-gray-900">
                                                {{ $year->tahun_ajaran }}
                                                <div class="text-[10px] text-gray-500 font-normal">Lulus: {{ $year->tanggal_kelulusan }}</div>
                                            </td>
                                            <td class="py-3 px-3 text-center">
                                                @if($year->is_active)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-green-100 text-green-800">
                                                        Aktif
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-gray-100 text-gray-600">
                                                        Nonaktif
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-3 text-center">
                                                <div class="flex items-center justify-center gap-1">
                                                    @if(!$year->is_active)
                                                    <form action="{{ route('settings.set_active', $year->id) }}" method="POST" class="inline-block">
                                                        @csrf @method('PATCH')
                                                        <button type="submit" class="p-1 px-2 bg-blue-500 hover:bg-blue-600 text-white rounded shadow-sm text-[10px] font-bold transition-all" title="Aktifkan">AKTIF</button>
                                                    </form>
                                                    @endif
                                                    
                                                    <a href="{{ route('settings.edit', $year->id) }}" class="p-1 px-2 bg-yellow-400 hover:bg-yellow-500 text-gray-900 rounded shadow-sm text-[10px] font-bold transition-all">EDIT</a>
                                                    
                                                    <form action="{{ route('settings.destroy', $year->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus data tahun ajaran ini?');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="p-1 px-2 bg-red-500 hover:bg-red-600 text-white rounded shadow-sm text-[10px] font-bold transition-all">HAPUS</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-center py-8 text-gray-400 italic">Belum ada data pengaturan kelulusan.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
